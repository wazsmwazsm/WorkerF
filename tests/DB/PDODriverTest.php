<?php
use WorkerF\DB\Drivers\PDODriver;

class PDODriverFake extends PDODriver
{

    protected static $_quote_symbol = '`';

    // do not create pdo connection
    public function __construct($config)
    {
        $this->_config = $config;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public static function quote($word)
    {
        return self::_quote($word);
    }

    public static function wrapRow($str)
    {
        return self::_wrapRow($str);
    }

    public static function getPlh()
    {
        return self::_getPlh();
    }

    public function wrapTable($table)
    {
        return $this->_wrapTable($table);
    }

    public function isTimeout(PDOException $e)
    {
        return $this->_isTimeout($e);
    }

    public function buildQuery()
    {
        $this->_buildQuery();
    }

    public function buildInsert()
    {
        $this->_buildInsert();
    }

    public function buildUpdate()
    {
        $this->_buildUpdate();
    }

    public function buildDelete()
    {
        $this->_buildDelete();
    }

    public function wrapPrepareSql()
    {
        $this->_wrapPrepareSql();
    }
}

class PDODriverTest extends PHPUnit_Framework_TestCase
{
    public $pdoDriver;

    public function setUp()
    {
        // 被测对象
        $config = [
          'prefix' => 't_',
        ];

        $this->pdoDriver = new PDODriverFake($config);
    }

    public function testQuote()
    {
        $expect = '`hello`';
        $result = PDODriverFake::quote('hello');

        $this->assertEquals($expect, $result);
    }

    public function testWrapRow()
    {
        // field mode
        $result = PDODriverFake::wrapRow('hello');
        $this->assertEquals('`hello`', $result);
        // as mode
        $result = PDODriverFake::wrapRow('time as count');
        $this->assertEquals('`time` as `count`', $result);
        // prefix mode
        $result = PDODriverFake::wrapRow('time.count');
        $this->assertEquals('`time`.`count`', $result);
        // func mode
        $result = PDODriverFake::wrapRow('count(sum)');
        $this->assertEquals('count(sum)', $result);
    }

    public function testIsTimeout()
    {
        $e = new \PDOException('something error');

        $e->errorInfo[1] = 2006;
        $result = $this->pdoDriver->isTimeout($e);
        $this->assertTrue($result);

        $e->errorInfo[1] = 2013;
        $result = $this->pdoDriver->isTimeout($e);
        $this->assertTrue($result);

        $e->errorInfo[1] = 7;
        $result = $this->pdoDriver->isTimeout($e);
        $this->assertTrue($result);

        $e->errorInfo[1] = 233;
        $result = $this->pdoDriver->isTimeout($e);
        $this->assertNotTrue($result);
    }

    public function testWrapTable()
    {
        $table = $this->pdoDriver->wrapTable('hello');
        $this->assertEquals('t_hello', $table);
    }

    public function testBuildQuery()
    {

        $this->pdoDriver->_table = ' test ';
        $this->pdoDriver->_prepare_sql = '';
        $this->pdoDriver->_cols_str = ' test.name, test.age ';
        $this->pdoDriver->_where_str = ' WHERE test.age = 25 ';
        $this->pdoDriver->_orderby_str = ' ORDER BY test.age DESC ';
        $this->pdoDriver->_groupby_str = ' GROUP BY test.sex ';
        $this->pdoDriver->_having_str = ' HAVING COUNT(test.sex) < 1 ';
        $this->pdoDriver->_join_str = ' LEFT JOIN test2 ON test.name = test2.name ';
        $this->pdoDriver->_limit_str = ' LIMIT 1 OFFSET 2';

        $this->pdoDriver->buildQuery();

        $expect = 'SELECT test.name, test.age FROM test LEFT JOIN test2 ON test.name = test2.name WHERE test.age = 25 GROUP BY test.sex HAVING COUNT(test.sex) < 1 ORDER BY test.age DESC LIMIT 1 OFFSET 2';

        // escape space
        $result = preg_replace('/\s+/', ' ', $this->pdoDriver->_prepare_sql);

        $this->assertEquals($expect, $result);
    }

    public function testBuildInsert()
    {

        $this->pdoDriver->_table = ' test ';
        $this->pdoDriver->_prepare_sql = '';
        $this->pdoDriver->_insert_str = " (name, age, sex) VALUES ('jack', 24, 1)";

        $this->pdoDriver->buildInsert();

        $expect = "INSERT INTO test (name, age, sex) VALUES ('jack', 24, 1)";

        // escape space
        $result = preg_replace('/\s+/', ' ', $this->pdoDriver->_prepare_sql);

        $this->assertEquals($expect, $result);
    }

    public function testBuildUpdate()
    {

        $this->pdoDriver->_table = ' test ';
        $this->pdoDriver->_prepare_sql = '';
        $this->pdoDriver->_where_str = ' WHERE age = 25 ';
        $this->pdoDriver->_update_str = " SET name = 'mary', age = 23 ";

        $this->pdoDriver->buildUpdate();

        $expect = "UPDATE test SET name = 'mary', age = 23 WHERE age = 25 ";

        // escape space
        $result = preg_replace('/\s+/', ' ', $this->pdoDriver->_prepare_sql);

        $this->assertEquals($expect, $result);
    }

    public function testBuildDelete()
    {

        $this->pdoDriver->_table = ' test ';
        $this->pdoDriver->_where_str = ' WHERE age = 25 ';

        $this->pdoDriver->buildDelete();

        $expect = "DELETE FROM test WHERE age = 25 ";

        // escape space
        $result = preg_replace('/\s+/', ' ', $this->pdoDriver->_prepare_sql);

        $this->assertEquals($expect, $result);
    }

    public function testWrapPrepareSql()
    {
        $this->pdoDriver->_table = 'test';
        $this->pdoDriver->_prepare_sql = '';
        $this->pdoDriver->_cols_str = ' `test`.`name`, `test`.`age` ';
        $this->pdoDriver->_where_str = ' WHERE `test`.`age` = 25 ';
        $this->pdoDriver->_orderby_str = ' ORDER BY `test`.`age` DESC ';
        $this->pdoDriver->_groupby_str = ' GROUP BY `test`.`sex` ';
        $this->pdoDriver->_having_str = ' HAVING COUNT(`test`.`sex`) < 1 ';
        $this->pdoDriver->_join_str = ' LEFT JOIN `t_test2` ON `test`.`name` = `test2`.`name` ';
        $this->pdoDriver->_limit_str = ' LIMIT 1 OFFSET 2';

        $this->pdoDriver->_table = $this->pdoDriver->quote($this->pdoDriver->wrapTable($this->pdoDriver->_table));
        $this->pdoDriver->buildQuery();
        $this->pdoDriver->wrapPrepareSql();


        $expect = 'SELECT `t_test`.`name`, `t_test`.`age` FROM `t_test` LEFT JOIN `t_test2` ON `t_test`.`name` = `t_test2`.`name` WHERE `t_test`.`age` = 25 GROUP BY `t_test`.`sex` HAVING COUNT(`t_test`.`sex`) < 1 ORDER BY `t_test`.`age` DESC LIMIT 1 OFFSET 2';

        // escape space
        $result = preg_replace('/\s+/', ' ', $this->pdoDriver->_prepare_sql);

        $this->assertEquals($expect, $result);
    }

    public function testGetPlh()
    {
        $plh = PDODriverFake::getPlh();
        $this->assertEquals(':', substr($plh, 0, 1));
        $this->assertEquals(33, strlen($plh));
    }
}
