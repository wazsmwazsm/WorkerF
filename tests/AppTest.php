<?php
namespace WorkerF\Tests;

use PHPUnit_Framework_TestCase;
use WorkerF\IOCContainer;
use WorkerF\Config;
use WorkerF\App;

class Foo1
{
    public $a = 1;

    public $b = 2;
}

class Foz1
{
    public $a = 3;

    public $b = 4;
}

class AppTest extends PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
       Config::set('app.registers', [
         Foo1::class,
         Foz1::class,
       ]);

        App::register();

        $this->assertEquals(new Foo1, IOCContainer::getSingleton(Foo1::class));
        $this->assertEquals(new Foz1, IOCContainer::getSingleton(Foz1::class));
    }
}
