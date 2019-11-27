<?php

namespace Wearesho\Yii\Filesystem\Local;

use yii\base;
use yii\helpers\Url;

/**
 * Class Config
 * @package Wearesho\Yii\Filesystem\Local
 */
class Config extends base\BaseObject implements ConfigInterface
{
    /** @var string */
    public $savePath = '@runtime';

    /** @var string|null */
    public $baseUrl = null;

    public function getSavePath(): string
    {
        return \Yii::getAlias($this->savePath);
    }

    /**
     * @return string
     * @throws base\InvalidConfigException
     */
    public function getBaseUrl(): string
    {
        if (is_null($this->baseUrl)) {
            throw new base\InvalidConfigException("basePath is not configured");
        }

        return Url::to($this->baseUrl, true);
    }
}
