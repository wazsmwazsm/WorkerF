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
     * @param  mixed  $header array or string
     * @return void
     */
    public static function header($headers)
    {
        if(is_array($headers)) {
            // if pass array
            foreach ($headers as $header) {
                if(FALSE === Http::header($header)) {
                    throw new \InvalidArgumentException("Header $header is invalid!");
                }
            }
            return;
        }
        // pass string
        if(FALSE === Http::header($headers)) {
            throw new \InvalidArgumentException("Header $headers is invalid!");
        }
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
}
