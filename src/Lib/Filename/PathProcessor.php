<?php

namespace Gutocf\ImageUpload\Lib\Filename;

use Gutocf\ImageUpload\Lib\Utils\Text;

abstract class PathProcessor
{

	public static function getAbsolutePath(string $base_dir, string $filename): string
	{
		return self::join($base_dir, $filename);
	}

	public static function join(...$paths)
	{
		return self::clear(implode(DS, $paths));
	}

	private static function clear($path)
	{
		return preg_replace(sprintf('/\%s{2,}/', DS), DS, $path);
	}

	public static function applyNumericSuffix(string $filename)
	{
		$dirname = pathinfo($filename, PATHINFO_DIRNAME);
		$extension = pathinfo($filename, PATHINFO_EXTENSION);
		$filename = Text::incNumericSuffix(pathinfo($filename, PATHINFO_FILENAME));
		return sprintf('%s%s%s.%s', $dirname, DS, $filename, $extension);
	}
}
