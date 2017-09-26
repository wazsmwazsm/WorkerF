<?php
namespace WorkerF\Http;
use Workerman\Protocols\Http;
use Workerman\Protocols\HttpCache;
use WorkerF\Config;

/**
 * HTTP response.
 *
 * build content\header to HTTP ptotocol
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
Class Response
{

    /**
     * create http response header.
     *
     * @param  mixed  $header
     * @return void
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
        Http::header($headers);
    }

    /**
     * get http response status.
     *
     * @param  int  $code
     * @return String
     */
    public static function getHttpStatus($code)
    {

        return HttpCache::$codes[$code];
    }

    /**
     * build response data.
     *
     * @param  mixed  $data
     * @return String
     * @throws \InvalidArgumentException
     */
    public static function bulid($data)
    {
        // should be json
        if(is_array($data) || is_object($data)) {
            Http::header("Content-Type: application/json;charset=utf-8");
            return self::_compress(json_encode($data));
        }
        // is string (could be html string)
        if(is_string($data)) {
            return self::_compress($data);
        }
        // if return others, regard as illegal
        throw new \InvalidArgumentException("Controller return illegal data type!");
    }

    /**
     * compress data.
     *
     * @param  string  $data
     * @return string
     */
    private static function _compress($data)
    {

        $compress_data = $data;
        // get accept encodeing from request
        $accept_encodeing = explode(',', $_SERVER['HTTP_ACCEPT_ENCODING']);
        // get compress config
        $compress_conf = Config::get('app.compress');
        // get response headers Content-Type
        $content_type = isset(HttpCache::$header['Content-Type']) ?
                        HttpCache::$header['Content-Type'] : "Content-Type: text/html;charset=utf-8";

        foreach ($accept_encodeing as $key => $value) {
            $accept_encodeing[$key] = trim($value);
        }

        // is compress conf be accepted?
        if(in_array($compress_conf['encoding'], $accept_encodeing)) {
            // get content type string
            preg_match('/^Content-Type\:\s*(.+)\s*\;/', $content_type, $match);
            // is content type enable compress ?
            if(in_array($match[1], $compress_conf['content_type'])) {
                // check conf encodeing, enable compress
                switch ($compress_conf['encoding']) {
                    case 'gzip':
                        Http::header("Content-Encoding: gzip");
                        $compress_data = gzencode($data, $compress_conf['level']);
                        break;
                    case 'deflate':
                        Http::header("Content-Encoding: deflate");
                        $compress_data = gzdeflate($data, $compress_conf['level']);
                        break;
                }
            }
        }

        return $compress_data;
    }

}
