<?php
namespace WorkerF\DB;
use WorkerF\Config;
use WorkerF\Error;
use Predis\Client;
use Closure;
/**
 * Redis.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class Redis
{
    /**
     * clients.
     *
     * @var array
     */
    private static $_clients;

    /**
     * init redis clients.
     *
     * @return void
     */
    public static function init()
    {
        // get redis config
        $rd_confs = Config::get('database.redis');
        // create redis init params
        $cluster = $rd_confs['cluster'];
        $options = (array) $rd_confs['options'];
        $servers = $rd_confs['rd_con'];
        // get clients
        self::$_clients = $cluster ?
            self::createAggregateClient($servers, $options) :
            self::createSingleClients($servers, $options);
        // check redis connect
        foreach (self::$_clients as $con_name => $client) {
            try {
                $client->connect();
            } catch (\Exception $e) {
                $msg = "Redis connect fail, check your redis config for connection '$con_name'. \n".$e->getMessage();
                Error::printError($msg);
            }
        }
    }

    /**
     * Get a specific Redis connection instance.
     *
     * @param  string  $con
     * @return \Predis\ClientInterface|null
     */
    public static function connection($con)
    {
        return self::$_clients[$con];
    }

    /**
     * Run a command against the Redis database.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public static function cmd($method, array $parameters = [])
    {
        return call_user_func_array([self::$_clients['default'], $method], $parameters);
    }

    /**
     * Create a new aggregate client supporting sharding.
     *
     * @param  array  $servers
     * @param  array  $options
     * @return array
     */
    public static function createAggregateClient(array $servers, array $options = [])
    {
        return ['default' => new Client(array_values($servers), $options)];
    }

    /**
     * Create an array of single connection clients.
     *
     * @param  array  $servers
     * @param  array  $options
     * @return array
     */
    public static function createSingleClients(array $servers, array $options = [])
    {
        $clients = [];

        foreach ($servers as $key => $server) {
            $clients[$key] = new Client($server, $options);
        }

        return $clients;
    }

    /**
     * Subscribe to a set of given channels for messages.
     * 'read_write_timeout' => 0 to solve the timeout problem
     *
     * @param  array|string  $channels
     * @param  \Closure  $callback
     * @param  string  $connection
     * @param  string  $method
     * @return void
     */
    public static function subscribe($channels, Closure $callback, $connection = 'default', $method = 'subscribe')
    {
        $loop = self::$_clients[$connection]->pubSubLoop();

        call_user_func_array([$loop, $method], (array) $channels);

        // loop blocking, start listen redis publish messages
        foreach ($loop as $message) {
            if ($message->kind === 'message' || $message->kind === 'pmessage') {
                // get publish message
                call_user_func($callback, $message->payload, $message->channel);
            }
        }
        // unset Predis\PubSub\Consumer iterator
        unset($loop);
    }

    /**
     * Subscribe to a set of given channels with wildcards.
     *
     * @param  array|string  $channels
     * @param  \Closure  $callback
     * @param  string  $connection
     * @return void
     */
    public static function psubscribe($channels, Closure $callback, $connection = 'default')
    {
        return self::subscribe($channels, $callback, $connection, 'psubscribe');
    }

    /**
     * call method (predis methods).
     *
     * @param  string  $method
     * @param  mixed  $params
     * @return void
     */
    public static function __callstatic($method, $params)
    {
        return call_user_func_array([self::$_clients['default'], $method], $params);
    }

}
