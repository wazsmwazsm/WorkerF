<?php

namespace WorkerF\Tests\DB;

use PHPUnit_Framework_TestCase;
use WorkerF\DB\DB;
use WorkerF\DB\Model;

class TModel extends Model
{
    protected $connection = 'con1';

    protected $table = 'user';
}

class ModelTest extends PHPUnit_Framework_TestCase
{
    protected static $model;

    public static function setUpBeforeClass()
    {
        $conf = [
            'con1' => [
                'driver'   => 'mysql',
                'host'     => 'localhost',
                'port'     => '3306',
                'user'     => 'root',
                'password' => '',
                'dbname'   => 'test',
                'prefix'   => 't_',
                'charset'  => 'utf8',
            ],
        ];

        DB::init($conf);

        self::$model = new TModel();
    }

    public static function tearDownAfterClass()
    {
        self::$model = NULL;
    }

    public function testModel()
    {
        $foo = self::$model->select('*');
        $this->assertTrue($foo instanceof \WorkerF\DB\Drivers\Mysql);
    }

    public function testModelCallStatic()
    {
        $foo = TModel::select('*');
        $this->assertTrue($foo instanceof \WorkerF\DB\Drivers\Mysql);
    }
}
