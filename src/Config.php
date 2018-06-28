<?php

namespace Wearesho\Yii\Filesystem;

use yii\base;

/**
 * Class Config
 * @package Wearesho\Yii\Filesystem
 */
class Config extends base\BaseObject implements ConfigInterface
{
    /** @var string */
    public $adapter = 's3';

    public function getAdapter(): string
    {
        return $this->adapter;
    }
}
