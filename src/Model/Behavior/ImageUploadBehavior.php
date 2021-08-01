<?php

namespace Gutocf\ImageUpload\Model\Behavior;

use ArrayObject;
use Cake\ORM\Behavior;
use Cake\Event\EventInterface;
use Cake\Validation\Validator;
use Laminas\Diactoros\UploadedFile;
use Cake\Datasource\EntityInterface;
use Gutocf\ImageUpload\Lib\Utils\Text;
use Gutocf\ImageUpload\Lib\Filename\Filename;
use Gutocf\ImageUpload\Lib\Filename\TokenProcessor;
use Gutocf\ImageUpload\Lib\Resizer\ImageResizer;
use Gutocf\ImageUpload\Lib\Writer\DefaultWriter;
use Gutocf\ImageUpload\Lib\Writer\WriterInterface;
use Gutocf\ImageUpload\Lib\Validator\ImageValidator;
use Manager\Model\Entity\User;
use PhpParser\Node\Name\Relative;
use Psr\Http\Message\UploadedFileInterface;

/**
 * @property ImageValidator $ImageValidator
 */
class ImageUploadBehavior extends Behavior
{

	protected $_defaultFieldConfig = [
		'base_dir' => null,
		'filename' => '{img}{DS}{model}{DS}{filename}.{ext}',
		'optional' => false,
		'extensions' => ['jpg', 'png', 'webp', 'jpeg'],
		'maxSize' => null,
		'thumbnails' => [],
		'writer' => null,
	];

	protected $_defaultThumbnailConfig = [
		'filename' => '{img}{DS}{model}{DS}{thumb}{DS}{filename}.{ext}',
		'width' => 0,
		'height' => 0,
	];

	protected const FIELD_REMOVE_SUFFIX = 'remove';

	public function initialize(array $config): void
	{
		parent::initialize($config);
		$schema = $this->table()->getSchema();
		foreach ($config as $field => $config) {
			$this->setConfig($field, array_merge($this->_defaultFieldConfig, $config), false);
			$schema->setColumnType($field, 'Gutocf/ImageUpload.file');
		}
	}

	public function buildValidator(EventInterface $event, Validator $validator, $name)
	{
		if ($name === 'default') {
			$imageValidator = new ImageValidator($validator);
			foreach ($this->getFields() as $field) {
				$config = $this->getConfig($field);
				$imageValidator->addRules($field, $config);
			}
		}
	}

	public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
	{
		foreach ($this->getFields() as $field) {

			/** @var UploadedFile $uploadedFile */
			$uploadedFile = $entity->get($field);
			$entity->set($field, $entity->getOriginal($field));

			if ($this->isMarkedForDeletion($entity, $field)) {
				$this->deleteFiles($entity, $field);
				$entity->$field = null;
			}

			if (!$uploadedFile instanceof UploadedFileInterface) {
				continue;
			}

			if ($uploadedFile->getError() === UPLOAD_ERR_NO_FILE) {
				continue;
			}

			if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
				$entity->set($field, $entity->getOriginal($field));
				$entity->setError($field, 'File upload error', true);
				continue;
			}

			if (!$entity->isNew() && $entity->getOriginal($field) !== null) {
				$this->deleteFiles($entity, $field);
			}

			$base_dir = $this->getFieldConfig($field, 'base_dir');
			$relative_path = TokenProcessor::getInstance()
				->setModelAlias($this->table()->getAlias())
				->setField($field)
				->setFilename($uploadedFile->getClientFilename())
				->replace($this->getFieldConfig($field, 'filename'));
			$filename = new Filename($relative_path, $base_dir);

			$writer = $this->getWriter($field);
			while ($writer->exists($filename->getAbsolutePath())) {
				$filename->incNumericSuffix();
			}
			$writer->write($filename->getAbsolutePath(), $uploadedFile->getStream());

			$entity->set($field, $filename->getRelativePath());

			$this->processThumbnails($field, $filename, $writer);
		}
	}

	public function beforeDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options)
	{
		foreach ($this->getFields() as $field) {
			if ($entity->getOriginal($field) !== null) {
				$this->deleteFiles($entity, $field);
			}
		}
	}

	protected function processThumbnails(string $field, Filename $original, WriterInterface $writer): void
	{
		foreach ($this->getFieldConfig($field, 'thumbnails', []) as $prefix => $config) {

			$base_dir = $this->getFieldConfig($field, 'base_dir');
			$relative_path = TokenProcessor::getInstance()
				->setModelAlias($this->table()->getAlias())
				->setField($field)
				->setFilename($original->getBasename())
				->setReplacement('thumb', $prefix)
				->replace($config['filename']);
			$thumb_filename = new Filename($relative_path, $base_dir);

			$writer->write(
				$thumb_filename->getAbsolutePath(),
				ImageResizer::getInstance()
					->createThumbnail(
						$original->getAbsolutePath(),
						$thumb_filename->getExtension(),
						$config['width'],
						$config['height']
					)
			);
		}
	}

	protected function getWriter($field): WriterInterface
	{
		$writer_class = $this->getFieldConfig($field, 'writer', DefaultWriter::class);
		return new $writer_class;
	}

	protected function isMarkedForDeletion(EntityInterface $entity, string $field): bool
	{
		$field_delete = Text::suffix($field, self::FIELD_REMOVE_SUFFIX);
		return isset($entity->$field_delete) && $entity->$field_delete;
	}

	protected function deleteFiles(EntityInterface $entity, string $field): void
	{
		$base_dir = $this->getFieldConfig($field, 'base_dir');
		$relative_path = $entity->getOriginal($field);
		$filename = new Filename($relative_path, $base_dir);

		$filesToDelete = [$filename->getAbsolutePath()];

		TokenProcessor::getInstance()
			->setModelAlias($this->table()->getAlias())
			->setField($field)
			->setFilename($filename->getBasename());

		foreach ($this->getFieldConfig($field, 'thumbnails', []) as $thumb => $config) {
			$relative_path = TokenProcessor::getInstance()
				->setReplacement('thumb', $thumb)
				->replace($config['filename']);
			$filename = new Filename($relative_path, $base_dir);
			$filesToDelete[] = $filename->getAbsolutePath();
		}

		$writer = $this->getWriter($field);
		collection($filesToDelete)->each(function ($file) use ($writer) {
			$writer->delete($file);
		});
	}

	/**
	 * Returns the fields names collection
	 */
	protected function getFields(): array
	{
		return array_keys($this->getConfig(null, []));
	}

	/**
	 * Returns de configuration array for a field
	 */
	protected function getFieldConfig($field, $key, $default = null)
	{
		return $this->getConfig(sprintf('%s.%s', $field, $key), $default);
	}
}
