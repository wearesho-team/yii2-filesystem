<?php

declare(strict_types=1);

namespace Wearesho\Yii\Filesystem;

interface ConfigInterface
{
    public function getAdapter(): string;
}
