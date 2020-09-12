<?php

namespace Gutocf\ImageUpload\Lib\Filename;

use const DS;
use Gutocf\ImageUpload\Lib\Utils\Text;

abstract class FilenameHandler {

	public static function applyNumericSuffix(string $absolutePath) {
		while (file_exists($absolutePath)) {
			$dirname = pathinfo($absolutePath, PATHINFO_DIRNAME);
			$filename = Text::incNumericSuffix(pathinfo($absolutePath, PATHINFO_FILENAME));
			$extension = pathinfo($absolutePath, PATHINFO_EXTENSION);
			$absolutePath = $dirname . DS . $filename . '.' . $extension;
		}
		return $absolutePath;
	}
}
