<?php
use WorkerF\DB\DB;

class DBTest extends PHPUnit_Framework_TestCase
{

    public function testConnection()
    {
        DB::init($this->getConfig());

        $mysql = DB::connection('con1');
        $pgsql = DB::connection('con2');
        $sqlite = DB::connection('con3');

        $this->assertInstanceOf('\WorkerF\DB\Drivers\Mysql', $mysql);
        $this->assertInstanceOf('\WorkerF\DB\Drivers\Pgsql', $pgsql);
        $this->assertInstanceOf('\WorkerF\DB\Drivers\Sqlite', $sqlite);
    }

    /**
    * @expectedException WorkerF\DB\ConnectException
    */
    public function testConnectFailed()
    {
        $conf = [
            'con1' => [
                'driver'   => 'mysql',
                'host'     => 'localhost',
                'port'     => '3306',
                'user'     => 'foo',
                'password' => 'bar',
                'dbname'   => 'test',
                'charset'  => 'utf8',
            ],
        ];

        DB::init($conf);
    }

    public function getConfig()
    {
        return [
            'con1' => [
                'driver'   => 'mysql',
                'host'     => 'localhost',
                'port'     => '3306',
                'user'     => 'root',
                'password' => '',
                'dbname'   => 'test',
                'charset'  => 'utf8',
            ],
            'con2' => [
                'driver'   => 'pgsql',
                'host'     => 'localhost',
                'port'     => '5432',
                'user'     => 'postgres',
                'password' => '',
                'dbname'   => 'test',
                'charset'  => 'utf8',
            ],
            'con3' => [
                'driver'   => 'sqlite',
                'dbname'   => __DIR__.'/test.db',
                'charset'  => 'utf8',
            ],
        ];
    }

}
