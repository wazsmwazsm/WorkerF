<?php
namespace WorkerF\DB\Drivers;
use Closure;
/**
 * DB Driver interface
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
interface ConnectorInterface
{

    /**
     * construct , create a db connection
     *
     * @param string $host
     * @param string $port
     * @param string $user
     * @param string $password
     * @param string $dbname
     * @param string $charset
     * @return  void
     * @throws  \PDOException
     */
    public function __construct($host, $port, $user, $password, $dbname, $charset = 'utf8');

    /**
     * set table
     *
     * @param  string $table
     * @return  self
     */
    public function table($table);

    /**
     * set select cols
     *
     * @return  self
     */
    public function select();

    /**
     * build where string
     *
     * @return  self
     */
    public function where();

    /**
     * build orWhere string
     *
     * @return  self
     */
    public function orWhere();

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
    public function whereIn($field, array $data, $condition = 'IN', $operator = 'AND');

    /**
     * build orWhereIn string
     *
     * @param  string $field
     * @param  array $data
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereIn($field, array $data);

    /**
     * build whereNotIn string
     *
     * @param  string $field
     * @param  array $data
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereNotIn($field, array $data);

    /**
     * build orWhereNotIn string
     *
     * @param  string $field
     * @param  array $data
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereNotIn($field, array $data);

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
    public function whereBetween($field, $start, $end, $operator = 'AND');

    /**
     * build orWhereBetween string
     *
     * @param  string $field
     * @param  int $start
     * @param  int $end
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereBetween($field, $start, $end);

    /**
     * build whereNull string
     *
     * @param  string $field
     * @param  string $condition
     * @param  string $operator
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereNull($field, $condition = 'NULL', $operator = 'AND');

    /**
     * build whereNotNull string
     *
     * @param  string $field
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereNotNull($field);

    /**
     * build orWhereNull string
     *
     * @param  string $field
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereNull($field);

    /**
     * build orWhereNotNull string
     *
     * @param  string $field
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereNotNull($field);

    /**
     * build whereBrackets string
     *
     * @param  \Closure $callback
     * @param  string $operator
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereBrackets(Closure $callback, $operator = 'AND');

    /**
     * build orWhereBrackets string
     *
     * @param  \Closure $callback
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereBrackets(Closure $callback);

    /**
     * build whereExists string
     *
     * @param  \Closure $callback
     * @param  string $condition
     * @param  string $operator
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereExists(Closure $callback, $condition = 'EXISTS', $operator = 'AND');

    /**
     * build whereNotExists string
     *
     * @param  \Closure $callback
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereNotExists(Closure $callback);

    /**
     * build orWhereExists string
     *
     * @param  \Closure $callback
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereExists(Closure $callback);

    /**
     * build orWhereNotExists string
     *
     * @param  \Closure $callback
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereNotExists(Closure $callback);

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
    public function whereInSub($field, Closure $callback, $condition = 'IN', $operator = 'AND');

    /**
     * build whereNotInSub string
     *
     * @param  string $field
     * @param  \Closure $callback
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereNotInSub($field, Closure $callback);

    /**
     * build orWhereInSub string
     *
     * @param  string $field
     * @param  \Closure $callback
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereInSub($field, Closure $callback);

    /**
     * build orWhereNotInSub string
     *
     * @param  string $field
     * @param  \Closure $callback
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereNotInSub($field, Closure $callback);

    /**
     * build groupBy string
     *
     * @param  string $field
     * @return  self
     */
    public function groupBy($field);

    /**
     * build having string
     *
     * @return  self
     */
    public function having();

    /**
     * build orHaving string
     *
     * @return  self
     */
    public function orHaving();

    /**
     * build orderBy string
     *
     * @param  string $field
     * @param  string $mode
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orderBy($field, $mode = 'ASC');

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
    public function join($table, $one, $two, $type = 'INNER');

    /**
     * build leftJoin string
     *
     * @param  string $table
     * @param  string $one
     * @param  string $two
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function leftJoin($table, $one, $two);

    /**
     * build rightJoin string
     *
     * @param  string $table
     * @param  string $one
     * @param  string $two
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function rightJoin($table, $one, $two);

    /**
     * build rightJoin string
     *
     * @param  Closure $callback
     * @return  self
     * @throws  \InvalidArgumentException
     * @throws  \PDOException
     */
    public function fromSub(Closure $callback);

    /**
     * build limit string
     *
     * @param  int $offset
     * @param  int $step
     * @return  self
     */
    public function limit($offset, $step);

    /**
     * get paginate data
     *
     * @param  int $step
     * @param  int $page
     * @return  array
     * @throws  \PDOException
     */
    public function paginate($step, $page = NULL);

    /**
     * get assoc data
     *
     * @return  array
     * @throws  \PDOException
     */
    public function get();

    /**
     * get assoc row data
     *
     * @return  array
     * @throws  \PDOException
     */
    public function row();

    /**
     * get field list
     *
     * @param  string $field
     * @return  array
     * @throws  \PDOException
     */
    public function list($field);

    /**
     * get count
     *
     * @param  string $field
     * @return  int
     * @throws  \PDOException
     */
    public function count($field = '*');

    /**
     * get sum
     *
     * @param  string $field
     * @return  int
     * @throws  \PDOException
     */
    public function sum($field);

    /**
     * get max
     *
     * @param  string $field
     * @return  int
     * @throws  \PDOException
     */
    public function max($field);

    /**
     * get min
     *
     * @param  string $field
     * @return  int
     * @throws  \PDOException
     */
    public function min($field);

    /**
     * get avg
     *
     * @param  string $field
     * @return  int
     * @throws  \PDOException
     */
    public function avg($field);

    /**
     * insert data
     *
     * @param  array $data
     * @return  null/int
     * @throws  \PDOException
     */
    public function insert(array $data);

    /**
     * update data
     *
     * @param  array $data
     * @return  int
     * @throws  \PDOException
     */
    public function update(array $data);

    /**
     * delete data
     *
     * @return  int
     * @throws  \PDOException
     */
    public function delete();

    /**
     * set debug to TRUE
     *
     * @return  self
     */
    public function withDebug();

    /**
     * native query, add auto reconnect
     *
     * @param  string $sql
     * @return  \PDOStatement/boolean
     * @throws  \PDOException
     */
    public function query($sql);

    /**
     * native exec, add auto reconnect
     *
     * @param  string $sql
     * @return  int
     * @throws  \PDOException
     */
    public function exec($sql);

    /**
     * native prepare, add auto reconnect
     *
     * @param  string $sql
     * @param  array $driver_options
     * @return  \PDOStatement/boolean
     * @throws  \PDOException
     */
    public function prepare($sql, array $driver_options = []);

    /**
     * begin Transaction, add auto reconnect
     *
     * @return  boolean
     * @throws  \PDOException
     */
    public function beginTrans();

    /**
     * commit Transaction
     *
     * @return  boolean
     */
    public function commitTrans();

    /**
     * rollBack Transaction
     *
     * @return  boolean
     */
    public function rollBackTrans();
}
