<?php

	 namespace App\Lib\ImageUpload\Filename;

	 use App\Lib\ImageUpload\Utils\Text;
	 use const DS;

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
	 