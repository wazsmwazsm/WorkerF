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
class Sqlite extends PDODriver implements ConnectorInterface
{
    /**
     * escape symbol
     *
     * @var array
     */
    protected static $_quote_symbol = '"';

    /**
     * operators
     *
     * @var array
     */
    protected $_operators = [
      '=', '<', '>', '<=', '>=', '<>', '!=',
      'like', 'not like', 'ilike',
      '&', '|', '<<', '>>',
    ];

    /**
     * create a PDO instance
     *
     * @return  void
     * @throws  \PDOException
     * @throws  \InvalidArgumentException
     */
    protected function _connect()
    {
        extract($this->_config, EXTR_SKIP);

        if ($dbname == ':memory:') {
            $dsn = 'sqlite::memory:';
        } else {
            $path = realpath($dbname);

            if ($path === FALSE) {
                throw new \InvalidArgumentException("Database $dbname does not exist.");
            }
            $dsn = 'sqlite:'.$path;
        }

        $options = isset($options) ? $options + $this->_options : $this->_options;

        try {

            $this->_pdo = new PDO($dsn, '', '', $options);

        } catch (PDOException $e) {
            throw $e;
        }
    }

}
