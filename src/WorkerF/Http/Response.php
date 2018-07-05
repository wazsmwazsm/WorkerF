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
     * http Status codes => Status map.
     *
     * @var array
     */
    public static $statusCodes = array(
        100 => 'HTTP/1.1 100 Continue',
        101 => 'HTTP/1.1 101 Switching Protocols',
        200 => 'HTTP/1.1 200 OK',
        201 => 'HTTP/1.1 201 Created',
        202 => 'HTTP/1.1 202 Accepted',
        203 => 'HTTP/1.1 203 Non-Authoritative Information',
        204 => 'HTTP/1.1 204 No Content',
        205 => 'HTTP/1.1 205 Reset Content',
        206 => 'HTTP/1.1 206 Partial Content',
        300 => 'HTTP/1.1 300 Multiple Choices',
        301 => 'HTTP/1.1 301 Moved Permanently',
        302 => 'HTTP/1.1 302 Found',
        303 => 'HTTP/1.1 303 See Other',
        304 => 'HTTP/1.1 304 Not Modified',
        305 => 'HTTP/1.1 305 Use Proxy',
        306 => 'HTTP/1.1 306 (Unused)',
        307 => 'HTTP/1.1 307 Temporary Redirect',
        400 => 'HTTP/1.1 400 Bad Request',
        401 => 'HTTP/1.1 401 Unauthorized',
        402 => 'HTTP/1.1 402 Payment Required',
        403 => 'HTTP/1.1 403 Forbidden',
        404 => 'HTTP/1.1 404 Not Found',
        405 => 'HTTP/1.1 405 Method Not Allowed',
        406 => 'HTTP/1.1 406 Not Acceptable',
        407 => 'HTTP/1.1 407 Proxy Authentication Required',
        408 => 'HTTP/1.1 408 Request Timeout',
        409 => 'HTTP/1.1 409 Conflict',
        410 => 'HTTP/1.1 410 Gone',
        411 => 'HTTP/1.1 411 Length Required',
        412 => 'HTTP/1.1 412 Precondition Failed',
        413 => 'HTTP/1.1 413 Request Entity Too Large',
        414 => 'HTTP/1.1 414 Request-URI Too Long',
        415 => 'HTTP/1.1 415 Unsupported Media Type',
        416 => 'HTTP/1.1 416 Requested Range Not Satisfiable',
        417 => 'HTTP/1.1 417 Expectation Failed',
        422 => 'HTTP/1.1 422 Unprocessable Entity',
        423 => 'HTTP/1.1 423 Locked',
        500 => 'HTTP/1.1 500 Internal Server Error',
        501 => 'HTTP/1.1 501 Not Implemented',
        502 => 'HTTP/1.1 502 Bad Gateway',
        503 => 'HTTP/1.1 503 Service Unavailable',
        504 => 'HTTP/1.1 504 Gateway Timeout',
        505 => 'HTTP/1.1 505 HTTP Version Not Supported',
    );

    /**
     * create http response header.
     *
     * @param  mixed  $header array or string
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
                $result = call_user_func($data);
                // is redirect Closure 
                if ($result == 'redirect') {
                    return 'link already redirected';
                }

                return '';
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
            preg_match('/^Content-Type\:\s*([^;]+)\s*;?/', $content_type, $match);
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
