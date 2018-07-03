<?php

namespace Wearesho\Yii\Filesystem\Tests\Replica;

/**
 * Class NonSeekableStream
 * @package Wearesho\Yii\Filesystem\Tests\Replica
 */
class NonSeekableStream
{
    public function stream_open($uri, $mode, $options, &$opened_path) // phpcs:ignore
    {
        return true;
    }

    public function stream_seek($offset, $whence = SEEK_SET) // phpcs:ignore
    {
        return false;
    }

    public function stream_eof() // phpcs:ignore
    {
        return false;
    }
}
