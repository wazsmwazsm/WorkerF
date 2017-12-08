<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;

class PDODMLTest extends TestCase
{
    use TestCaseTrait;

    protected static $db;

    protected static $pdo;

    public function getConnection()
    {

    }

    public function getDataSet()
    {
        return $this->createXMLDataSet(dirname(__FILE__).'/test.xml');
    }

    public function testSetGetTable()
    {
        $this->assertEquals('t_user', self::$db->table('user')->getTable());
    }

}
