<?php
namespace WorkerF\Tests\Http;

use PHPUnit_Framework_TestCase;
use WorkerF\Http\Requests;

class RequestsTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $GLOBALS['HTTP_RAW_POST_DATA'] = '{"a":"test"}';
    }

    public function testRequests()
    {
        $_GET     = ['foo' => 'bar'];
        $_POST    = ['foz' => 'baz'];
        $_REQUEST = ['foo' => 'bar', 'foz' => 'baz'];
        $_SERVER  = ['server' => 'test'];
        $_COOKIE  = ['foo' => 'bar'];
        $_FILES   = ['foo' => 'bar'];
        $_FILES   = ['foo' => 'bar'];

        $request = new Requests();

        $this->assertEquals((object) $_GET, $request->get());
        $this->assertEquals((object) $_POST, $request->post());
        $this->assertEquals((object) $_REQUEST, $request->request());
        $this->assertEquals((object) $_SERVER, $request->server());
        $this->assertEquals((object) $_COOKIE, $request->cookie());
        $this->assertEquals((object) $_FILES, $request->files());
        $this->assertEquals($GLOBALS['HTTP_RAW_POST_DATA'], $request->rawData());
    }

    public function testMagicGet()
    {
        $_REQUEST = ['foo' => 'bar', 'foz' => 'baz'];

        $request = new Requests();
        $this->assertEquals('bar', $request->foo);
        $this->assertEquals('baz', $request->foz);
    }

    public function testMethod()
    {
        $_SERVER = ['REQUEST_METHOD' => 'PUT'];

        $request = new Requests();
        $this->assertEquals('PUT', $request->method());
    }

    public function testIsHttps()
    {
        $_SERVER = [];

        $request = new Requests();
        $this->assertFalse($request->isHttps());

        $_SERVER = ['HTTPS' => 'off'];

        $request = new Requests();
        $this->assertFalse($request->isHttps());

        $_SERVER = ['HTTPS' => 'on'];

        $request = new Requests();
        $this->assertTrue($request->isHttps());

        $_SERVER = ['HTTP_X_FORWARDED_PROTO' => 'http'];

        $request = new Requests();
        $this->assertFalse($request->isHttps());

        $_SERVER = ['HTTP_X_FORWARDED_PROTO' => 'https'];

        $request = new Requests();
        $this->assertTrue($request->isHttps());
    }

    public function testUrl()
    {
        $_SERVER = [
            'HTTP_HOST' => 'www.test.com',
            'REQUEST_URI' => '/p1/p2?a=2&b3',
        ];

        $request = new Requests();
        $this->assertEquals('http://www.test.com/p1/p2', $request->url());

        $_SERVER = [
            'HTTPS' => 'on',
            'HTTP_HOST' => 'www.test.com',
            'REQUEST_URI' => '/p1/p2?a=2&b3',
        ];

        $request = new Requests();
        $this->assertEquals('https://www.test.com/p1/p2', $request->url());
    }

    public function testFullUrl()
    {
        $_SERVER = [
            'HTTP_HOST' => 'www.test.com',
            'REQUEST_URI' => '/p1/p2?a=2&b3',
        ];

        $request = new Requests();
        $this->assertEquals('http://www.test.com/p1/p2?a=2&b3', $request->fullUrl());

        $_SERVER = [
            'HTTPS' => 'on',
            'HTTP_HOST' => 'www.test.com',
            'REQUEST_URI' => '/p1/p2?a=2&b3',
        ];

        $request = new Requests();
        $this->assertEquals('https://www.test.com/p1/p2?a=2&b3', $request->fullUrl());
    }

    public function testPath()
    {
        $_SERVER = [
            'REQUEST_URI' => '/p1/p2?a=2&b3',
        ];

        $request = new Requests();
        $this->assertEquals('/p1/p2', $request->path());
    }

    public function testQueryString()
    {
        $_SERVER = [
            'QUERY_STRING' => 'a=2&b3',
        ];

        $request = new Requests();
        $this->assertEquals('a=2&b3', $request->queryString());
    }

    public function testIp()
    {
        $_SERVER = [
            'REMOTE_ADDR' => '123.22.11.1',
        ];

        $request = new Requests();
        $this->assertEquals('123.22.11.1', $request->ip());

        $_SERVER['HTTP_CLIENT_IP'] = '1.2.3.4';

        $request = new Requests();
        $this->assertEquals('1.2.3.4', $request->ip());

        $_SERVER['HTTP_X_FORWARDED_FOR'] = '192.168.1.1';

        $request = new Requests();
        $this->assertEquals('192.168.1.1', $request->ip());
    }
}
