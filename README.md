# Yii2 Filesystem
[![Latest Stable Version](https://poser.pugx.org/wearesho-team/yii2-filesystem/v/stable)](https://packagist.org/packages/wearesho-team/yii2-filesystem)
[![Total Downloads](https://poser.pugx.org/wearesho-team/yii2-filesystem/downloads)](https://packagist.org/packages/wearesho-team/yii2-filesystem)
[![codecov](https://codecov.io/gh/wearesho-team/yii2-filesystem/branch/master/graph/badge.svg)](https://codecov.io/gh/wearesho-team/yii2-filesystem)
[![Build Status](https://travis-ci.org/wearesho-team/yii2-filesystem.svg?branch=master)](https://travis-ci.org/wearesho-team/yii2-filesystem)
[![License](https://poser.pugx.org/wearesho-team/yii2-filesystem/license)](https://packagist.org/packages/wearesho-team/yii2-filesystem)

Integration of [league/flysystem](https://github.com/thephpleague/flysystem) for Yii2.

It is configured by environment variables by default.
Available integration out-of-box:
- [FTP](./src/Ftp) - should be used in deprecated systems
- [Local](./src/Local) - should be used for development
- [S3](./src/S3) - should be used in production

## Configuring
By-default configuring available using environment variables.
To choose which integration to use you need to configure 
**FILESYSTEM_ADAPTER** variable.
Available values: *local*, *ftp*, *s3*. 
(or another, if you use custom bootstrap configuration)

### Configuring S3 adapter
| Variable    | Required | Default      | Description                                    |
| ----------- | -------- | ------------ | ---------------------------------------------- |
| S3_ENDPOINT | yes      |              | endpoint url (with http/https)                 |
| S3_KEY      | yes      |              | public key                                     |
| S3_SECRET   | yes      |              | secret key                                     |
| S3_VERSION  | no       | latest       |                                                |
| S3_REGION   | yes      |              | Example: *eu-central-1*                        |
| S3_BUCKET   | yes      |              | Example: *yourcompany*                         |
| S3_PREFIX   | no       | empty string | path prefix                                    |
| S3_BASE_URL | no       |              | base url will be used to generate URL to files |

### Configuring Local adapter
| Variable                   | Required | Default | Description                                    |
| -------------------------- | -------- | ------- | ---------------------------------------------- |
| FILESYSTEM_LOCAL_SAVE_PATH | yes      |         | path to save file on local machine             |
| FILESYSTEM_LOCAL_BASE_URL  | no       |         | base url will be used to generate URL to files |

### Configuring Ftp adapter
| Variable     | Required | Default      | Description                                    |
| ------------ | -------- | ------------ | ---------------------------------------------- |
| FTP_HOST     | yes      |              |                                                |
| FTP_USER     | yes      |              |                                                |
| FTP_PASS     | no       | null         | password for FTP user                          |
| FTP_PORT     | no       | 21           |                                                |
| FTP_PATH     | no       | empty string | prefix for save path                           |
| FTP_BASE_URL | no       |              | base url will be used to generate URL to files |

### Configuring Replica adapter
This adapter purpose of mirroring files on few adapters
(any [AdapterInterface](./src/AdapterInterface.php) implementation)
```php
<?php

use Wearesho\Yii\Filesystem;

$adapter = new Filesystem\Replica\Adapter([
    'master' => [
        'class' => Filesystem\S3\Adapter::class,
    ],
    'slaves' => [
        // so much slaves
        [
            'class' => Filesystem\Ftp\Adapter::class,
        ],
    ],
]);

```

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

### Filesystem Class
You can also use [Filesystem](./src/Filesystem.php) class for yii2-way configuration:
```php
<?php

use Wearesho\Yii\Filesystem\Filesystem;
use Wearesho\Yii\Filesystem\AdapterInterface;

$fs = new Filesystem([
    'adapter' => [
        'class' => AdapterInterface::class, // or another implementation, if container not configured
    ],
]);

```

## TODO
- Tests

## License
[MIT](./LICENSE.md)
