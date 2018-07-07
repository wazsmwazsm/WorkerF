<?php
namespace WorkerF\Tests;

use PHPUnit_Framework_TestCase;
use WorkerF\IOCContainer;
use WorkerF\Config;
use WorkerF\App;

class Foo
{
    public $a = 1;

    public $b = 2;
}

class Foz
{
    public $a = 3;

    public $b = 4;
}

class ErrorTest extends PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
       Config::set('app.registers', [
         Foo::class,
         Foz::class,
       ]);

        App::register();

        $this->assertEquals(new Foo, IOCContainer::getSingleton(Foo::class));
        $this->assertEquals(new Foz, IOCContainer::getSingleton(Foz::class));
    }
}
