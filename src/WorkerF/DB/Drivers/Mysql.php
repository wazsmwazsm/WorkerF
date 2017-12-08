<?php
namespace WorkerF\DB\Drivers;
use PDO;
use PDOException;
use WorkerF\DB\Drivers\PDODriver;

/**
 * Mysql Driver
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class Mysql extends PDODriver implements ConnectorInterface
{
    /**
     * escape symbol
     *
     * @var array
     */
    protected static $_escape_symbol = '`';

    /**
     * operators
     *
     * @var array
     */
    protected $_operators = [
      '=', '<', '>', '<=', '>=', '<>', '!=', '<=>',
      'like', 'not like', 'like binary', 'rlike', 'regexp', 'not regexp',
      '&', '|', '^', '<<', '>>',
    ];

    /**
     * create a PDO instance
     *
     * @return  void
     * @throws  \PDOException
     */
    protected function _connect()
    {
        extract($this->_config, EXTR_SKIP);

        $dsn = isset($unix_socket) ?
               'mysql:unix_socket='.$unix_socket.';dbname='.$dbname :
               'mysql:dbname='.$dbname.';host='.$host.(isset($port) ? ';port='.$port : '');

        $options = isset($options) ? $options + $this->_options : $this->_options;

        try {

            $this->_pdo = new PDO($dsn, $user, $password, $options);
            
            // charset set
            if(isset($charset)) {
                $this->_pdo->prepare("set names $charset ".(isset($collation) ? " collate '$collation'" : ''))->execute();
            }
            // timezone
            if(isset($timezone)) {
                $this->_pdo->prepare("set time_zone='$timezone'")->execute();
            }
            // strict mode
            if(isset($strict)) {
                if($strict) {
                    $this->_pdo->prepare("set session sql_mode='STRICT_ALL_TABLES'")->execute();
                } else {
                    $this->_pdo->prepare("set session sql_mode=''")->execute();
                }
            }
        } catch (PDOException $e) {
            throw $e;
        }
    }

}
