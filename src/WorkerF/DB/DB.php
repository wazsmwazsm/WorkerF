<?php
namespace WorkerF\DB;
use WorkerF\Config;
use WorkerF\Error;
use WorkerF\DB\Drivers\Mysql;
use WorkerF\DB\Drivers\Pgsql;
use WorkerF\DB\Drivers\Sqlite;
/**
 * DB.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class DB
{
    /**
     * connections.
     *
     * @var array
     */
    private static $_connections = [];

    /**
     * init db connections.
     *
     * @return void
     */
    public static function init()
    {
        // get db config
        $db_confs = Config::get('database.db_con');
        // connect database
        foreach ($db_confs as $con_name => $db_conf) {
            try {
                switch (strtolower($db_conf['driver'])) {
                    case 'mysql':
                        self::$_connections[$con_name] = new Mysql($db_conf);
                        break;
                    case 'pgsql':
                        self::$_connections[$con_name] = new Pgsql($db_conf);
                        break;
                    case 'sqlite':
                        self::$_connections[$con_name] = new Sqlite($db_conf);
                        break;
                    default:
                        break;
                }
            } catch (\Exception $e) {
                $msg = "Database connect fail, check your database config for connection '$con_name' \n".$e->getMessage();
                Error::printError($msg);
            }
        }
    }

    /**
     * get db connection.
     *
     * @param string $con_name
     * @return object
     */
    public static function connection($con_name)
    {
        return self::$_connections[$con_name];
    }
}
