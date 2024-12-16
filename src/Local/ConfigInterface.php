<?php

declare(strict_types=1);

namespace Wearesho\Yii\Filesystem\Local;

interface ConfigInterface
{
    public function getSavePath(): string;
}
