<?php
require __DIR__.'/fake/IOCContainerFake.php';
use WorkerF\IOCContainer;

class Foo
{
    public $a = 1;

    public $b = 2;
}

class Bar
{
    public $a;

    public function f1(Foo $foo)
    {
        $this->a = $foo->a + $foo->b;
    }
}


class IOCContainerTest extends PHPUnit_Framework_TestCase
{
    public function testSingleton()
    {
        $singleton = IOCContainerFake::getSingleton(Foo::class);
        $this->assertNull($singleton);

        $foo = new Foo();
        IOCContainerFake::singleton($foo);

        $singleton = IOCContainerFake::getSingleton(Foo::class);
        $this->assertEquals($singleton, $foo);

    }


}
