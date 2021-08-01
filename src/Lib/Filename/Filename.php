<?php

namespace Gutocf\ImageUpload\Lib\Filename;

class Filename
{

    protected ?string $base_dir;

    protected string $relative_path;

    public function __construct(string $relative_path, string $base_dir = null)
    {
        $this->base_dir = $base_dir;
        $this->relative_path = $relative_path;
    }

    public function getAbsolutePath(): string
    {
        return PathProcessor::join($this->base_dir, $this->relative_path);
    }

    public function getBaseDir(): ?string
    {
        return $this->base_dir;
    }

    public function getRelativePath(): string
    {
        return $this->relative_path;
    }

    public function getBasename(): string
    {
        return pathinfo($this->relative_path, PATHINFO_BASENAME);
    }

    public function getExtension(): string
    {
        return pathinfo($this->relative_path, PATHINFO_EXTENSION);
    }

    public function incNumericSuffix(): void
    {
        $this->relative_path = PathProcessor::applyNumericSuffix($this->relative_path);
    }
}
