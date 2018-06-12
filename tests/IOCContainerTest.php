<?php
namespace WorkerF\Tests;

use PHPUnit_Framework_TestCase;
use WorkerF\IOCContainer;
use ReflectionClass;

class IOCContainerFake extends IOCContainer
{
    public static function getDiParams(array $params)
    {
        return self::_getDiParams($params);
    }
}

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

class Bar
{
    public $a = 2333;

    public $b = 666;

    public function __construct(Foo $foo, Foz $foz)
    {
        $this->a = $foo->a;
        $this->b = $foz->b;
    }

    public function f1(Foo $foo)
    {
        $this->a = $foo->a + $foo->b;

        return $this->a;
    }
}


class IOCContainerTest extends PHPUnit_Framework_TestCase
{
    public function testSingleton()
    {
        $singleton = IOCContainerFake::getSingleton('WorkerF\Tests\Foo');
        $this->assertNull($singleton);

        $foo = new Foo();
        IOCContainerFake::singleton($foo);

        $singleton = IOCContainerFake::getSingleton('WorkerF\Tests\Foo');
        $this->assertEquals($singleton, $foo);

    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testSingletonException()
    {
        IOCContainerFake::singleton('WorkerF\Tests\Foo');
    }

    public function testGetDiParams()
    {
        // test construct
        $reflector = new ReflectionClass('WorkerF\Tests\Bar');
        $constructor = $reflector->getConstructor();
        $di_params = IOCContainerFake::getDiParams($constructor->getParameters());

        $this->assertEquals(2, count($di_params));
        $this->assertInstanceOf('WorkerF\Tests\Foo', $di_params[0]);
        $this->assertInstanceOf('WorkerF\Tests\Foz', $di_params[1]);

        // test function
        $reflector = new ReflectionClass('WorkerF\Tests\Bar');
        $reflectorMethod = $reflector->getMethod('f1');
        $di_params = IOCContainerFake::getDiParams($reflectorMethod->getParameters());
        $this->assertEquals(1, count($di_params));
        $this->assertInstanceOf('WorkerF\Tests\Foo', $di_params[0]);
    }

    public function testGetInstance()
    {
        $foo = new Foo();
        $foz = new Foz();
        $expect = new Bar($foo, $foz);
        $result = IOCContainerFake::getInstance('WorkerF\Tests\Bar');

        $this->assertEquals($expect, $result);
    }

    public function testGetInstanceSingleton()
    {
        $foo = new Foo();
        $foz = new Foz();
        $expect = new Bar($foo, $foz);
        
        $this->assertEquals(NULL, IOCContainerFake::getSingleton('WorkerF\Tests\Bar'));
        // set singleton
        IOCContainerFake::singleton($expect);

        $result = IOCContainerFake::getInstance('WorkerF\Tests\Bar');

        $this->assertEquals($expect, $result);

        $this->assertEquals($expect, IOCContainerFake::getSingleton('WorkerF\Tests\Bar'));
    }

    public function testRun()
    {
        $foo = new Foo();
        $foz = new Foz();
        $expect = new Bar($foo, $foz);

        $result = IOCContainerFake::run('WorkerF\Tests\Bar', 'f1');
        
        $this->assertEquals($expect->f1($foo), $result);
    }

    /**
    * @expectedException \BadMethodCallException
    */
    public function testRunExceptionClassNotFound()
    {
        $result = IOCContainerFake::run('WorkerF\Tests\Baz', 'f1');
    }

    /**
    * @expectedException \BadMethodCallException
    */
    public function testRunExceptionMethodNotFound()
    {
        $result = IOCContainerFake::run('WorkerF\Tests\Bar', 'f2');
    }
}
