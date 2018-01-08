<?php
use WorkerF\DB\Drivers\PDODriver;

/**
 * PDODriverFake class , use mysql quote
 *
 */
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

    public function reset()
    {
        $this->_reset();
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

    public function condition_constructor($args_num, $params, &$construct_str)
    {
        $this->_condition_constructor($args_num, $params, $construct_str);
    }

    public function storeBuildAttr()
    {
        return $this->_storeBuildAttr();
    }

    public function reStoreBuildAttr(array $data)
    {
        $this->_reStoreBuildAttr($data);
    }

    public function storeBindParam()
    {
        return $this->_storeBindParam();
    }

    public function reStoreBindParam($bind_params)
    {
        $this->_reStoreBindParam($bind_params);
    }

    public function subBuilder(Closure $callback)
    {
        return $this->_subBuilder($callback);
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

    public function testConditionConstructor()
    {
        // 1 param mode
        $construct_str = '';
        $args_num = 1;
        $params = [
          [
            'name' => 'jack',
            'age'  => 25,
          ]
        ];
        $this->pdoDriver->condition_constructor($args_num, $params, $construct_str);

        $match = [];
        preg_match('/\( `name` = (:[0-9a-z]{32}) AND `age` = (:[0-9a-z]{32}) \)/', $construct_str, $match);

        $this->assertEquals(2, count($this->pdoDriver->_bind_params));
        $this->assertEquals('jack', $this->pdoDriver->_bind_params[$match[1]]);
        $this->assertEquals(25, $this->pdoDriver->_bind_params[$match[2]]);
        $this->assertRegExp('/\( `name` = :[0-9a-z]{32} AND `age` = :[0-9a-z]{32} \)/', $construct_str);

        // 2 param mode
        $construct_str = '';
        $args_num = 2;
        $params = ['name', 'jack'];
        $this->pdoDriver->condition_constructor($args_num, $params, $construct_str);

        $match = [];
        preg_match('/ `name` = (:[0-9a-z]{32}) /', $construct_str, $match);
        $this->assertEquals(3, count($this->pdoDriver->_bind_params));
        $this->assertEquals('jack', $this->pdoDriver->_bind_params[$match[1]]);
        $this->assertRegExp('/ `name` = :[0-9a-z]{32} /', $construct_str);
        // 2 param is null mode
        $construct_str = '';
        $args_num = 2;
        $params = ['name', NULL];
        $this->pdoDriver->condition_constructor($args_num, $params, $construct_str);

        $this->assertEquals(' `name` IS NULL ', $construct_str);

        // 3 parm mode
        $construct_str = '';
        $args_num = 3;
        $params = ['age', '<=', 30];
        $this->pdoDriver->condition_constructor($args_num, $params, $construct_str);

        $match = [];
        preg_match('/ `age` <= (:[0-9a-z]{32}) /', $construct_str, $match);
        $this->assertEquals(4, count($this->pdoDriver->_bind_params));
        $this->assertEquals(30, $this->pdoDriver->_bind_params[$match[1]]);
        $this->assertRegExp('/ `age` <= :[0-9a-z]{32} /', $construct_str);

        $construct_str = '';
        $args_num = 3;
        $params = ['name', 'like', 'joe'];
        $this->pdoDriver->condition_constructor($args_num, $params, $construct_str);

        $match = [];
        preg_match('/ `name` like (:[0-9a-z]{32}) /', $construct_str, $match);

        $this->assertEquals('joe', $this->pdoDriver->_bind_params[$match[1]]);
        $this->assertEquals(5, count($this->pdoDriver->_bind_params));
        $this->assertRegExp('/ `name` like :[0-9a-z]{32} /', $construct_str);
    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testConditionConstructorWrongParam1()
    {
        // args_num error
        $construct_str = '';
        $args_num = 5;
        $params = ['name', 'like', 'joe'];
        $this->pdoDriver->condition_constructor($args_num, $params, $construct_str);
    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testConditionConstructorWrongParam2()
    {
        // args_num is 1, but $params[0] is not an array
        $construct_str = '';
        $args_num = 1;
        $params = ['name', 'like', 'joe'];
        $this->pdoDriver->condition_constructor($args_num, $params, $construct_str);
    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testConditionConstructorWrongParam3()
    {
        // Confusing Symbol
        $construct_str = '';
        $args_num = 1;
        $params = ['name', 'fuck', 'joe'];
        $this->pdoDriver->condition_constructor($args_num, $params, $construct_str);
    }

    public function testStoreRestoreBuildAttr()
    {
        // store
        $this->pdoDriver->_table = 'test';
        $this->pdoDriver->_prepare_sql = 'SELECT * FROM test';

        $buildAttr = $this->pdoDriver->storeBuildAttr();

        $this->assertEquals($this->pdoDriver->_table, $buildAttr['table']);
        $this->assertEquals($this->pdoDriver->_prepare_sql, $buildAttr['prepare_sql']);

        // restore
        $this->pdoDriver->reset();
        $this->assertEmpty($this->pdoDriver->_table);
        $this->assertEmpty($this->pdoDriver->_prepare_sql);

        $this->pdoDriver->reStoreBuildAttr($buildAttr);
        $this->assertEquals($this->pdoDriver->_table, $buildAttr['table']);
        $this->assertEquals($this->pdoDriver->_prepare_sql, $buildAttr['prepare_sql']);
    }

    public function testStoreRestoreBindParam()
    {
        // store
        $this->_bind_params = [
            'foo' => 1,
            'bar' => 2,
        ];

        $bindParam = $this->pdoDriver->storeBindParam();

        $this->assertEquals($this->pdoDriver->_bind_params, $bindParam);

        // restore
        $this->pdoDriver->reset();
        $this->assertEmpty($this->pdoDriver->_bind_params);

        $this->pdoDriver->reStoreBindParam($bindParam);
        $this->assertEquals($this->pdoDriver->_bind_params, $bindParam);
    }

    public function testSubBuilder()
    {
        $this->pdoDriver->_table = 'test';
        $this->pdoDriver->_cols_str = '*';

        $sub_attr = $this->pdoDriver->subBuilder(function($query) {
            $query->_table = 'hello';
            $query->_cols_str = 'hi';
        });

        $this->assertEquals('test', $this->pdoDriver->_table);
        $this->assertEquals('*', $this->pdoDriver->_cols_str);
        $this->assertEquals('hello', $sub_attr['table']);
        $this->assertEquals('hi', $sub_attr['cols_str']);
    }

    public function testTable()
    {
        $pdo = $this->pdoDriver->table('test_table');
        $this->assertEquals($this->pdoDriver, $pdo);
        $this->assertEquals('`t_test_table`', $this->pdoDriver->_table);
    }

    public function testSelect()
    {
        $pdo = $this->pdoDriver->select();
        $this->assertEquals($this->pdoDriver, $pdo);
        $this->assertEquals(' * ', $this->pdoDriver->_cols_str);

        $pdo = $this->pdoDriver->select('*');
        $this->assertEquals($this->pdoDriver, $pdo);
        $this->assertEquals(' * ', $this->pdoDriver->_cols_str);

        $pdo = $this->pdoDriver->select('name', 'age');
        $this->assertEquals($this->pdoDriver, $pdo);
        $this->assertEquals(' `name`, `age`', $this->pdoDriver->_cols_str);
    }

    public function testWhere()
    {
        // first
        $pdo = $this->pdoDriver->where('name', 'jack');
        $this->assertEquals($this->pdoDriver, $pdo);
        $match = [];
        preg_match('/ `name` = (:[0-9a-z]{32}) /', $this->pdoDriver->_where_str, $match);

        $this->assertEquals('jack', $this->pdoDriver->_bind_params[$match[1]]);
        $this->assertEquals(1, count($this->pdoDriver->_bind_params));
        $this->assertRegExp('/ WHERE\s+`name` = :[0-9a-z]{32} /', $this->pdoDriver->_where_str);

        // second
        $this->pdoDriver->where('age', '>=', 23);
        $this->assertRegExp('/ WHERE\s+`name` = :[0-9a-z]{32}\s+AND\s+`age` >= :[0-9a-z]{32} /', $this->pdoDriver->_where_str);

        // third
        $this->pdoDriver->orWhere(['sex' => 1, 'grade' => 2]);
        $this->assertRegExp('/ WHERE\s+`name` = :[0-9a-z]{32}\s+AND\s+`age` >= :[0-9a-z]{32}\s+OR\s+\( `sex` = :[0-9a-z]{32} AND `grade` = :[0-9a-z]{32} \)/', $this->pdoDriver->_where_str);

    }

    public function testWhereIn()
    {
        $pdo = $this->pdoDriver->whereIn('name', ['jack', 'green', 'hack']);
        $this->assertEquals($this->pdoDriver, $pdo);
        $match = [];

        preg_match('/ `name` IN \((:[0-9a-z]{32}),(:[0-9a-z]{32}),(:[0-9a-z]{32})\)/', $this->pdoDriver->_where_str, $match);

        $this->assertEquals(3, count($this->pdoDriver->_bind_params));
        $this->assertEquals('jack', $this->pdoDriver->_bind_params[$match[1]]);
        $this->assertEquals('green', $this->pdoDriver->_bind_params[$match[2]]);
        $this->assertEquals('hack', $this->pdoDriver->_bind_params[$match[3]]);

        $this->assertRegExp('/ WHERE\s+`name` IN \(:[0-9a-z]{32},:[0-9a-z]{32},:[0-9a-z]{32}\)/', $this->pdoDriver->_where_str);

    }

    public function testWhereNotIn()
    {
        $pdo = $this->pdoDriver->whereNotIn('name', ['jack', 'hack']);
        $this->assertRegExp('/ WHERE\s+`name` NOT IN \(:[0-9a-z]{32},:[0-9a-z]{32}\)/', $this->pdoDriver->_where_str);
    }

    public function testOrWhereIn()
    {
        $pdo = $this->pdoDriver->where('sex', NULL)->orWhereIn('name', ['jack', 'hack']);
        $this->assertRegExp('/ WHERE\s+`sex` IS NULL\s+OR `name` IN \(:[0-9a-z]{32},:[0-9a-z]{32}\)/', $this->pdoDriver->_where_str);
    }

    public function testOrWhereNotIn()
    {
        $pdo = $this->pdoDriver->where('sex', NULL)->orWhereNotIn('name', ['jack', 'hack']);
        $this->assertRegExp('/ WHERE\s+`sex` IS NULL\s+OR `name` NOT IN \(:[0-9a-z]{32},:[0-9a-z]{32}\)/', $this->pdoDriver->_where_str);
    }


    /**
    * @expectedException \InvalidArgumentException
    */
    public function testWhereInException1()
    {
        $this->pdoDriver->whereIn('name', [1, 2, 4], 'SOME', 'AND');
    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testWhereInException2()
    {
        $this->pdoDriver->whereIn('name', [1, 2, 4], 'NOT IN', 'ANDS');
    }

    public function testWhereBetween()
    {
        $pdo = $this->pdoDriver->whereBetween('age', 1, 20);
        $this->assertEquals($this->pdoDriver, $pdo);
        $match = [];

        preg_match('/ `age` BETWEEN (:[0-9a-z]{32}) AND (:[0-9a-z]{32})/', $this->pdoDriver->_where_str, $match);

        $this->assertEquals(2, count($this->pdoDriver->_bind_params));
        $this->assertEquals(1, $this->pdoDriver->_bind_params[$match[1]]);
        $this->assertEquals(20, $this->pdoDriver->_bind_params[$match[2]]);

        $this->assertRegExp('/ WHERE\s+`age` BETWEEN :[0-9a-z]{32} AND :[0-9a-z]{32}/', $this->pdoDriver->_where_str);

    }

    public function testOrWhereBetween()
    {
        $pdo = $this->pdoDriver->where('sex', NULL)->orWhereBetween('age', 1, 20);
        $this->assertRegExp('/ WHERE\s+`sex` IS NULL\s+OR `age` BETWEEN :[0-9a-z]{32} AND :[0-9a-z]{32}/', $this->pdoDriver->_where_str);
    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testWhereBetweenException()
    {
        $this->pdoDriver->whereNull('name', 1, 2, 'ANDS');
    }

    public function testWhereNull()
    {
        $pdo = $this->pdoDriver->whereNull('age');
        $this->assertEquals($this->pdoDriver, $pdo);

        $this->assertRegExp('/ WHERE\s+`age` IS NULL /', $this->pdoDriver->_where_str);
    }

    public function testWhereNotNull()
    {
        $pdo = $this->pdoDriver->whereNotNull('age');
        $this->assertEquals($this->pdoDriver, $pdo);

        $this->assertRegExp('/ WHERE\s+`age` IS NOT NULL /', $this->pdoDriver->_where_str);
    }

    public function testOrWhereNull()
    {
        $pdo = $this->pdoDriver->where('sex', NULL)->orWhereNull('age');
        $this->assertEquals($this->pdoDriver, $pdo);

        $this->assertRegExp('/ WHERE\s+`sex` IS NULL\s+OR `age` IS NULL /', $this->pdoDriver->_where_str);
    }

    public function testOrWhereNotNull()
    {
        $pdo = $this->pdoDriver->where('sex', NULL)->orWhereNotNull('age');
        $this->assertEquals($this->pdoDriver, $pdo);

        $this->assertRegExp('/ WHERE\s+`sex` IS NULL\s+OR `age` IS NOT NULL /', $this->pdoDriver->_where_str);
    }


    /**
    * @expectedException \InvalidArgumentException
    */
    public function testWhereNullException1()
    {
        $this->pdoDriver->whereNull('name', 'SOME', 'AND');
    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testWhereNullException2()
    {
        $this->pdoDriver->whereNull('name', 'NULL', 'ANDS');
    }

    public function testWhereBrackets()
    {
        $pdo = $this->pdoDriver->where('sex', NULL)
             ->whereBrackets(function($query) {
                $query->where('name', 'mike')
                      ->orWhere('name', 'juice');
             });
        $this->assertEquals($this->pdoDriver, $pdo);

        $this->assertRegExp('/ WHERE\s+`sex` IS NULL\s+AND \(\s+`name` = :[0-9a-z]{32}\s+OR\s+`name` = :[0-9a-z]{32}\s+\)/', $this->pdoDriver->_where_str);
    }

    public function testOrWhereBrackets()
    {
        $pdo = $this->pdoDriver->where('sex', NULL)
             ->orWhereBrackets(function($query) {
                $query->where('name', 'mike')
                      ->orWhere('name', 'juice');
             });
        $this->assertEquals($this->pdoDriver, $pdo);

        $this->assertRegExp('/ WHERE\s+`sex` IS NULL\s+OR \(\s+`name` = :[0-9a-z]{32}\s+OR\s+`name` = :[0-9a-z]{32}\s+\)/', $this->pdoDriver->_where_str);
    }
    /**
    * @expectedException \InvalidArgumentException
    */
    public function testWhereBracketsException()
    {
        $this->pdoDriver->whereBrackets(function() { return; }, 'SOME');
    }

    public function testWhereExists()
    {
        $pdo = $this->pdoDriver->where('sex', NULL)
             ->whereExists(function($query) {
                $query->table('test2');
             });
        $this->assertEquals($this->pdoDriver, $pdo);

        $this->assertRegExp('/ WHERE\s+`sex` IS NULL\s+AND EXISTS \(\s+SELECT\s+\*\s+FROM `t_test2`\s+\)/', $this->pdoDriver->_where_str);
    }

    public function testOrWhereExists()
    {
        $pdo = $this->pdoDriver->where('sex', NULL)
             ->orWhereExists(function($query) {
                $query->table('test2');
             });
        $this->assertEquals($this->pdoDriver, $pdo);

        $this->assertRegExp('/ WHERE\s+`sex` IS NULL\s+OR EXISTS \(\s+SELECT\s+\*\s+FROM `t_test2`\s+\)/', $this->pdoDriver->_where_str);
    }

    public function testWhereNotExists()
    {
        $pdo = $this->pdoDriver->where('sex', NULL)
             ->whereNotExists(function($query) {
                $query->table('test2');
             });
        $this->assertEquals($this->pdoDriver, $pdo);

        $this->assertRegExp('/ WHERE\s+`sex` IS NULL\s+AND NOT EXISTS \(\s+SELECT\s+\*\s+FROM `t_test2`\s+\)/', $this->pdoDriver->_where_str);
    }

    public function testOrWhereNotExists()
    {
        $pdo = $this->pdoDriver->where('sex', NULL)
             ->orWhereNotExists(function($query) {
                $query->table('test2');
             });
        $this->assertEquals($this->pdoDriver, $pdo);

        $this->assertRegExp('/ WHERE\s+`sex` IS NULL\s+OR NOT EXISTS \(\s+SELECT\s+\*\s+FROM `t_test2`\s+\)/', $this->pdoDriver->_where_str);
    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testWhereExistsException1()
    {
        $this->pdoDriver->whereExists(function() { return; }, 'SOME', 'AND');
    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testWhereExistsException2()
    {
        $this->pdoDriver->whereExists(function() { return; }, 'EXISTS', 'ANDS');
    }

    public function testWhereInSub()
    {


    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testWhereInSubException1()
    {
        $this->pdoDriver->whereInSub('name', function() { return; }, 'SOME', 'AND');
    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testWhereInSubException2()
    {
        $this->pdoDriver->whereInSub('name', function() { return; }, 'NOT IN', 'ANDS');
    }

}
