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
use WorkerF\Exceptions\ExceptionHandler;

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
            $conf = Config::get('app');
            // get request
            $request = new Requests();

            // check global middlewares
            $global_middlerwares = Config::get('middleware.global');
            $result = Middleware::run($global_middlerwares, $request);
            // middlewares check passed?
            if ($result instanceof Requests) {
                // run dispatch
                $result = Route::dispatch($result);
            }
            
        } catch (\Exception $e) {
            // Handle Exception
            $exceptionHandler = IOCContainer::getInstanceWithSingleton(ExceptionHandler::class);

            $result = $exceptionHandler->handle($e);
            
        } finally {
            // return Response data
            $response = Response::bulid($result, $conf);
            $con->send($response);
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
            // register class
            self::register();
            // init database
            DB::init(Config::get('database.db_con'));
            // init redis
            Redis::init(Config::get('database.redis'));

        } catch (\Exception $e) {
            Error::printError($e->getMessage());
        }
    }

    /**
     * register class
     *
     * @return void
     */
    public static function register()
    {
        // register middlewares
        $registers = Config::get('app.registers');

        if ( ! empty($registers)) {
            foreach ($registers as $register) {
                IOCContainer::register($register);
            }
        }
    }

}
