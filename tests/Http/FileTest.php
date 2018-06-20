<?php

namespace WorkerF\Tests\Http;

use PHPUnit_Framework_TestCase;
use WorkerF\Http\File;


class FileTest extends PHPUnit_Framework_TestCase
{
    public static function tearDownAfterClass()
    {
        unlink(__DIR__.'/a.txt');
    }

    public function testGetInfo()
    {
        $filesArr = [
            'file_name' => 'test',
            'file_data' => 'Some test data',
            'file_size' => 24,
            'file_type' => 'text',
        ];

        $file = new File($filesArr);

        $this->assertEquals('test', $file->getFileName());
        $this->assertEquals(24, $file->getFileSize());
        $this->assertEquals('text', $file->getFileType());

    }

    public function testMove()
    {
        $filesArr = [
            'file_name' => 'test',
            'file_data' => 'Some test data',
            'file_size' => 24,
            'file_type' => 'text',
        ];

        $file = new File($filesArr);

        $file->move(__DIR__, 'a.txt');

        $this->assertTrue(file_exists(__DIR__.'/a.txt'));

        $this->assertEquals('Some test data', file_get_contents(__DIR__.'/a.txt'));
    }
}
