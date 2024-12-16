<?php

declare(strict_types=1);

namespace Wearesho\Yii\Filesystem;

use League\Flysystem\FilesystemAdapter;

interface AdapterFactoryInterface
{
    public function create(): FilesystemAdapter;
}
