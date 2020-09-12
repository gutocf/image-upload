<?php

namespace Gutocf\ImageUpload\Lib\Filename;

use Cake\Utility\Text;
use const DS;

class TokenProcessor {

	private $replacementMap;

	public function __construct(string $alias, string $field, string $filename) {
		$this->_initReplacements($alias, $field, $filename);
	}

	private function _initReplacements(string $alias, string $field, string $filename) {
		$this->replacementMap = [
			'{model}' => strtolower(Text::slug($alias)),
			'{field}' => $field,
			'{year}' => date('Y'),
			'{month}' => date('m'),
			'{day}' => date('d'),
			'{time}' => time(),
			'{DS}' => DS,
			'{slug}' => strtolower(Text::slug(pathinfo($filename, PATHINFO_FILENAME))),
			'{ext}' => strtolower(pathinfo($filename, PATHINFO_EXTENSION)),
		];
	}

	public function replace(string $pattern) {
		$search = array_keys($this->replacementMap);
		$replace = array_values($this->replacementMap);
		return str_replace($search, $replace, $pattern);
	}
}
