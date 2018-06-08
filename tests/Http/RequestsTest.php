<?php
use WorkerF\Http\Requests;

class RequestsTest extends PHPUnit_Framework_TestCase
{
    public function testRequests()
    {
        $_GET     = (object) ['foo' => 'bar'];
        $_POST    = (object) ['foz' => 'baz'];
        $_REQUEST = (object) ['foo' => 'bar', 'foz' => 'baz'];
        $_SERVER  = (object) ['server' => 'test'];
        $_COOKIE  = (object) ['foo' => 'bar'];
        $_FILES   = (object) ['foo' => 'bar'];
        $_FILES   = (object) ['foo' => 'bar'];
        $GLOBALS['HTTP_RAW_POST_DATA'] = '{"a":"test"}';

        $requests = new Requests();

        $this->assertEquals($_GET, $requests->get());
        $this->assertEquals($_POST, $requests->post());
        $this->assertEquals($_REQUEST, $requests->request());
        $this->assertEquals($_SERVER, $requests->server());
        $this->assertEquals($_COOKIE, $requests->cookie());
        $this->assertEquals($_FILES, $requests->files());
        $this->assertEquals($GLOBALS['HTTP_RAW_POST_DATA'], $requests->rawData());
    }

    public function testMagicGet()
    {
        $_REQUEST = (object) ['foo' => 'bar', 'foz' => 'baz'];
        $GLOBALS['HTTP_RAW_POST_DATA'] = '{"a":"test"}';

        $requests = new Requests();
        $this->assertEquals('bar', $requests->foo);
        $this->assertEquals('baz', $requests->foz);
    }

}
