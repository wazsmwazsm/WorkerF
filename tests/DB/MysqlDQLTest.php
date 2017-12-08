<?php
require_once 'PDODQL.php';
use WorkerF\DB\Drivers\Mysql;

class MysqlDQLTest extends PDODQLTest
{
    public static function setUpBeforeClass()
    {
        // 新建 pdo 对象, 用于测试被测驱动
        $dsn = 'mysql:dbname=test;host=localhost;port=3306';
        // $dsn = 'mysql:dbname=test;unix_socket=/var/run/mysqld/mysqld.sock';
        $options = [
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
            PDO::ATTR_STRINGIFY_FETCHES => FALSE,
            PDO::ATTR_EMULATE_PREPARES => FALSE,
        ];
        self::$pdo = new PDO($dsn, 'homestead', 'secret', $options);
        self::$pdo->prepare('set names utf8 collate utf8_general_ci')->execute();
        self::$pdo->prepare('set time_zone=\'+8:00\'')->execute();
        self::$pdo->prepare("set session sql_mode=''")->execute();
        // self::$pdo->prepare("set session sql_mode='STRICT_ALL_TABLES'")->execute();

        // 被测对象
        $config = [
          'host'        => 'localhost',
          'port'        => '3306',
          'user'        => 'homestead',
          'password'    => 'secret',
          'dbname'      => 'test',
          'charset'     => 'utf8',
          'prefix'      => 't_',
          'timezone'    => '+8:00',
          'collection'  => 'utf8_general_ci',
          'strict'      => false,
          // 'unix_socket' => '/var/run/mysqld/mysqld.sock',
        ];
        self::$db = new Mysql($config);
    }

}
