<?php

namespace Gutocf\ImageUpload\Lib\Utils;

abstract class Text
{

	private const FIRST_SUFFIX = 0;

	public static function suffix($string, $suffix, $separator = '_')
	{
		return sprintf('%s%s%s', $string, $separator, $suffix);
	}

	public static function incNumericSuffix($string, $separator = '-')
	{
		$suffix = self::getNextNumericSuffix($string, $separator);
		return self::suffix(self::clearNumericSuffix($string, $separator), $suffix, $separator);
	}

	public static function getNextNumericSuffix($string, $separator = '-')
	{
		$matches = [];
		$pattern = sprintf('/%s(?<suffix>\d+)$/', $separator);
		if (preg_match($pattern, $string, $matches)) {
			return ++$matches['suffix'];
		}

		return self::FIRST_SUFFIX;
	}

	public static function clearNumericSuffix($string, $separator = '-')
	{
		$pattern = sprintf('/%s\d+$/', $separator);
		return preg_replace($pattern, '', $string);
	}
}
