<?php
namespace WorkerF\Http;
use WorkerF\WorkerSocket\WorkerHttp;
use Closure;

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
        WorkerHttp::header($headers);
    }
    
    /**
     * get http response header.
     *
     * @param  string  $key
     * @return string
     */
    public static function getHeader($key)
    {
        return WorkerHttp::getHeader($key);
    }

    /**
     * redirect.
     *
     * @param  string  $path
     * @return \Closure
     */
    public static function redirect($path)
    {    
        // return Closure 
        // to notice bulid method the type of response
        return function() use($path) {
            // run Location
            Response::header("Location: $path");

            return "redirect";
        };
    }

    /**
     * build response data.
     *
     * @param  mixed  $data
     * @param  array  $conf
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function bulid($data, array $conf)
    {
        // should be json
        if(is_array($data) || is_object($data)) {
            // Closure
            if($data instanceof Closure) {
                
                switch (call_user_func($data)) {
                    case 'redirect':
                        return 'link already redirected';
                        break;
                    
                    default:
                        return '';
                        break;
                }
            }
            // Array \ Object
            self::header("Content-Type: application/json;charset=utf-8");
            return self::_compress(json_encode($data), $conf['compress']);
        }
        // is string (could be html string)
        if(is_string($data)) {
            return self::_compress($data, $conf['compress']);
        }
        // if return others, regard as illegal
        throw new \InvalidArgumentException("Controller return illegal data type!");
    }

    /**
     * compress data.
     *
     * @param  string  $data
     * @param  array  $compress_conf
     * @return string
     */
    protected static function _compress($data, array $compress_conf)
    {
        $compress_data = $data;
        // get accept encodeing from request
        $accept_encodeing = explode(',', $_SERVER['HTTP_ACCEPT_ENCODING']);
        // get response headers Content-Type
        $header = self::getHeader('Content-Type');
        $content_type = $header ? $header : "Content-Type: text/html;charset=utf-8";

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
                        self::header("Content-Encoding: gzip");
                        $compress_data = gzencode($data, $compress_conf['level']);
                        break;
                    case 'deflate':
                        self::header("Content-Encoding: deflate");
                        $compress_data = gzdeflate($data, $compress_conf['level']);
                        break;
                }
            }
        }

        return $compress_data;
    }

}
