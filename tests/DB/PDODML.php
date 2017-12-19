<?php

class PDODMLTest extends PHPUnit_Framework_TestCase
{
    use PHPUnit_Extensions_Database_TestCase_Trait;

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

    public function testInsert()
    {
        $insert_data = [
          'id'        => 5,
          'c_id'      => 1,
          'groupname' => 'test_group',
          'sort_num'  => 50,
          'created'   => time(),
        ];

        $count_before = self::$db->table('user_group')->count();

        $effect_row = self::$db->table('user_group')->insert($insert_data);

        $rst = self::$db->table('user_group')->where('id', 5)->row();
        $count_after = self::$db->table('user_group')->count();

        $this->assertEquals(1, $effect_row);
        $this->assertEquals($count_before + 1, $count_after);
    }

    public function testInsertGetLastId()
    {
        $insert_data = [
          'id'        => 5,
          'c_id'      => 1,
          'groupname' => 'test_group',
          'sort_num'  => 50,
          'created'   => time(),
        ];

        $last_id = self::$db->table('user_group')->insertGetLastId($insert_data);

        $this->assertEquals(5, $last_id);
    }

    public function testUpdate()
    {
        $update_data = [
          'groupname' => 'test_group',
          'sort_num'  => 88,
        ];

        $before = self::$db->table('user_group')->where('id', 3)->row();
        $count_before = self::$db->table('user_group')->count();

        $effect_row = self::$db->table('user_group')
                    ->where('id', 3)
                    ->update($update_data);

        $after = self::$db->table('user_group')->where('id', 3)->row();
        $count_after = self::$db->table('user_group')->count();

        $this->assertEquals(1, $effect_row);
        $this->assertNotEquals($before, $after);
        $this->assertEquals($count_before, $count_after);
        $this->assertEquals(88, $after['sort_num']);
    }

    public function testDelete()
    {
        $count_before = self::$db->table('user_group')->count();

        $effect_row = self::$db->table('user_group')
                    ->where('c_id', 1)
                    ->delete();

        $count_after = self::$db->table('user_group')->count();

        $this->assertEquals(2, $effect_row);
        $this->assertEquals($count_before - 2, $count_after);
    }

    public function testTrans()
    {
        // rollBack
        $count_before = self::$db->table('user_group')->count();

        self::$db->beginTrans();
        self::$db->table('user_group')
                 ->where('c_id', 1)
                 ->delete();
        self::$db->rollBackTrans();

        $count_after = self::$db->table('user_group')->count();

        $this->assertEquals($count_before, $count_after);

        // commit
        $count_before = self::$db->table('user_group')->count();

        self::$db->beginTrans();
        self::$db->table('user_group')
                 ->where('c_id', 2)
                 ->delete();
        self::$db->commitTrans();

        $count_after = self::$db->table('user_group')->count();

        $this->assertEquals($count_before - 2, $count_after);

    }

}
