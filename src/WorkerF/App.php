<?php
namespace WorkerF;
use Workerman\Connection\TcpConnection;
use WorkerF\Http\Requests;
use WorkerF\Http\Response;
use WorkerF\Http\Route;
use WorkerF\Config;
use WorkerF\Error;
use WorkerF\DB\DB;
use WorkerF\DB\Redis;

/**
 * App.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class App
{
    /**
     * run http app.
     *
     * @param  Workerman\Connection\TcpConnection $con
     * @return void
     * @throws \LogicException
     * @throws \BadMethodCallException
     * @throws \InvalidArgumentException
     * @throws \PDOException
     */
    public static function run(TcpConnection $con)
    {
        try {
            // build config
            $conf = [];
            $conf['compress'] = Config::get('app.compress');
            // dispatch route, return Response data
            $response = Response::bulid(Route::dispatch(new Requests()), $conf);
            $con->send($response);

        } catch (\Exception $e) {
            // create http response header
            $httpCode = property_exists($e, 'httpCode') ? $e->httpCode : NULL;
            switch ($httpCode) {
                case 404:
                    $header = 'HTTP/1.1 404 Not Found';
                    break;

                default:
                    $header = 'HTTP/1.1 500 Internal Server Error';
                    Error::printError($e); // if Server error, echo to stdout
                    break;
            }

            Response::header($header);
            $con->send(Error::errorHtml($e, $header, Config::get('app.debug')));
        }

    }

    /**
     * Initialize some devices like redis \ database ...
     *
     * @return void
     * @throws WorkerF\DB\ConnectException
     */
    public static function init()
    {
        try {
            // init database
            DB::init(Config::get('database.db_con'));
            // init redis
            Redis::init(Config::get('database.redis'));

        } catch (\Exception $e) {
            Error::printError($e->getMessage());
        }

    }

}
