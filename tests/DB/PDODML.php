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
        return $this->createXMLDataSet('test.xml');
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

        $last_id = self::$db->table('user_group')->insert($insert_data);

        $rst = self::$db->table('user_group')->where('id', $last_id)->row();
        $count_after = self::$db->table('user_group')->count();

        $this->assertEquals($insert_data, $rst);
        $this->assertEquals($count_before + 1, $count_after);
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
}
