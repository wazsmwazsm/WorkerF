<?php

namespace WorkerF\Exceptions;

use WorkerF\Http\Response;
use WorkerF\Config;
use WorkerF\Error;

/**
 * ExceptionHandler.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class ExceptionHandler
{

    public function handle(\Exception $e)
    {
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

        return Error::errorHtml($e, $header, Config::get('app.debug'));
    }
}
