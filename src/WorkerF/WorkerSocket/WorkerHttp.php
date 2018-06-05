<?php
namespace WorkerF\WorkerSocket;

use Workerman\Protocols\Http;
use Workerman\Protocols\HttpCache;

/**
 * WorkerHttp.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class WorkerHttp
{
    /**
     * create http response header.
     *
     * @param  mixed  $header
     * @return boolean
     */
    public static function header($headers)
    {
        if(is_array($headers)) {
            // if pass array
            foreach ($headers as $header) {
                Http::header($header);
            }
            return;
        }
        // pass string
        return Http::header($headers);
    }

    /**
     * get http response header.
     *
     * @param  string  $key
     * @return string
     */
    public static function getHeader($key)
    {
        if( ! array_key_exists($key, HttpCache::$header)) {
            return NULL;
        }

        return HttpCache::$header[$key];
    }

    /**
     * get http response status.
     *
     * @param  int  $code
     * @return string
     */
    public static function getHttpStatus($code)
    {
        return HttpCache::$codes[$code];
    }
}
