<?php

namespace Gutocf\ImageUpload\Model\Behavior;

use Gutocf\ImageUpload\Lib\Filename\FilenameHandler;
use Gutocf\ImageUpload\Lib\Filename\PathBuilder;
use Gutocf\ImageUpload\Lib\Filename\PathProcessor;
use Gutocf\ImageUpload\Lib\Resizer\ImageResizer;
use Gutocf\ImageUpload\Lib\Utils\Text;
use Gutocf\ImageUpload\Lib\Validator\ImageValidator;
use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Filesystem\Folder;
use Cake\ORM\Behavior;
use Cake\Validation\Validator;
use Laminas\Diactoros\UploadedFile;
use const WWW_ROOT;

/**
 * @property ImageResizer $ImageResizer
 * @property ImageValidator $ImageValidator
 */
class ImageUploadBehavior extends Behavior {

	protected $_defaultConfigField = [
		'baseDir' => WWW_ROOT,
		'dir' => 'img',
		'filename' => '{slug}.{ext}',
		'optional' => false,
		'extensions' => ['jpg', 'png'],
		'maxSize' => null,
		'thumbnails' => [],
	];

	protected const fieldFileSuffix = 'file';

	protected const fieldRemoveSuffix = 'remove';

	protected $ImageValidator;

	protected $ImageResizer;

	public function initialize(array $config): void {
		foreach (array_keys($config) as $field) {
			if (is_int($field)) {
				$this->setConfig($config[$field], $this->_defaultConfigField);
				$this->_configDelete($field);
			} else {
				$this->setConfig($field, array_merge($this->_defaultConfigField, $config[$field]), false);
			}
		}
		$this->ImageResizer = new ImageResizer();
	}

	public function buildValidator(EventInterface $event, Validator $validator, $name) {
		if ($name == 'default') {
			$this->ImageValidator = new ImageValidator($validator);
			foreach ($this->getFieldList() as $field) {
				$config = $this->getConfig($field);
				$_field = Text::suffix($field, self::fieldFileSuffix);
				$this->ImageValidator->addRules($_field, $config);
			}
		}
	}

	public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options) {
		foreach ($this->getFieldList() as $field) {
			$this->saveOrRemoveFiles($entity, $field);
		}
	}

	public function beforeDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options) {
		foreach ($this->getFieldList() as $field) {
			$this->deleteFiles($entity, $field);
		}
	}

	protected function saveOrRemoveFiles(EntityInterface $entity, string $field) {
		if ($this->hasUploadedFile($entity, $field)) {
			$this->saveFile($entity, $field);
		} elseif ($this->isMarkedForRemoval($entity, $field)) {
			$this->removeFile($entity, $field);
		}
	}

	private function removeFile(EntityInterface $entity, string $field) {
		$field_file = Text::suffix($field, self::fieldFileSuffix);
		$field_path = Text::suffix($field, 'path');
		$this->deleteFiles($entity, $field);
		$entity->set([
			$field => null,
			$field_file => null,
			$field_path => null,
		]);
	}

	protected function isMarkedForRemoval(EntityInterface $entity, string $field): bool {
		$field_delete = Text::suffix($field, self::fieldRemoveSuffix);
		return isset($entity->$field_delete) && $entity->$field_delete;
	}

	protected function hasUploadedFile(EntityInterface $entity, string $field): bool {
		$uploadedFile = $this->getUploadFile($entity, $field);
		return $uploadedFile !== null && $uploadedFile->getError() === 0;
	}

	protected function getUploadFile(EntityInterface $entity, string $field): UploadedFile {
		$field_file = Text::suffix($field, self::fieldFileSuffix);
		return  $entity->get($field_file);
	}

	protected function saveFile(EntityInterface $entity, string $field): void {
		$field_file = Text::suffix($field, self::fieldFileSuffix);
		$uploadedFile = $entity->get($field_file);
		if (!$entity->isNew()) {
			$this->deleteFiles($entity, $field);
		}
		$pathBuilder = $this->getPathBuilder($field, $uploadedFile->getClientFilename());
		$entity->set([
			$field => $pathBuilder->filename(),
			Text::suffix($field, 'path') => $pathBuilder->dir(),
		]);
		$this->moveUploadedFile($uploadedFile, $pathBuilder->absolutePath());
		$this->createThumbnails($pathBuilder, $field);
	}

	protected function moveUploadedFile(UploadedFile $uploadedFile, string $absolutePath): void {
		FilenameHandler::applyNumericSuffix($absolutePath);
		new Folder(pathinfo($absolutePath, PATHINFO_DIRNAME), true, 775);
		$uploadedFile->moveTo($absolutePath);
	}

	protected function createThumbnails(PathBuilder $pathBuilder, string $field): void {
		$thumbnailConfig = $this->getFieldConfig($field, 'thumbnails');
		foreach ($thumbnailConfig as $thumbnailDirname => $config) {
			$config = array_merge(['width' => null, 'height' => null], $config);
			$absolutePath = $pathBuilder->absolutePath();
			$thumbnailPath = $pathBuilder->thumbnailAbsolutePath($thumbnailDirname);
			$this->ImageResizer->createThumbnail($absolutePath, $thumbnailPath, $config['width'], $config['height']);
		}
	}

	protected function deleteFiles(EntityInterface $entity, string $field): void {
		$pathField = Text::suffix($field, 'path');
		$dir = $entity->$pathField;
		$filename = $entity->$field;
		$filesToDelete = [
			PathProcessor::join($this->getFieldConfig($field, 'baseDir'), $dir, $filename)
		];
		foreach ($this->getFieldConfig($field, 'thumbnails') as $folder => $config) {
			$filesToDelete[] = PathProcessor::join($this->getFieldConfig($field, 'baseDir'), $dir, $folder, $filename);
		}
		array_filter($filesToDelete, function ($file) {
			@unlink($file);
		});
	}

	protected function getFieldList(): array {
		return array_keys($this->getConfig(null, []));
	}

	protected function getFieldConfig($field, $key, $default = null) {
		return $this->getConfig(sprintf('%s.%s', $field, $key), $default);
	}

	protected function getPathBuilder(string $field, string $filename): PathBuilder {
		return PathBuilder::getInstance(
			$field,
			$filename,
			$this->getTable()->getAlias(),
			$this->getConfig($field)
		);
	}
}
