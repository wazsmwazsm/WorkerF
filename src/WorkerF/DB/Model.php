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
     * @var String
     */
    protected $connection;

    /**
     * database table
     *
     * @var String
     */
    protected $table;

    /**
     * call method from DB.
     *
     * @param String $method
     * @param Array $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        // get db connection
        $db = DB::connection($this->connection)->table($this->table);
        // improve the efficiency
        switch (count($params)) {
            case 0:
                return $db->$method();
            case 1:
                return $db->$method($params[0]);
            case 2:
                return $db->$method($params[0], $params[1]);
            case 3:
                return $db->$method($params[0], $params[1], $params[2]);
            case 4:
                return $db->$method($params[0], $params[1], $params[2], $params[3]);
            default:
                return call_user_func_array([$db, $method], $params);
        }
    }

    /**
     * call static method from DB.
     *
     * @param String $method
     * @param Array $params
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        $instance = new static;
        // get db connection
        $db = DB::connection($instance->connection)->table($instance->table);
        // improve the efficiency
        switch (count($params)) {
            case 0:
                return $db->$method();
            case 1:
                return $db->$method($params[0]);
            case 2:
                return $db->$method($params[0], $params[1]);
            case 3:
                return $db->$method($params[0], $params[1], $params[2]);
            case 4:
                return $db->$method($params[0], $params[1], $params[2], $params[3]);
            default:
                return call_user_func_array([$db, $method], $params);
        }
    }
}
