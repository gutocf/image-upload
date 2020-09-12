<?php

	 namespace App\Lib\ImageUpload\Validator;

	 use Cake\Core\InstanceConfigTrait;
	 use Cake\I18n\Number;
	 use Cake\Validation\Validation;
	 use Cake\Validation\Validator;

	 class ImageValidator {

		 private $validator;

		 use InstanceConfigTrait;

		 private $_defaultConfig = [];

		 public function __construct(Validator $valitador) {
			 $this->validator = $valitador;
		 }

		 public function addRules(string $field, array $config = []) {
			 $this->setConfig($config);
			 $this->addUploadedFileRule($field);
			 $this->addExtensionRule($field);
			 $this->addMaxFileSizeRule($field);
		 }

		 private function addExtensionRule(string $field) {
			 $extensions = $this->getConfig('extensions');
			 if (!empty($extensions)) {
				 $rule = ['extension', $extensions];
				 $message = sprintf('O arquivo deve ter uma das extensões a seguir: %s', implode(', ', $extensions));
				 $this->addRule($field, 'extension', $rule, $message);
			 }
		 }

		 private function addUploadedFileRule(string $field) {
			 $message = 'Selecione um arquivo';
			 $when = $this->getConfig('optional') ? Validator::WHEN_CREATE : Validator::WHEN_UPDATE;
			 $this->validator->allowEmptyFile($field, $message, $when);
		 }

		 private function addMaxFileSizeRule(string $field) {
			 $maxSize = $this->getConfig('maxSize');
			 if ($maxSize) {
				 $rule = ['fileSize', Validation::COMPARE_LESS_OR_EQUAL, $maxSize];
				 $message = sprintf('Tamanho máximo de arquivo permitido é %s', Number::toReadableSize($maxSize));
				 $this->addRule($field, 'fileSize', $rule, $message);
			 }
		 }

		 private function addRule(string $field, string $name, array $rule, string $message = null) {
			 $last = true;
			 $this->validator->add($field, $name, compact('rule', 'message', 'last'));
		 }

	 }
	 