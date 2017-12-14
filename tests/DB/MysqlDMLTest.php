<?php
require_once 'PDODML.php';
use WorkerF\DB\Drivers\Mysql;

class MysqlDMLTest extends PDODMLTest
{

    public function getConnection()
    {
        // pdo 对象，用于测试被测对象和构建测试基境
        if (self::$pdo == null) {
            $dsn = 'mysql:dbname=test;host=localhost;port=3306';
            // $dsn = 'mysql:dbname=test;unix_socket=/var/run/mysqld/mysqld.sock';
            $options = [
                PDO::ATTR_CASE => PDO::CASE_NATURAL,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
                PDO::ATTR_STRINGIFY_FETCHES => FALSE,
                PDO::ATTR_EMULATE_PREPARES => FALSE,
            ];
            self::$pdo = new PDO($dsn, 'root', '', $options);
            self::$pdo->prepare('set names utf8 collate utf8_general_ci')->execute();
            self::$pdo->prepare('set time_zone=\'+8:00\'')->execute();
            self::$pdo->prepare("set session sql_mode=''")->execute();
            // self::$pdo->prepare("set session sql_mode='STRICT_ALL_TABLES'")->execute();
        }
        // 待测的 mysql 对象
        if (self::$db == null) {
            $config = [
              'host'       => 'localhost',
              'port'       => '3306',
              'user'       => 'root',
              'password'   => '',
              'dbname'     => 'test',
              'charset'    => 'utf8',
              'prefix'     => 't_',
              'timezone'   => '+8:00',
              'collection' => 'utf8_general_ci',
              'strict'     => false,
              // 'unix_socket' => '/var/run/mysqld/mysqld.sock',
            ];

            self::$db = new Mysql($config);
        }

        return $this->createDefaultDBConnection(self::$pdo, ':memory:');
    }
}
