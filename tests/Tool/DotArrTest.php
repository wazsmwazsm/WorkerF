<?php
use WorkerF\Tool\DotArr;

class DotArrTest extends PHPUnit_Framework_TestCase
{

    public function testSet()
    {
        $arr = [];
        $expect = [
            'foo' => [
                'bar' => 'hello'
            ]
        ];

        DotArr::dotSet($arr, 'foo.bar', 'hello');

        $this->assertEquals($expect, $arr);
    }

    public function testGet()
    {
        $arr = [];

        DotArr::dotSet($arr, 'foo.bar', 'hello');

        $this->assertEquals('hello', DotArr::dotGet($arr, 'foo.bar'));
    }

    public function testHas()
    {
        $arr = [];

        DotArr::dotSet($arr, 'foo.bar', 'hello');

        $this->assertTrue(DotArr::dotHas($arr, 'foo.bar'));
        $this->assertFalse(DotArr::dotHas($arr, 'foo.some'));
    }

}
