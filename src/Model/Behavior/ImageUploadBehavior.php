<?php

	 namespace App\Model\Behavior;

	 use App\Lib\ImageUpload\Filename\FilenameHandler;
	 use App\Lib\ImageUpload\Filename\PathBuilder;
	 use App\Lib\ImageUpload\Filename\PathProcessor;
	 use App\Lib\ImageUpload\Resizer\ImageResizer;
	 use App\Lib\ImageUpload\Utils\Text;
	 use App\Lib\ImageUpload\Validator\ImageValidator;
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

		 private $_defaultConfigField = [
			  'baseDir' => WWW_ROOT,
			  'dir' => 'img',
			  'filename' => '{slug}.{ext}',
			  'optional' => false,
			  'extensions' => ['jpg', 'png'],
			  'maxSize' => null,
			  'thumbnails' => [],
		 ];

		 private const fieldFileSuffix = 'file';

		 private $ImageValidator;

		 private $ImageResizer;

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
				 if (!$entity->isNew()) {
					 $this->deleteFiles($entity, $field);
				 }
				 $this->saveFile($entity, $field);
			 }
		 }

		 public function beforeDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options) {
			 foreach ($this->getFieldList() as $field) {
				 $this->deleteFiles($entity, $field);
			 }
		 }

		 private function saveFile(EntityInterface $entity, string $field) {
			 $uploadedFile = $entity->get(Text::suffix($field, self::fieldFileSuffix));
			 if ($uploadedFile->getError() === 0) {
				 $pathBuilder = $this->getPathBuilder($field, $uploadedFile->getClientFilename());
				 $entity->set([
					  $field => $pathBuilder->filename(),
					  Text::suffix($field, 'path') => $pathBuilder->dir(),
				 ]);
				 $this->moveUploadedFile($uploadedFile, $pathBuilder->absolutePath());
				 $this->createThumbnails($pathBuilder, $field);
			 }
		 }

		 private function moveUploadedFile(UploadedFile $uploadedFile, string $absolutePath): void {
			 FilenameHandler::applyNumericSuffix($absolutePath);
			 new Folder(pathinfo($absolutePath, PATHINFO_DIRNAME), true, 775);
			 $uploadedFile->moveTo($absolutePath);
		 }

		 private function createThumbnails(PathBuilder $pathBuilder, string $field): void {
			 $thumbnailConfig = $this->getFieldConfig($field, 'thumbnails');
			 foreach ($thumbnailConfig as $thumbnailDirname => $config) {
				 $config = array_merge(['width' => null, 'height' => null], $config);
				 $absolutePath = $pathBuilder->absolutePath();
				 $thumbnailPath = $pathBuilder->thumbnailAbsolutePath($thumbnailDirname);
				 $this->ImageResizer->createThumbnail($absolutePath, $thumbnailPath, $config['width'], $config['height']);
			 }
		 }

		 private function deleteFiles(EntityInterface $entity, string $field) {
			 $pathField = Text::suffix($field, 'path');
			 $dir = $entity->$pathField;
			 $filename = $entity->$field;
			 $filesToDelete = [
				  PathProcessor::join($this->getFieldConfig($field, 'baseDir'), $dir, $filename)
			 ];
			 foreach ($this->getFieldConfig($field, 'thumbnails') as $folder => $config) {
				 $filesToDelete[] = PathProcessor::join($this->getFieldConfig($field, 'baseDir'), $dir, $folder, $filename);
			 }
			 array_filter($filesToDelete, function($file) {
				 @unlink($file);
			 });
		 }

		 private function getFieldList() {
			 return array_keys($this->getConfig(null, []));
		 }

		 private function getFieldConfig($field, $key, $default = null) {
			 return $this->getConfig(sprintf('%s.%s', $field, $key), $default);
		 }

		 private function getPathBuilder(string $field, string $filename): PathBuilder {
			 return PathBuilder::getInstance(
								  $field,
								  $filename,
								  $this->getTable()->getAlias(),
								  $this->getConfig($field)
			 );
		 }

	 }
	 