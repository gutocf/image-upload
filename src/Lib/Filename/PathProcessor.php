<?php

namespace Gutocf\ImageUpload\Lib\Filename;

abstract class PathProcessor {

	public static function join(...$paths) {
		return self::clear(implode(DS, $paths));
	}

	private static function clear($path) {
		return preg_replace(sprintf('/\%s{2,}/', DS), DS, $path);
	}
}
