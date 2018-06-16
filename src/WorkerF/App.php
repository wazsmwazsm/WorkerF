<?php
namespace WorkerF;
use Workerman\Connection\TcpConnection;
use WorkerF\Http\Requests;
use WorkerF\Http\Response;
use WorkerF\Http\Route;
use WorkerF\Http\Middleware;
use WorkerF\IOCContainer;
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
            // get request
            $request = IOCContainer::getInstance('WorkerF\Http\Requests');
            
            // check global middlewares
            $global_middlerwares = Config::get('middleware.global');
            $request = Middleware::run($global_middlerwares, $request);
            // middlewares check passed?
            if ($request instanceof Requests) {
                // run dispatch
                $request = Route::dispatch($request);
            }
            
            // return Response data
            $response = Response::bulid($request, $conf);
            $con->send($response);

        } catch (\Exception $e) {
            $httpCode = 500;
            // create http response header
            if (property_exists($e, 'httpCode')) { // is a http exception
                $httpCode = $e->httpCode;
                $header = Response::$statusCodes[$httpCode];
            } else { // other exception
                $header = Response::$statusCodes[$httpCode];
                Error::printError($e); // if Server error, echo to stdout
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
