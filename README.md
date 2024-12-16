# Yii2 Filesystem
[![Test & Lint](https://github.com/wearesho-team/yii2-filesystem/actions/workflows/php.yml/badge.svg?branch=master)](https://github.com/wearesho-team/yii2-filesystem/actions/workflows/php.yml)
[![Latest Stable Version](https://poser.pugx.org/wearesho-team/yii2-filesystem/v/stable)](https://packagist.org/packages/wearesho-team/yii2-filesystem)
[![Total Downloads](https://poser.pugx.org/wearesho-team/yii2-filesystem/downloads)](https://packagist.org/packages/wearesho-team/yii2-filesystem)
[![codecov](https://codecov.io/gh/wearesho-team/yii2-filesystem/branch/master/graph/badge.svg)](https://codecov.io/gh/wearesho-team/yii2-filesystem)
[![License](https://poser.pugx.org/wearesho-team/yii2-filesystem/license)](https://packagist.org/packages/wearesho-team/yii2-filesystem)

Integration of [league/flysystem](https://github.com/thephpleague/flysystem) for Yii2.

It is configured by environment variables by default.
Available integration out-of-box:
- [Local](./src/Local) - should be used for development
- [S3](./src/S3) - should be used in production

## Configuring
By-default configuring available using environment variables.
To choose which integration to use you need to configure 
**FILESYSTEM_ADAPTER** variable.
Available values: *local*, *s3*. 
(or another, if you use custom bootstrap configuration)

### Configuring S3 adapter
| Variable    | Required | Default      | Description                                    |
| ----------- | -------- | ------------ | ---------------------------------------------- |
| S3_ENDPOINT | yes      |              | endpoint url (with http/https)                 |
| S3_REGION   | yes      |              | Example: *eu-central-1*                        |
| S3_BUCKET   | yes      |              | Example: *yourcompany*                         |
| S3_PREFIX   | no       | empty string | path prefix                                    |

### Configuring Local adapter
| Variable                   | Required | Default | Description                                    |
| -------------------------- | -------- | ------- | ---------------------------------------------- |
| FILESYSTEM_LOCAL_SAVE_PATH | yes      |         | path to save file on local machine             |


## Usage
### Bootstrap
To start use this package out-of-box you need to append [Bootstrap](./src/Bootstrap.php)
into your Yii2 application.
```php
<?php

// common/config/main.php or another configuration file

use Wearesho\Yii\Filesystem;

return [
    'components' => [
        // ...
    ],
    'bootstrap' => [
        'class' => Filesystem\Bootstrap::class,
        'container' => true, // if you need to configure global DI container (\Yii::$container)
        'id' => 'fs', // \Yii::$app component to be configured. Filesystem will be available using \Yii::$app->fs
    ],
];
```
*Note: for advanced usage you may customize [Bootstrap](./src/Bootstrap.php) adapters and config properties*


## TODO
- Tests

## License
[MIT](./LICENSE.md)
