<?php
namespace WorkerF\Tests\Http;

use PHPUnit_Framework_TestCase;
use WorkerF\Http\Response;
use WorkerF\WorkerSocket\WorkerHttp;
use WorkerF\Config;
use WorkerF\Error;

class ResponseFake extends Response
{
    public static function compress($data, array $compress_conf)
    {
        return self::_compress($data, $compress_conf);
    }
}

class ResponseTest extends PHPUnit_Framework_TestCase
{
    public function testHeader()
    {
        Response::header('Content-Encoding: gzip');
        $this->assertEquals('Content-Encoding: gzip', WorkerHttp::getHeader('Content-Encoding'));
    }

    public function testHeaderMultiple()
    {
        $headers = [
            'Content-Encoding: gzip',
            'HTTP/1.1 403 Forbidden',
        ];
        Response::header($headers);
        $this->assertEquals('Content-Encoding: gzip', WorkerHttp::getHeader('Content-Encoding'));
        $this->assertEquals('HTTP/1.1 403 Forbidden', WorkerHttp::getHeader('Http-Code'));
    }

    public function testGetHeader()
    {
        Response::header('Content-Encoding: gzip');

        $this->assertEquals('Content-Encoding: gzip', Response::getHeader('Content-Encoding'));
    }

    public function testRedirect()
    {
        $redirectClosure = Response::redirect('http://www.test.com');

        $result = call_user_func($redirectClosure);

        $this->assertEquals('HTTP/1.1 302 Found', Response::getHeader('Http-Code'));
        $this->assertEquals('Location: http://www.test.com', Response::getHeader('Location'));
        $this->assertEquals('redirect', $result);
    }

    public function testCompress()
    {
        $_SERVER['HTTP_ACCEPT_ENCODING'] = 'gzip, deflate';

        // do not compress
        $conf = [
            'encoding'     => '', 
            'level'        => '5',
            'content_type' => [
                'application/json',
                'text/html',
            ],
        ];
        
        $data = ResponseFake::compress('Hello, World!', $conf);
        $this->assertEquals('Hello, World!', $data);

        // compress gzip
        $conf = [
            'encoding'     => 'gzip', 
            'level'        => '4',
            'content_type' => [
                'application/json',
                'text/html',
            ],
        ];
        
        $data = ResponseFake::compress('Hello, World!', $conf);
        $this->assertEquals('Content-Encoding: gzip', Response::getHeader('Content-Encoding'));
        $this->assertEquals(gzencode('Hello, World!', 4), $data);

        // compress deflate
        $conf = [
            'encoding'     => 'deflate', 
            'level'        => '5',
            'content_type' => [
                'application/json',
                'text/html',
            ],
        ];
        
        $data = ResponseFake::compress('Hello, World!', $conf);
        $this->assertEquals('Content-Encoding: deflate', Response::getHeader('Content-Encoding'));
        $this->assertEquals(gzdeflate('Hello, World!', 5), $data);

        // content type not allow
        $conf = [
            'encoding'     => 'gzip', 
            'level'        => '4',
            'content_type' => [
                'application/json',
                'text/html',
            ],
        ];

        Response::header('Content-Type: text/xml');
        $data = ResponseFake::compress('Hello, World!', $conf);
        $this->assertEquals('Hello, World!', $data);
    }

    public function testBuild()
    {
        $_SERVER['HTTP_ACCEPT_ENCODING'] = 'gzip, deflate';
        $conf = [];
        $conf['compress'] = [
            'encoding'     => '', // do not compress
            'level'        => '4',
            'content_type' => [
                'application/json',
                'text/html',
            ],
        ];

        // string
        $data = 'Hello world!';
        $result = Response::bulid($data, $conf);
        $this->assertEquals($data, $result);

        // array
        $data = [
            'a' => 2,
            'b' => 3,
        ];
        $result = Response::bulid($data, $conf);
        $this->assertEquals(json_encode($data), $result);

        // object
        $data = new \stdClass();
        $data->name = 'jack';
        $data->age = 18;

        $result = Response::bulid($data, $conf);
        $this->assertEquals(json_encode($data), $result);

        // Closure
        $data = function() {
            return 'something';
        };

        $result = Response::bulid($data, $conf);
        $this->assertEquals('', $result);

        // is redirect Closure
        $data = function() {
            return 'redirect';
        };

        $result = Response::bulid($data, $conf);
        $this->assertEquals('link already redirected', $result);
    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testBuildInvalidException()
    {
        $_SERVER['HTTP_ACCEPT_ENCODING'] = 'gzip, deflate';
        $conf = [];
        $conf['compress'] = [
            'encoding'     => '', // do not compress
            'level'        => '4',
            'content_type' => [
                'application/json',
                'text/html',
            ],
        ];

        $result = Response::bulid(FALSE, $conf);
    }
}
