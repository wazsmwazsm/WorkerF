<?php
namespace WorkerF\DB\Drivers;
use PDO;
use PDOException;
use Closure;

/**
 * PDODriver Driver
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class PDODriver implements ConnectorInterface
{
    /**
     * PDO instance
     *
     * @var \PDO
     */
    protected $_pdo = NULL;

    /**
     * PDOStatement instance
     *
     * @var \PDOStatement
     */
    protected $_pdoSt = NULL;

    /**
     * debug, set TRUE dump debug info to stdout
     *
     * @var boolean
     */
    protected $_debug = FALSE;

    /**
     * PDO connect config
     *
     * @var array
     */
    protected $_config = [];

    /**
     * operators
     *
     * @var array
     */
    protected $_operators = [
      '=', '<', '>', '<=', '>=', '<>', '!=',
      'like', 'not like',
      '&', '|', '<<', '>>',
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
        PDO::ATTR_STRINGIFY_FETCHES => FALSE,
        PDO::ATTR_EMULATE_PREPARES => FALSE,
    ];

    /**
     * build attribute list
     *
     * @var array
     */
    protected $_buildAttrs = [
      '_table',
      '_prepare_sql',
      '_cols_str',
      '_where_str',
      '_orderby_str',
      '_groupby_str',
      '_having_str',
      '_join_str',
      '_limit_str',
    ];

    /**
     * table name
     *
     * @var string
     */
    protected $_table = '';

    /**
     * sql sting
     *
     * @var string
     */
    protected $_prepare_sql = '';

    /**
     * sql sting
     *
     * @var string
     */
    protected $_cols_str = ' * ';

    /**
     * where sting
     *
     * @var string
     */
    protected $_where_str = '';

    /**
     * orderby sting
     *
     * @var string
     */
    protected $_orderby_str = '';

    /**
     * groupby sting
     *
     * @var string
     */
    protected $_groupby_str = '';

    /**
     * having sting
     *
     * @var string
     */
    protected $_having_str = '';

    /**
     * join sting
     *
     * @var string
     */
    protected $_join_str = '';

    /**
     * limit sting
     *
     * @var string
     */
    protected $_limit_str = '';

    /**
     * insert sting
     *
     * @var string
     */
    protected $_insert_str = '';

    /**
     * update sting
     *
     * @var string
     */
    protected $_update_str = '';

    /**
     * bind params list
     *
     * @var array
     */
    protected $_bind_params = [];

    /**
     * escape symbol
     *
     * @var array
     */
    protected static $_quote_symbol = '`';

    /**
     * construct , create a db connection
     *
     * @param string $config
     * @return  void
     * @throws  \PDOException
     */
    public function __construct($config)
    {
        $this->_config = $config;

        $this->_connect();
    }

    /**
     * create a PDO instance
     *
     * @return  void
     * @throws  \PDOException
     */
    protected function _connect()
    {
        extract($this->_config, EXTR_SKIP);

        $dsn = 'mysql:dbname='.$dbname.
               ';host='.$host.
               ';port='.$port;

        $options = isset($options) ? $options + $this->_options : $this->_options;

        try {

            $this->_pdo = new PDO($dsn, $user, $password, $options);

        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * close a db connection
     *
     * @return  void
     */
    protected function _closeConnection()
    {
        $this->_pdo = NULL;
    }

    /**
     * is pdo connect timeout
     *
     * @param   \PDOException $e
     * @return  boolean
     */
    protected function _isTimeout(PDOException $e)
    {
        return (
          $e->errorInfo[1] == 2006 ||   // MySQL server has gone away (CR_SERVER_GONE_ERROR)
          $e->errorInfo[1] == 2013 ||   // Lost connection to MySQL server during query (CR_SERVER_LOST)
          $e->errorInfo[1] == 7         // no connection to the server (for postgresql)
        );
    }

    /**
     * reset all attribute
     * memory-resident mode , need manual reset attr
     *
     * @return  void
     */
    protected function _reset()
    {
        $this->_table = '';
        $this->_prepare_sql = '';
        $this->_cols_str = ' * ';
        $this->_where_str = '';
        $this->_orderby_str = '';
        $this->_groupby_str = '';
        $this->_having_str = '';
        $this->_join_str = '';
        $this->_limit_str = '';
        $this->_insert_str = '';
        $this->_update_str = '';
        $this->_bind_params = [];
    }

    /**
     * reset build attr (for _subBuilder)
     *
     * @return  void
     */
    protected function _resetBuildAttr()
    {
        $this->_table = '';
        $this->_prepare_sql = '';
        $this->_cols_str = ' * ';
        $this->_where_str = '';
        $this->_orderby_str = '';
        $this->_groupby_str = '';
        $this->_having_str = '';
        $this->_join_str = '';
        $this->_limit_str = '';
    }

    /**
     * build query sql
     *
     * @return  void
     */
    protected function _buildQuery()
    {
        $this->_prepare_sql = 'SELECT '.$this->_cols_str.' FROM '.$this->_table.
            $this->_join_str.
            $this->_where_str.
            $this->_groupby_str.$this->_having_str.
            $this->_orderby_str.
            $this->_limit_str;
    }

    /**
     * build insert sql
     *
     * @return  void
     */
    protected function _buildInsert()
    {
        $this->_prepare_sql = 'INSERT INTO '.$this->_table.$this->_insert_str;
    }

    /**
     * build update sql
     *
     * @return  void
     */
    protected function _buildUpdate()
    {
        $this->_prepare_sql = 'UPDATE '.$this->_table.$this->_update_str.$this->_where_str;
    }

    /**
     * build delete sql
     *
     * @return  void
     */
    protected function _buildDelete()
    {
        $this->_prepare_sql = 'DELETE FROM '.$this->_table.$this->_where_str;
    }

    /**
     * wrap prepare sql
     *
     * @return  void
     */
    protected function _wrapPrepareSql()
    {
        // set table prefix
        $quote = static::$_quote_symbol;
        $prefix_pattern = '/'.$quote.'([a-zA-Z0-9_]+)'.$quote.'(\.)'.$quote.'([a-zA-Z0-9_]+)'.$quote.'/';
        $prefix_replace = self::_quote($this->_wrapTable('$1')).'$2'.self::_quote('$3');

        $this->_prepare_sql = preg_replace($prefix_pattern, $prefix_replace, $this->_prepare_sql);
    }

    /**
     * prepare and execute, if timeout, auto reconnect
     *
     * @return  void
     * @throws  \PDOException
     */
    protected function _execute()
    {
        try {
            $this->_wrapPrepareSql();
            $this->_pdoSt = $this->_pdo->prepare($this->_prepare_sql);
            $this->_bindParams();
            $this->_pdoSt->execute();
            $this->_reset();  // memory-resident mode, singleton pattern, need reset build attr
            // if debug mode, print sql and bind params to stdout
            if($this->_debug) {
                $this->_pdoSt->debugDumpParams();
                $this->_debug = FALSE; // close debug
            }
        } catch (PDOException $e) {
            // when time out, reconnect
            if($this->_isTimeout($e)) {
                $this->_closeConnection();
                $this->_connect();
                // retry
                try {
                    $this->_wrapPrepareSql();
                    $this->_pdoSt = $this->_pdo->prepare($this->_prepare_sql);
                    $this->_bindParams();
                    $this->_pdoSt->execute();
                    $this->_reset();
                    // if debug mode, print sql and bind params to stdout
                    if($this->_debug) {
                        $this->_pdoSt->debugDumpParams();
                        $this->_debug = FALSE; // close debug
                    }
                } catch (PDOException $e) {
                    throw $e;
                }

            } else {
                throw $e;
            }
        }

    }

    /**
     * bind all params
     *
     * @return  void
     */
    protected function _bindParams()
    {
        if(is_array($this->_bind_params)) {

            foreach ($this->_bind_params as $plh => $param) {

                $data_type = PDO::PARAM_STR;

                if(is_numeric($param)) {
                    $data_type = PDO::PARAM_INT;
                }

                if(is_null($param)) {
                    $data_type = PDO::PARAM_NULL;
                }

                if(is_bool($param)) {
                    $data_type = PDO::PARAM_BOOL;
                }

                $this->_pdoSt->bindValue($plh, $param, $data_type);
            }
        }
    }

    /**
     * set table prefix
     *
     * @param  string $table
     * @return  string
     */
    protected function _wrapTable($table)
    {
        $prefix = array_key_exists('prefix', $this->_config) ?
                $this->_config['prefix'] : '';

        return $prefix.$table;
    }

    /**
     * generate a placeholder
     *
     * @return  string
     */
    protected static function _getPlh()
    {
        return ':'.md5(uniqid(mt_rand(), TRUE));
    }

    /**
     * escape a reserved word
     *
     * @param   string  $word
     * @return  string
     */
    protected static function _quote($word)
    {
        return static::$_quote_symbol.$word.static::$_quote_symbol;
    }

    /**
     * add backquote sto field, support alias mode, prefix mode, func mode
     *
     * @param  string $str
     * @return  string
     */
    protected static function _wrapRow($str)
    {
        // match pattern
        $alias_pattern = '/([a-zA-Z0-9_\.]+)\s+(AS|as|As)\s+([a-zA-Z0-9_]+)/';
        $alias_replace = self::_quote('$1').' $2 '.self::_quote('$3');
        $prefix_pattern = '/([a-zA-Z0-9_]+\s*)(\.)(\s*[a-zA-Z0-9_]+)/';
        $prefix_replace = self::_quote('$1').'$2'.self::_quote('$3');
        $func_pattern = '/[a-zA-Z0-9_]+\([a-zA-Z0-9_\,\s\`\'\"\*]*\)/';
        // alias mode
        if(preg_match($alias_pattern, $str, $alias_match)) {
            // if field is aa.bb as cc mode
            if(preg_match($prefix_pattern, $alias_match[1])) {
                $pre_rst = preg_replace($prefix_pattern, $prefix_replace, $alias_match[1]);
                $alias_replace = $pre_rst.' $2 '.self::_quote('$3');
            }
            return preg_replace($alias_pattern, $alias_replace, $str);
        }
        // prefix mode
        if(preg_match($prefix_pattern, $str)) {
            return preg_replace($prefix_pattern, $prefix_replace, $str);
        }
        // func mode (do nothing)
        if(preg_match($func_pattern, $str)) {
            return $str;
        }
        // field mode
        return self::_quote($str);
    }

    /**
     * parse argurment, create build attr, store bind param
     *
     * @param  int $args_num
     * @param  array $params
     * @param  string &$construct_str
     * @return  void
     * @throws  \InvalidArgumentException
     */
    protected function _condition_constructor($args_num, $params, &$construct_str)
    {
        // params dose not conform to specification
        if( ! $args_num || $args_num > 3) {
            throw new \InvalidArgumentException("Error number of parameters");
        }
        // argurment mode
        switch ($args_num) {
          // assoc array mode
          case 1:
              if( ! is_array($params[0])) {
                  throw new \InvalidArgumentException($params[0].' should be Array');
              }
              $construct_str .= '(';
              foreach ($params[0] as $field => $value) {
                  $plh = self::_getPlh();
                  $construct_str .= ' '.self::_wrapRow($field).' = '.$plh.' AND';
                  $this->_bind_params[$plh] = $value;
              }
              // remove last operator
              $construct_str = substr($construct_str, 0, strrpos($construct_str, 'AND'));
              $construct_str .= ')';
              break;
          // ('a', 10) : a = 10 mode or ('a', null) : a is null mode
          case 2:
              if(is_null($params[1])) {
                  $construct_str .= ' '.self::_wrapRow($params[0]).' IS NULL ';
              } else {
                  $plh = self::_getPlh();
                  $construct_str .= ' '.self::_wrapRow($params[0]).' = '.$plh.' ';
                  $this->_bind_params[$plh] = $params[1];
              }
              break;
          // ('a', '>', 10) : a > 10 mode \ ('name', 'like', '%adam%') : name like '%adam%' mode
          case 3:
              if( ! in_array(strtolower($params[1]), $this->_operators)) {
                  throw new \InvalidArgumentException('Confusing Symbol '.$params[1]);
              }
              $plh = self::_getPlh();
              $construct_str .= ' '.self::_wrapRow($params[0]).' '.$params[1].' '.$plh.' ';
              $this->_bind_params[$plh] = $params[2];
              break;
        }
    }

    /**
     * store build attr to tmp
     *
     * @return  array
     */
    protected function _storeBuildAttr()
    {
        // attribute need to store
        $store = [];
        // store attr
        foreach ($this->_buildAttrs as $buildAttr) {
            $store[ltrim($buildAttr, '_')] = $this->$buildAttr;
        }

        return $store;
    }

    /**
     * restore build attr from tmp
     *
     * @param  array $data
     * @return  void
     */
    protected function _reStoreBuildAttr(array $data)
    {
        foreach ($this->_buildAttrs as $buildAttr) {
            $this->$buildAttr = $data[ltrim($buildAttr, '_')];
        }
    }

    /**
     * store bind params to tmp
     *
     * @return  array
     */
    protected function _storeBindParam()
    {
        return $this->_bind_params;
    }

    /**
     * restore bind params from tmp
     *
     * @param  array $data
     * @return  void
     */
    protected function _reStoreBindParam($bind_params)
    {
        $this->_bind_params = $bind_params;
    }

    /**
     * do sub build
     *
     * @param  \Closure $callback
     * @return  array
     * @throws  \InvalidArgumentException
     * @throws  \PDOException
     */
    protected function _subBuilder(Closure $callback)
    {
        // store build attr
        $store = $this->_storeBuildAttr();

        /**************** begin sub query build ****************/
            // empty attribute
            $this->_resetBuildAttr();
            // call sub query callback
            call_user_func($callback, $this);
            // get sub query build attr
            $sub_attr = [];

            $this->_buildQuery();

            foreach ($this->_buildAttrs as $buildAttr) {
                $sub_attr[ltrim($buildAttr, '_')] = $this->$buildAttr;
            }
        /**************** end sub query build ****************/

        // restore attribute
        $this->_reStoreBuildAttr($store);

        return $sub_attr;
    }

    /**
     * set table
     *
     * @param  string $table
     * @return  self
     */
    public function table($table)
    {
        $this->_table = self::_wrapRow($this->_wrapTable($table));

        return $this;
    }

    /**
     * get table
     *
     * @return string
     */
    public function getTable()
    {
        $pattern = '/['.static::$_quote_symbol.']+/';
        return preg_replace($pattern, '', $this->_table);
    }

    /**
     * set select cols
     *
     * @return  self
     */
    public function select()
    {
        $cols = func_get_args();

        if( ! func_num_args() || in_array('*', $cols)) {
            $this->_cols_str = ' * ';
        } else {
            // _cols_str default ' * ' , it easy to get a result when select func dosen't called
            // but when you call select func , you should set it to ''
            $this->_cols_str = '';
            foreach ($cols as $col) {
                $this->_cols_str .= ' '.self::_wrapRow($col).',';
            }
            $this->_cols_str = rtrim($this->_cols_str, ',');
        }

        return $this;
    }

    /**
     * build where string
     *
     * @return  self
     */
    public function where()
    {
        $operator = 'AND';
        // is the first time call where method ?
        if($this->_where_str == '') {
            $this->_where_str = ' WHERE ';
        } else {
            $this->_where_str .= ' '.$operator.' ';
        }
        // build attribute, bind params
        $this->_condition_constructor(func_num_args(), func_get_args(), $this->_where_str);

        return $this;
    }

    /**
     * build orWhere string
     *
     * @return  self
     */
    public function orWhere()
    {
        $operator = 'OR';
        // is the first time call where method ?
        if($this->_where_str == '') {
            $this->_where_str = ' WHERE ';
        } else {
            $this->_where_str .= ' '.$operator.' ';
        }
        // build attribute, bind params
        $this->_condition_constructor(func_num_args(), func_get_args(), $this->_where_str);

        return $this;
    }

    /**
     * build whereIn string
     *
     * @param  string $field
     * @param  array $data
     * @param  string $condition
     * @param  string $operator
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereIn($field, array $data, $condition = 'IN', $operator = 'AND')
    {
        if( ! in_array($condition, ['IN', 'NOT IN']) || ! in_array($operator, ['AND', 'OR'])) {
            throw new \InvalidArgumentException("Error whereIn mode");
        }
        // create placeholder
        foreach ($data as $key => $value) {
            $plh = self::_getPlh();
            $data[$key] = $plh;
            $this->_bind_params[$plh] = $value;
        }
        // is the first time call where method ?
        if($this->_where_str == '') {
            $this->_where_str = ' WHERE '.self::_wrapRow($field).' '.$condition.' ('.implode(',', $data).')';
        } else {
            $this->_where_str .= ' '.$operator.' '.self::_wrapRow($field).' '.$condition.' ('.implode(',', $data).')';
        }

        return $this;
    }

    /**
     * build orWhereIn string
     *
     * @param  string $field
     * @param  array $data
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereIn($field, array $data)
    {
        return $this->whereIn($field, $data, 'IN', 'OR');
    }

    /**
     * build whereNotIn string
     *
     * @param  string $field
     * @param  array $data
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereNotIn($field, array $data)
    {
        return $this->whereIn($field, $data, 'NOT IN', 'AND');
    }

    /**
     * build orWhereNotIn string
     *
     * @param  string $field
     * @param  array $data
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereNotIn($field, array $data)
    {
        return $this->whereIn($field, $data, 'NOT IN', 'OR');
    }

    /**
     * build whereBetween string
     *
     * @param  string $field
     * @param  int $start
     * @param  int $end
     * @param  string $operator
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereBetween($field, $start, $end, $operator = 'AND')
    {
        if( ! in_array($operator, ['AND', 'OR'])) {
            throw new \InvalidArgumentException("Logical operator");
        }
        // create placeholder
        $start_plh = self::_getPlh();
        $end_plh = self::_getPlh();
        $this->_bind_params[$start_plh] = $start;
        $this->_bind_params[$end_plh] = $end;

        // is the first time call where method ?
        if($this->_where_str == '') {
            $this->_where_str = ' WHERE '.self::_wrapRow($field).' BETWEEN '.$start_plh.' AND '.$end_plh;
        } else {
            $this->_where_str .= ' '.$operator.' '.self::_wrapRow($field).' BETWEEN '.$start_plh.' AND '.$end_plh;
        }

        return $this;
    }

    /**
     * build orWhereBetween string
     *
     * @param  string $field
     * @param  int $start
     * @param  int $end
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereBetween($field, $start, $end)
    {
        return $this->whereBetween($field, $start, $end, 'OR');
    }

    /**
     * build whereNull string
     *
     * @param  string $field
     * @param  string $condition
     * @param  string $operator
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereNull($field, $condition = 'NULL', $operator = 'AND')
    {
        if( ! in_array($operator, ['AND', 'OR'])) {
            throw new \InvalidArgumentException("Logical operator");
        }
        // is the first time call where method ?
        if($this->_where_str == '') {
            $this->_where_str = ' WHERE ';
        } else {
            $this->_where_str .= ' '.$operator.' ';
        }

        $this->_where_str .= self::_wrapRow($field).' IS '.$condition.' ';

        return $this;
    }

    /**
     * build whereNotNull string
     *
     * @param  string $field
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereNotNull($field)
    {
        return $this->whereNull($field, 'NOT NULL', 'AND');
    }

    /**
     * build orWhereNull string
     *
     * @param  string $field
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereNull($field)
    {
        return $this->whereNull($field, 'NULL', 'OR');
    }

    /**
     * build orWhereNotNull string
     *
     * @param  string $field
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereNotNull($field)
    {
        return $this->whereNull($field, 'NOT NULL', 'OR');
    }

    /**
     * build whereBrackets string
     *
     * @param  \Closure $callback
     * @param  string $operator
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereBrackets(Closure $callback, $operator = 'AND')
    {
        if( ! in_array($operator, ['AND', 'OR'])) {
            throw new \InvalidArgumentException("Logical operator");
        }
        // first time call where ?
        if($this->_where_str == '') {
            $this->_where_str = ' WHERE ( ';
        } else {
            $this->_where_str .= ' '.$operator.' ( ';
        }
        $sub_attr = $this->_subBuilder($callback);

        $this->_where_str .= preg_replace('/WHERE/', '', $sub_attr['where_str'], 1).' ) ';

        return $this;
    }

    /**
     * build orWhereBrackets string
     *
     * @param  \Closure $callback
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereBrackets(Closure $callback)
    {
        return $this->whereBrackets($callback, 'OR');
    }

    /**
     * build whereExists string
     *
     * @param  \Closure $callback
     * @param  string $condition
     * @param  string $operator
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereExists(Closure $callback, $condition = 'EXISTS', $operator = 'AND')
    {
        if( ! in_array($condition, ['EXISTS', 'NOT EXISTS']) || ! in_array($operator, ['AND', 'OR'])) {
            throw new \InvalidArgumentException("Error whereExists mode");
        }
        // first time call where ?
        if($this->_where_str == '') {
            $this->_where_str = ' WHERE '.$condition.' ( ';
        } else {
            $this->_where_str .= ' '.$operator.' '.$condition.' ( ';
        }

        $sub_attr = $this->_subBuilder($callback);
        $this->_where_str .= $sub_attr['prepare_sql'].' ) ';

        return $this;
    }

    /**
     * build whereNotExists string
     *
     * @param  \Closure $callback
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereNotExists(Closure $callback)
    {
        return $this->whereExists($callback, 'NOT EXISTS', 'AND');
    }

    /**
     * build orWhereExists string
     *
     * @param  \Closure $callback
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereExists(Closure $callback)
    {
        return $this->whereExists($callback, 'EXISTS', 'OR');
    }

    /**
     * build orWhereNotExists string
     *
     * @param  \Closure $callback
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereNotExists(Closure $callback)
    {
        return $this->whereExists($callback, 'NOT EXISTS', 'OR');
    }

    /**
     * build whereInSub string
     *
     * @param  string $field
     * @param  \Closure $callback
     * @param  string $condition
     * @param  string $operator
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereInSub($field, Closure $callback, $condition = 'IN', $operator = 'AND')
    {
        if( ! in_array($condition, ['IN', 'NOT IN']) || ! in_array($operator, ['AND', 'OR'])) {
            throw new \InvalidArgumentException("Error whereIn mode");
        }
        // first time call where ?
        if($this->_where_str == '') {
            $this->_where_str = ' WHERE '.self::_wrapRow($field).' '.$condition.' ( ';
        } else {
            $this->_where_str .= ' '.$operator.' '.self::_wrapRow($field).' '.$condition.' ( ';
        }

        $sub_attr = $this->_subBuilder($callback);
        $this->_where_str .= $sub_attr['prepare_sql'].' ) ';

        return $this;
    }

    /**
     * build whereNotInSub string
     *
     * @param  string $field
     * @param  \Closure $callback
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereNotInSub($field, Closure $callback)
    {
        return $this->whereInSub($field, $callback, 'NOT IN', 'AND');
    }

    /**
     * build orWhereInSub string
     *
     * @param  string $field
     * @param  \Closure $callback
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereInSub($field, Closure $callback)
    {
        return $this->whereInSub($field, $callback, 'IN', 'OR');
    }

    /**
     * build orWhereNotInSub string
     *
     * @param  string $field
     * @param  \Closure $callback
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereNotInSub($field, Closure $callback)
    {
        return $this->whereInSub($field, $callback, 'NOT IN', 'OR');
    }


    /**
     * build groupBy string
     *
     * @param  string $field
     * @return  self
     */
    public function groupBy($field)
    {
        // is the first time call groupBy method ?
        if($this->_groupby_str == '') {
            $this->_groupby_str = ' GROUP BY '.self::_wrapRow($field);
        } else {
            $this->_groupby_str .= ' , '.self::_wrapRow($field);
        }

        return $this;
    }

    /**
     * build having string
     *
     * @return  self
     */
    public function having()
    {
        $operator = 'AND';

        // is the first time call where method ?
        if($this->_having_str == '') {
            $this->_having_str = ' HAVING ';
        } else {
            $this->_having_str .= ' '.$operator.' ';
        }
        // build attribute, bind params
        $this->_condition_constructor(func_num_args(), func_get_args(), $this->_having_str);

        return $this;
    }

    /**
     * build orHaving string
     *
     * @return  self
     */
    public function orHaving()
    {
        $operator = 'OR';

        // is the first time call where method ?
        if($this->_having_str == '') {
            $this->_having_str = ' HAVING ';
        } else {
            $this->_having_str .= ' '.$operator.' ';
        }
        // build attribute, bind params
        $this->_condition_constructor(func_num_args(), func_get_args(), $this->_having_str);

        return $this;
    }

    /**
     * build having string raw
     *
     * @param   $string
     * @return  self
     */
    public function havingRaw($string)
    {
        $this->_having_str = ' HAVING '.$string.' ';

        return $this;
    }

    /**
     * build orderBy string
     *
     * @param  string $field
     * @param  string $mode
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orderBy($field, $mode = 'ASC')
    {
        if( ! in_array($mode, ['ASC', 'DESC'])) {
            throw new \InvalidArgumentException("Error order by mode");
        }
        // is the first time call orderBy method ?
        if($this->_orderby_str == '') {
            $this->_orderby_str = ' ORDER BY '.self::_wrapRow($field).' '.$mode;
        } else {
            $this->_orderby_str .= ' , '.self::_wrapRow($field).' '.$mode;
        }

        return $this;
    }

    /**
     * build join string
     *
     * @param  string $table
     * @param  string $one
     * @param  string $two
     * @param  string $type
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function join($table, $one, $two, $type = 'INNER')
    {
        if( ! in_array($type, ['INNER', 'LEFT', 'RIGHT'])) {
            throw new \InvalidArgumentException("Error join mode");
        }
        // set table prefix
        $table = $this->_wrapTable($table);
        // create join string
        $this->_join_str .= ' '.$type.' JOIN '.self::_wrapRow($table).
            ' ON '.self::_wrapRow($one).' = '.self::_wrapRow($two);
        return $this;
    }

    /**
     * build leftJoin string
     *
     * @param  string $table
     * @param  string $one
     * @param  string $two
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function leftJoin($table, $one, $two)
    {
        return $this->join($table, $one, $two, 'LEFT');
    }

    /**
     * build rightJoin string
     *
     * @param  string $table
     * @param  string $one
     * @param  string $two
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function rightJoin($table, $one, $two)
    {
        return $this->join($table, $one, $two, 'RIGHT');
    }

    /**
     * build rightJoin string
     *
     * @param  Closure $callback
     * @return  self
     * @throws  \InvalidArgumentException
     * @throws  \PDOException
     */
    public function fromSub(Closure $callback)
    {
        $sub_attr = $this->_subBuilder($callback);
        $this->_table .= ' ( '.$sub_attr['prepare_sql'].' ) AS tb_'.uniqid().' ';

        return $this;
    }

    /**
     * build limit string
     *
     * @param  int $offset
     * @param  int $step
     * @return  self
     */
    public function limit($offset, $step)
    {
        $this->_limit_str = ' LIMIT '.$step.' OFFSET '.$offset.' ';

        return $this;
    }

    /**
     * get paginate data
     *
     * @param  int $step
     * @param  int $page
     * @return  array
     * @throws  \PDOException
     */
    public function paginate($step, $page = NULL)
    {
        // store build attr\bind param
        $store = $this->_storeBuildAttr();
        $bind_params = $this->_storeBindParam();
        // get count
        $count = $this->count();
        // restore build attr\bind param
        $this->_reStoreBuildAttr($store);
        $this->_reStoreBindParam($bind_params);

        // create paginate data
        $page = $page ? $page : 1;
        $this->limit($step * ($page - 1), $step);

        $rst['total']        = $count;
        $rst['per_page']     = $step;
        $rst['current_page'] = $page;
        $rst['next_page']    = ($page + 1) > ($count / $step) ? NULL : ($page + 1);
        $rst['prev_page']    = ($page - 1) < 1 ? NULL : ($page - 1);
        $rst['first_page']   = 1;
        $rst['last_page']    = $count / $step;
        $rst['data']         = $this->get();

        return $rst;
    }

    /**
     * get assoc data
     *
     * @return  array
     * @throws  \PDOException
     */
    public function get()
    {
        $this->_buildQuery();
        $this->_execute();

        return $this->_pdoSt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * get assoc row data
     *
     * @return  array
     * @throws  \PDOException
     */
    public function row()
    {
        $this->_buildQuery();
        $this->_execute();

        return $this->_pdoSt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * get field list
     *
     * @param  string $field
     * @return  array
     * @throws  \PDOException
     */
    public function getList($field)
    {
        $this->_cols_str = ' '.self::_quote($field).' ';
        $this->_buildQuery();
        $this->_execute();

        return $this->_pdoSt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    /**
     * get count
     *
     * @param  string $field
     * @return  int
     * @throws  \PDOException
     */
    public function count($field = '*')
    {
        if(trim($field) != '*') {
            $field = self::_quote($field);
        }
        $this->_cols_str = ' COUNT('.$field.') AS count_num ';

        return $this->row()['count_num'];
    }

    /**
     * get sum
     *
     * @param  string $field
     * @return  int
     * @throws  \PDOException
     */
    public function sum($field)
    {
        $this->_cols_str = ' SUM('.self::_quote($field).') AS sum_num ';

        return $this->row()['sum_num'];
    }

    /**
     * get max
     *
     * @param  string $field
     * @return  int
     * @throws  \PDOException
     */
    public function max($field)
    {
        $this->_cols_str = ' MAX('.self::_quote($field).') AS max_num ';

        return $this->row()['max_num'];
    }

    /**
     * get min
     *
     * @param  string $field
     * @return  int
     * @throws  \PDOException
     */
    public function min($field)
    {
        $this->_cols_str = ' MIN('.self::_quote($field).') AS min_num ';

        return $this->row()['min_num'];
    }

    /**
     * get avg
     *
     * @param  string $field
     * @return  int
     * @throws  \PDOException
     */
    public function avg($field)
    {
        $this->_cols_str = ' AVG('.self::_quote($field).') AS avg_num ';

        return $this->row()['avg_num'];
    }

    /**
     * insert data
     *
     * @param  array $data
     * @return  null/int
     * @throws  \PDOException
     */
    public function insert(array $data)
    {
        // create build str
        $field_str = '';
        $value_str = '';
        foreach ($data as $key => $value) {
            $field_str .= ' '.self::_wrapRow($key).',';
            $plh = self::_getPlh();
            $this->_bind_params[$plh] = $value;
            $value_str .= ' '.$plh.',';
        }

        $field_str = rtrim($field_str, ',');
        $value_str = rtrim($value_str, ',');

        $this->_insert_str = ' ('.$field_str.') VALUES ('.$value_str.') ';
        // execute
        $this->_buildInsert();
        $this->_execute();

        return $this->_pdoSt->rowCount();
    }

    /**
     * insert data, get last insert ID, this method dosen't apply to postgresql
     *
     * @param  array $data
     * @return  null/int
     * @throws  \PDOException
     */
    public function insertGetLastId(array $data)
    {
        $this->insert($data);

        return $this->_pdo->lastInsertId();
    }

    /**
     * update data
     *
     * @param  array $data
     * @return  int
     * @throws  \PDOException
     */
    public function update(array $data)
    {
        // should not update without where
        if(empty($this->_where_str)) {
            throw new \InvalidArgumentException("Need where condition");
        }
        // create build str
        $this->_update_str = ' SET ';
        foreach ($data as $key => $value) {
            $plh = self::_getPlh();
            $this->_bind_params[$plh] = $value;
            $this->_update_str .= ' '.self::_wrapRow($key).' = '.$plh.',';
        }

        $this->_update_str = rtrim($this->_update_str, ',');

        $this->_buildUpdate();
        $this->_execute();

        return $this->_pdoSt->rowCount();
    }

    /**
     * delete data
     *
     * @return  int
     * @throws  \PDOException
     */
    public function delete()
    {
        // should not delete without where
        if(empty($this->_where_str)) {
            throw new \InvalidArgumentException("Need where condition");
        }

        $this->_buildDelete();
        $this->_execute();

        return $this->_pdoSt->rowCount();
    }

    /**
     * set debug to TRUE
     *
     * @return  self
     */
    public function withDebug()
    {
        $this->_debug = TRUE;

        return $this;
    }

    /**
     * native query, add auto reconnect
     *
     * @param  string $sql
     * @return  \PDOStatement/boolean
     * @throws  \PDOException
     */
    public function query($sql)
    {
        try {
            return $this->_pdo->query($sql);
        } catch (PDOException $e) {
            // when time out, reconnect
            if($this->_isTimeout($e)) {

                $this->_closeConnection();
                $this->_connect();

                try {
                    return $this->_pdo->query($sql);
                } catch (PDOException $e) {
                    throw $e;
                }

            } else {
                throw $e;
            }
        }
    }

    /**
     * native exec, add auto reconnect
     *
     * @param  string $sql
     * @return  int
     * @throws  \PDOException
     */
    public function exec($sql)
    {
        try {
            return $this->_pdo->exec($sql);
        } catch (PDOException $e) {
            // when time out, reconnect
            if($this->_isTimeout($e)) {

                $this->_closeConnection();
                $this->_connect();

                try {
                    return $this->_pdo->exec($sql);
                } catch (PDOException $e) {
                    throw $e;
                }

            } else {
                throw $e;
            }
        }
    }

    /**
     * native prepare, add auto reconnect
     *
     * @param  string $sql
     * @param  array $driver_options
     * @return  \PDOStatement/boolean
     * @throws  \PDOException
     */
    public function prepare($sql, array $driver_options = [])
    {
        try {
            return $this->_pdo->prepare($sql, $driver_options);
        } catch (PDOException $e) {
            // when time out, reconnect
            if($this->_isTimeout($e)) {

                $this->_closeConnection();
                $this->_connect();

                try {
                    return $this->_pdo->prepare($sql, $driver_options);
                } catch (PDOException $e) {
                    throw $e;
                }

            } else {
                throw $e;
            }
        }
    }

    /**
     * begin Transaction, add auto reconnect
     *
     * @return  boolean
     * @throws  \PDOException
     */
    public function beginTrans()
    {
        try {
            return $this->_pdo->beginTransaction();
        } catch (PDOException $e) {
            // when time out, reconnect
            if ($this->_isTimeout($e)) {

                $this->_closeConnection();
                $this->_connect();

                try {
                    return $this->_pdo->beginTransaction();
                } catch (PDOException $e) {
                    throw $e;
                }

            } else {
                throw $e;
            }
        }
    }

    /**
     * commit Transaction
     *
     * @return  boolean
     */
    public function commitTrans()
    {
        return $this->_pdo->commit();
    }

    /**
     * rollBack Transaction
     *
     * @return  boolean
     */
    public function rollBackTrans()
    {
        if ($this->_pdo->inTransaction()) {
            // if transaction already started, roll back
            return $this->_pdo->rollBack();
        }
    }

}
