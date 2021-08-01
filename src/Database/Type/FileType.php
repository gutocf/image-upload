<?php

declare(strict_types=1);

namespace Gutocf\ImageUpload\Database\Type;

use Cake\Database\DriverInterface;
use Cake\Database\Type\BaseType;

class FileType extends BaseType
{

    public function marshal($value)
    {
        return $value;
    }


    public function toDatabase($value, DriverInterface $driver)
    {
        return $value;
    }

    public function toPHP($value, DriverInterface $driver)
    {
        return $value;
    }
}
