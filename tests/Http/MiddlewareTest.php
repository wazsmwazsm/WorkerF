<?php

namespace WorkerF\Tests\Http;

use WorkerF\Http\Middleware;
use WorkerF\Http\Requests;
use WorkerF\Http\MiddlewareInterface;

class M1 implements MiddlewareInterface
{
    public function handle(Requests $request)
    {
        return $request;
    }
}

class M2 implements MiddlewareInterface
{
    public function handle(Requests $request)
    {
        return function() {
            return 'stop at m2!';
        };
    }
}

class Some
{
    public function handle(Requests $request)
    {
        return $request;
    }
}

class MiddlewareTest extends \PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        $GLOBALS['HTTP_RAW_POST_DATA'] = '{"a":"test"}';

        $request = new Requests();
        // null
        $middlewares = [];
        $result = Middleware::run($middlewares, $request);
        $this->assertEquals($request, $result);
        // passed
        $middlewares = ['WorkerF\Tests\Http\M1'];
        $result = Middleware::run($middlewares, $request);
        $this->assertEquals($request, $result);
        // not passed
        $middlewares = ['WorkerF\Tests\Http\M2'];
        $result = Middleware::run($middlewares, $request);
        $this->assertInstanceOf('Closure', $result);
        $this->assertEquals('stop at m2!', call_user_func($result));
        // not passed
        $middlewares = ['WorkerF\Tests\Http\M1', 'WorkerF\Tests\Http\M2'];
        $result = Middleware::run($middlewares, $request);
        $this->assertInstanceOf('Closure', $result);
        $this->assertEquals('stop at m2!', call_user_func($result));
    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testMiddlewareNotImplementsInterfaceException()
    {
        $GLOBALS['HTTP_RAW_POST_DATA'] = '{"a":"test"}';

        $request = new Requests();
        // null
        $middlewares = ['WorkerF\Tests\Http\Some'];
        $result = Middleware::run($middlewares, $request);
    }

}
