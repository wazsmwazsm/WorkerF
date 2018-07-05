<?php
namespace WorkerF\Tests\WorkerSocket;

use PHPUnit_Framework_TestCase;
use WorkerF\WorkerSocket\WorkerHttp;
use Workerman\Protocols\HttpCache;

class WorkerHttpTest extends PHPUnit_Framework_TestCase
{
    public function testHeader()
    {
        WorkerHttp::header('Content-Encoding: gzip');
        $this->assertEquals('Content-Encoding: gzip', HttpCache::$header['Content-Encoding']);

        WorkerHttp::header('HTTP/1.1 403 Forbidden');
        $this->assertEquals('HTTP/1.1 403 Forbidden', HttpCache::$header['Http-Code']);
    }

    public function testHeaderMultiple()
    {
        $headers = [
            'Content-Encoding: gzip',
            'HTTP/1.1 403 Forbidden',
        ];
        WorkerHttp::header($headers);

        $this->assertEquals('Content-Encoding: gzip', HttpCache::$header['Content-Encoding']);
        $this->assertEquals('HTTP/1.1 403 Forbidden', HttpCache::$header['Http-Code']);
    }

    public function getHeader()
    {
        $this->assertNull(WorkerHttp::getHeader('Content-Encoding'));

        WorkerHttp::header('HTTP/1.1 403 Forbidden');
        $this->assertEquals(WorkerHttp::getHeader('Http-Code'), HttpCache::$header['Http-Code']);
    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testHeaderInvalidException()
    {
        WorkerHttp::header('not-exist');
    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testHeaderInvalidException2()
    {
        $headers = [
            'Content-Encoding: gzip',
            'not-exist',
            'HTTP/1.1 403 Forbidden',
        ];
        WorkerHttp::header($headers);
    }
}
