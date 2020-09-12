<?php

namespace Gutocf\ImageUpload\Lib\Utils;

abstract class Text {

	public static function suffix($string, $suffix, $separator = '_') {
		return sprintf('%s%s%s', $string, $separator, $suffix);
	}

	public static function incNumericSuffix($string, $separator = '-') {
		$suffix = self::getNextNumericSuffix($string, $separator);
		return self::suffix(self::clearNumericSuffix($string, $separator), $suffix, $separator);
	}

	public static function getNextNumericSuffix($string, $separator = '-') {
		$next = 0;
		$matches = [];
		$pattern = sprintf('/%s(?<suffix>\d+)$/', $separator);
		if (preg_match($pattern, $string, $matches)) {
			$next = ++$matches['suffix'];
		}
		return $next;
	}

	public static function clearNumericSuffix($string, $separator = '-') {
		$pattern = sprintf('/%s\d+$/', $separator);
		return preg_replace($pattern, '', $string);
	}
}
