# Yii Filesystem
Integration of *league/flysystem* for Bobra.
It can use Amazon S3 or local storage.

## Configuring
Amazon S3 will be used if next environment variables set:

- S3_ENDPOINT
- S3_BUCKET
- S3_KEY
- S3_SECRET
- S3_REGION
- S3_VERSION
- S3_BASE_URL

otherwise local storage will be used.
Don't forget to define alias for storage
```php
<?php
// bootstrap.php

// important to pass web folder with baseUrl equals to \Yii::$app->urlManager
\Yii::setAlias('fileStorage', 'path-to-your-web-folder'); 
```

## Usage
Configure container:
```php
<?php
// bootstrap.php

use Wearesho\Yii\Filesystem\Filesystem;
use Wearesho\Yii\Filesystem\FilesystemFactory;

\Yii::$container->set(
    Filesystem::class,
    [FilesystemFactory::class, 'instantiate']
);
```
Then use in your code
```php
<?php

use Wearesho\Yii\Filesystem\Filesystem;

/** @var Filesystem $fs */
$fs = \Yii::$container->get(Filesystem::class);
$fs->write('hello.txt', 'Hello, world!');
```

## TODO
- Tests

## License
Proprietary
Request [wearesho](https://wearesho.com) for usage.