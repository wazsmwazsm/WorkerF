<?php

namespace WorkerF\Tests\Http;

use PHPUnit_Framework_TestCase;
use WorkerF\Http\Middleware;
use WorkerF\Http\Requests;
use WorkerF\Http\MiddlewareInterface;
use WorkerF\IOCContainer;

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

class MiddlewareTest extends PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        $GLOBALS['HTTP_RAW_POST_DATA'] = '{"a":"test"}';
        IOCContainer::register(M1::class);
        IOCContainer::register(M2::class);

        $request = new Requests();
        // null
        $middlewares = [];
        $result = Middleware::run($middlewares, $request);
        $this->assertEquals($request, $result);
        // passed
        $middlewares = [M1::class];
        $result = Middleware::run($middlewares, $request);
        $this->assertEquals($request, $result);
        // not passed
        $middlewares = [M2::class];
        $result = Middleware::run($middlewares, $request);
        $this->assertEquals('stop at m2!', $result);
        // not passed
        $middlewares = [M1::class, M2::class];
        $result = Middleware::run($middlewares, $request);
        $this->assertEquals('stop at m2!', $result);
    }
}
