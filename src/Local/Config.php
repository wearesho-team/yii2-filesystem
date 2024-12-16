<?php

declare(strict_types=1);

namespace Wearesho\Yii\Filesystem\Local;

use yii\base;

class Config extends base\BaseObject implements ConfigInterface
{
    public string $savePath = '@runtime';

    public function getSavePath(): string
    {
        return \Yii::getAlias($this->savePath);
    }
}
