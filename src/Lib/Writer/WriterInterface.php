<?php

namespace Gutocf\ImageUpload\Lib\Writer;

use Cake\Filesystem\File;

interface WriterInterface
{

    /**
     * Writes a file to a resource
     * @param string $file Absolute file name
     * @param mixed $data File data
     * @return bool True if the file was successfully saved
     */
    public function write(string $filename, $data): bool;

    /**
     * Returns true if the file exists
     * @param string $file Absolute file name
     */
    public function exists(string $filename): bool;

    /**
     * Deletes a file from a resource
     * @param string $file Absolute file name
     */
    public function delete(string $filename): bool;
}
