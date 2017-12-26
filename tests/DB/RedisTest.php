<?php
use WorkerF\DB\Redis;

class RedisTest extends PHPUnit_Framework_TestCase
{

    public function testConnection() {

        Redis::init($this->getConfig());
        $default = Redis::connection('default');

        $this->assertTrue($default instanceof \Predis\Client);
    }

    public function testGetSet() {

        Redis::init($this->getConfig());
        $foo = 'hello';

        Redis::set('foo', $foo);

        $this->assertEquals($foo, Redis::get('foo'));
    }

    /**
    * @expectedException WorkerF\DB\ConnectException
    */
    public function testConnectFailed() {

        $conf = [
            'cluster' => FALSE,
            'options' => NULL,
            'rd_con' => [
                'default' => [
                    'host'     => '127.0.0.11',
                    'password' => NULL,
                    'port'     => 6379,
                    'database' => 0,
                    // 'read_write_timeout' => 0,
                ],
            ]
        ];

        Redis::init($conf);
    }

    public function getConfig() {

        return [
            'cluster' => FALSE,
            'options' => NULL,
            'rd_con' => [
                'default' => [
                    'host'     => '127.0.0.1',
                    'password' => NULL,
                    'port'     => 6379,
                    'database' => 0,
                    // 'read_write_timeout' => 0,
                ],
            ]
        ];
    }

}
