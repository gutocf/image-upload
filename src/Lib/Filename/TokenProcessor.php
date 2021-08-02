<?php

namespace Gutocf\ImageUpload\Lib\Filename;

use const DS;

use Cake\Utility\Inflector;
use Cake\Utility\Text;

/**
 * @property TokenProcessor[] $instances
 * @property string[] $replacements
 *
 * */
class TokenProcessor
{

	private static $instance;

	private $replacements;

	private function __construct()
	{
		$this->replacements = [
			'{year}' => date('Y'),
			'{month}' => date('m'),
			'{day}' => date('d'),
			'{time}' => time(),
			'{DS}' => DS,
		];
	}

	public static function getInstance(): self
	{
		if (self::$instance === null) {
			self::$instance = new TokenProcessor();
		}
		return self::$instance;
	}

	public function replace(string $pattern): string
	{
		$search = array_keys($this->replacements);
		$replace = array_values($this->replacements);
		return str_replace($search, $replace, $pattern);
	}

	public function setModelAlias(string $model_alias): self
	{
		return $this->setReplacement('model', strtolower(Inflector::dasherize($model_alias)));
	}

	public function setField(string $field): self
	{
		return $this->setReplacement('field', $field);
	}

	public function setFilename(string $filename): self
	{
		$this->setReplacement('filename', strtolower(Text::slug(pathinfo($filename, PATHINFO_FILENAME))));
		$this->setReplacement('ext', strtolower(pathinfo($filename, PATHINFO_EXTENSION)));

		return $this;
	}

	public function setReplacement(string $key, $value): self
	{
		$this->replacements[sprintf('{%s}', $key)] = $value;

		return $this;
	}
}
