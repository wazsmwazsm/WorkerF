<?php
use WorkerF\Config;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    public function testGetSet()
    {
          $foo = 'bar';

          Config::set('a.b.c', $foo);

          $this->assertEquals($foo, Config::get('a.b.c'));

          $foo = 'hello';

          Config::set('a', $foo);

          $this->assertEquals($foo, Config::get('a'));
    }

    public function testGetRootPath()
    {
          $this->assertEquals(getcwd(), Config::getRootPath());
    }

    public function testLoad()
    {
        $foo = [
            'a1' => [
                'b1' => 'hello',
            ],
        ];
        Config::load('file', $foo);

        $this->assertEquals('hello', Config::get('file.a1.b1'));
    }
}
