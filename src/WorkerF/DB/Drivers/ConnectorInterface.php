<?php

namespace WorkerF\DB\Drivers;

use Closure;

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
     * @param  String $table
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
     * @param  String $field
     * @param  Array $data
     * @param  String $condition
     * @param  String $operator
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereIn($field, Array $data, $condition = 'IN', $operator = 'AND');

    /**
     * build orWhereIn string
     *
     * @param  String $field
     * @param  Array $data
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereIn($field, Array $data);

    /**
     * build whereNotIn string
     *
     * @param  String $field
     * @param  Array $data
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereNotIn($field, Array $data);

    /**
     * build orWhereNotIn string
     *
     * @param  String $field
     * @param  Array $data
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereNotIn($field, Array $data);

    /**
     * build whereBetween string
     *
     * @param  String $field
     * @param  Int $start
     * @param  Int $end
     * @param  String $operator
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereBetween($field, $start, $end, $operator = 'AND');

    /**
     * build orWhereBetween string
     *
     * @param  String $field
     * @param  Int $start
     * @param  Int $end
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereBetween($field, $start, $end);

    /**
     * build whereNull string
     *
     * @param  String $field
     * @param  String $condition
     * @param  String $operator
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereNull($field, $condition = 'NULL', $operator = 'AND');

    /**
     * build whereNotNull string
     *
     * @param  String $field
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereNotNull($field);

    /**
     * build orWhereNull string
     *
     * @param  String $field
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereNull($field);

    /**
     * build orWhereNotNull string
     *
     * @param  String $field
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereNotNull($field);

    /**
     * build whereBrackets string
     *
     * @param  \Closure $callback
     * @param  String $operator
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
     * @param  String $condition
     * @param  String $operator
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
     * @param  String $field
     * @param  \Closure $callback
     * @param  String $condition
     * @param  String $operator
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereInSub($field, Closure $callback, $condition = 'IN', $operator = 'AND');

    /**
     * build whereNotInSub string
     *
     * @param  String $field
     * @param  \Closure $callback
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function whereNotInSub($field, Closure $callback);

    /**
     * build orWhereInSub string
     *
     * @param  String $field
     * @param  \Closure $callback
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereInSub($field, Closure $callback);

    /**
     * build orWhereNotInSub string
     *
     * @param  String $field
     * @param  \Closure $callback
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orWhereNotInSub($field, Closure $callback);

    /**
     * build groupBy string
     *
     * @param  String $field
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
     * @param  String $field
     * @param  String $mode
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function orderBy($field, $mode = 'ASC');

    /**
     * build join string
     *
     * @param  String $table
     * @param  String $one
     * @param  String $two
     * @param  String $type
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function join($table, $one, $two, $type = 'INNER');

    /**
     * build leftJoin string
     *
     * @param  String $table
     * @param  String $one
     * @param  String $two
     * @return  self
     * @throws  \InvalidArgumentException
     */
    public function leftJoin($table, $one, $two);

    /**
     * build rightJoin string
     *
     * @param  String $table
     * @param  String $one
     * @param  String $two
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
     * @param  Int $offset
     * @param  Int $step
     * @return  self
     */
    public function limit($offset, $step);

    /**
     * get paginate data
     *
     * @param  Int $step
     * @param  Int $page
     * @return  Array
     * @throws  \PDOException
     */
    public function paginate($step, $page = NULL);

    /**
     * get assoc data
     *
     * @return  Array
     * @throws  \PDOException
     */
    public function get();

    /**
     * get assoc row data
     *
     * @return  Array
     * @throws  \PDOException
     */
    public function row();

    /**
     * get field list
     *
     * @param  String $field
     * @return  Array
     * @throws  \PDOException
     */
    public function list($field);

    /**
     * get count
     *
     * @param  String $field
     * @return  Int
     * @throws  \PDOException
     */
    public function count($field = '*');

    /**
     * get sum
     *
     * @param  String $field
     * @return  Int
     * @throws  \PDOException
     */
    public function sum($field);

    /**
     * get max
     *
     * @param  String $field
     * @return  Int
     * @throws  \PDOException
     */
    public function max($field);

    /**
     * get min
     *
     * @param  String $field
     * @return  Int
     * @throws  \PDOException
     */
    public function min($field);

    /**
     * get avg
     *
     * @param  String $field
     * @return  Int
     * @throws  \PDOException
     */
    public function avg($field);

    /**
     * insert data
     *
     * @param  Array $data
     * @return  NULL/Int
     * @throws  \PDOException
     */
    public function insert(Array $data);

    /**
     * update data
     *
     * @param  Array $data
     * @return  Int
     * @throws  \PDOException
     */
    public function update(Array $data);

    /**
     * delete data
     *
     * @return  Int
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
     * @param  String $sql
     * @return  \PDOStatement/Boolean
     * @throws  \PDOException
     */
    public function query($sql);

    /**
     * native exec, add auto reconnect
     *
     * @param  String $sql
     * @return  Int
     * @throws  \PDOException
     */
    public function exec($sql);

    /**
     * native prepare, add auto reconnect
     *
     * @param  String $sql
     * @param  Array $driver_options
     * @return  \PDOStatement/Boolean
     * @throws  \PDOException
     */
    public function prepare($sql, Array $driver_options = []);

    /**
     * begin Transaction, add auto reconnect
     *
     * @return  Boolean
     * @throws  \PDOException
     */
    public function beginTrans();

    /**
     * commit Transaction
     *
     * @return  Boolean
     */
    public function commitTrans();

    /**
     * rollBack Transaction
     *
     * @return  Boolean
     */
    public function rollBackTrans();
}
