<?php
namespace WorkerF\Http;
use WorkerF\WorkerSocket\WorkerHttp;

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
            WorkerHttp::header("Content-Type: application/json;charset=utf-8");
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
        $header = WorkerHttp::getHeader('Content-Type');
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
                        WorkerHttp::header("Content-Encoding: gzip");
                        $compress_data = gzencode($data, $compress_conf['level']);
                        break;
                    case 'deflate':
                        WorkerHttp::header("Content-Encoding: deflate");
                        $compress_data = gzdeflate($data, $compress_conf['level']);
                        break;
                }
            }
        }

        return $compress_data;
    }

}
