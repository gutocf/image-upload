<?php

declare(strict_types=1);

namespace Gutocf\ImageUpload;

use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Database\TypeFactory;
use Gutocf\ImageUpload\Database\Type\FileType;

class Plugin extends BasePlugin
{
    protected $name = 'ImageUpload';

    protected $bootstrapEnabled = true;

    public function bootstrap(PluginApplicationInterface $app): void
    {
        TypeFactory::map('Gutocf/ImageUpload.file', FileType::class);
    }
}
