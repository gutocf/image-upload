<?php

namespace Gutocf\ImageUpload\Lib\Filename;

use Cake\Core\InstanceConfigTrait;
use const DS;

class PathBuilder
{

	use InstanceConfigTrait;

	protected $_defaultConfig = [];

	protected static $instances = [];

	private $baseDir;

	private $dir;

	private $filename;

	private function __construct(string $field, string $filename, string $alias, array $config)
	{
		$this->setConfig($config);
		$this->baseDir = $this->getConfig('baseDir');
		$this->replaceTokens($alias, $field, $filename);
		$this->renameIfFileExists();
	}

	public static function getInstance(string $field, string $filename, string $alias, array $config): PathBuilder
	{
		if (!isset(self::$instances[$field]) || !is_a(self::$instances[$field], PathProcessor::class)) {
			self::$instances[$field] = new PathBuilder($field, $filename, $alias, $config);
		}
		return self::$instances[$field];
	}

	private function replaceTokens(string $alias, string $field, string $filename): void
	{
		$tokenProcessor = new TokenProcessor($alias, $field, $filename);
		$this->dir = $tokenProcessor->replace($this->getConfig('dir'));
		$this->filename = $tokenProcessor->replace($this->getConfig('filename'));
	}

	private function renameIfFileExists(): void
	{
		$this->filename = pathinfo(FilenameHandler::applyNumericSuffix($this->absolutePath()), PATHINFO_BASENAME);
	}

	public function baseDir(): string
	{
		return PathProcessor::join($this->baseDir, '');
	}

	public function absolutePath(): string
	{
		return PathProcessor::join($this->baseDir(), $this->dir(), $this->filename());
	}

	public function thumbnailAbsolutePath($thumbnailDirname): string
	{
		return PathProcessor::join($this->baseDir(), $this->dir(), $thumbnailDirname, $this->filename());
	}

	public function dir(): string
	{
		return PathProcessor::join('', $this->dir, '');
	}

	public function dirname(): string
	{
		return PathProcessor::join($this->baseDir(), $this->dir(), DS);
	}

	public function filename(): string
	{
		return $this->filename;
	}
}
