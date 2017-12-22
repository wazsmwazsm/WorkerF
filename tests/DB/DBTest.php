<?php
use WorkerF\Config;
use WorkerF\DB\DB;

class DBTest extends PHPUnit_Framework_TestCase
{

    public function testConnection() {
        $this->setConfig();

        $db_confs = Config::get('database.db_con');
        DB::init();

        $mysql = DB::connection('con1');
        $pgsql = DB::connection('con2');
        $sqlite = DB::connection('con3');

        $this->assertTrue($mysql instanceof \WorkerF\DB\Drivers\Mysql);
        $this->assertTrue($pgsql instanceof \WorkerF\DB\Drivers\Pgsql);
        $this->assertTrue($sqlite instanceof \WorkerF\DB\Drivers\Sqlite);
    }

    public function setConfig() {

        $db_con = [
            'db_con' => [
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
                    'dbname'   => dirname(__FILE__).'/test.db',
                    'charset'  => 'utf8',
                ],
            ]
        ];

        Config::load('database', $db_con);
    }

}
