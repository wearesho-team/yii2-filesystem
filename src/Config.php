<?php

declare(strict_types=1);

namespace Wearesho\Yii\Filesystem;

class Config implements ConfigInterface
{
    public function __construct(public string $adapter = 'local')
    {
    }

    public function getAdapter(): string
    {
        return $this->adapter;
    }
}
