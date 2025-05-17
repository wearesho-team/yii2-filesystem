<?php

declare(strict_types=1);

namespace Wearesho\Yii\Filesystem\FileExport;

use League\Flysystem\FilesystemException;

class Exception extends \RuntimeException implements FilesystemException
{
}
