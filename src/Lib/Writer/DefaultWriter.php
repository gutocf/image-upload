<?php

namespace Gutocf\ImageUpload\Lib\Writer;

use Cake\Filesystem\File;
use Gutocf\ImageUpload\Lib\Filename\PathProcessor;
use Search\Model\Filter\Boolean;

class DefaultWriter implements WriterInterface
{

    public function write(string $filename, $content): bool
    {
        $file = new File($filename, true, 644);
        $success = $file->write($content, 'w', true);
        $file->close();
        return $success;
    }

    public function exists(string $filename): bool
    {
        $file = new File($filename);
        $exists = $file->exists();
        $file->close();
        return $exists;
    }

    public function delete(string $filename): bool
    {
        $file = new File($filename);
        if ($file->exists()) {
            return $file->delete();
        }
        return false;
    }
}
