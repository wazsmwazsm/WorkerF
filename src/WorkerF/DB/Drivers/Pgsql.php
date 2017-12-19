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
class Pgsql extends PDODriver implements ConnectorInterface
{

    /**
     * escape symbol
     *
     * @var array
     */
    protected static $_escape_symbol = '"';

    /**
     * operators
     *
     * @var array
     */
    protected $_operators = [
      '=', '<', '>', '<=', '>=', '<>', '!=',
      'like', 'not like', 'ilike', 'similar to', 'not similar to',
      '&', '|', '#', '<<', '>>',
    ];

    /**
     * The default PDO connection options.
     *
     * @var array
     */
    protected $_options = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
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

        $dsn = 'pgsql:dbname='.$dbname.
               (isset($host) ? ';host='.$host : '').
               (isset($port) ? ';port='.$port : '').
               (isset($sslmode) ? ';sslmode='.$sslmode : '');

        $options = isset($options) ? $options + $this->_options : $this->_options;

        try {

            $this->_pdo = new PDO($dsn, $user, $password, $options);

            // charset set
            if(isset($charset)) {
                $this->_pdo->prepare("set names '$charset'")->execute();
            }
            // timezone
            if(isset($timezone)) {
                $this->_pdo->prepare("set time zone '$timezone'")->execute();
            }
            // set schema path
            if(isset($schema)) {
                $this->_pdo->prepare("set search_path to $schema")->execute();
            }
            // set application name
            if(isset($application_name)) {
                $this->_pdo->prepare("set application_name to '$applicationName'")->execute();
            }
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * get last insert ID for postgresql
     *
     * @param  array $data
     * @return  null/int
     * @throws  \PDOException
     */
    public function insertGetLastId(array $data)
    {
        // create build str
        $field_str = '';
        $value_str = '';
        foreach ($data as $key => $value) {
            $field_str .= ' '.self::_backquote($key).',';
            $plh = self::_getPlh();
            $this->_bind_params[$plh] = $value;
            $value_str .= ' '.$plh.',';
        }

        $field_str = rtrim($field_str, ',');
        $value_str = rtrim($value_str, ',');

        $this->_insert_str = ' ('.$field_str.') VALUES ('.$value_str.') RETURNING id ';
        // execute
        $this->_buildInsert();
        $this->_execute();
        $result = $this->_pdoSt->fetch(PDO::FETCH_ASSOC);

        return $result['id'];
    }

}
