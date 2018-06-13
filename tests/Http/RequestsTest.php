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
}
