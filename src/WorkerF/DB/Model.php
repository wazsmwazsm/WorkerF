<?php
namespace WorkerF\DB;

use WorkerF\DB\DB;
/**
 * DB.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class Model
{
    /**
     * database connection
     *
     * @var string
     */
    protected $connection;

    /**
     * database table
     *
     * @var string
     */
    protected $table;

    /**
     * call method from DB.
     *
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        // get db connection
        $db = DB::connection($this->connection)->table($this->table);

        return call_user_func_array([$db, $method], $params);
    }

    /**
     * call static method from DB.
     *
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        $instance = new static;
        // get db connection
        $db = DB::connection($instance->connection)->table($instance->table);

        return call_user_func_array([$db, $method], $params);
    }
}
