<?php
namespace WorkerF;
use WorkerF\Config;
/**
 * Error.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class Error
{

    /**
     * response html.
     *
     * @var String
     */
    private static $_html_blade = '<html><head><title>{{title}}</title><style>'.
        'body{width:35em;margin:0 auto;font-family:Tahoma,Verdana,Arial,sans-serif}</style>'.
        '</head><body><center><h1>{{header}}</h1><div style="text-align:left;line-height:22px">{{exception}}</div></center></body></html>';

    /**
     * print error.
     *
     * @param  Mixed $e
     * @return void
     */
    public static function printError($e)
    {
        echo '['.date('Y-m-d H:i:s', time()).']'."\n".$e."\n";
    }

    /**
     * return error html.
     *
     * @param  Mixed $e
     * @param  int $header
     * @return String
     */
    public static function errorHtml($e, $header)
    {
        $pattern = [
            '/\{\{title\}\}/',
            '/\{\{header\}\}/',
            '/\{\{exception\}\}/',
        ];

        $title = $header;
        $exception = Config::get('app.debug') ? $e : 'something error...';
        $replacement = [$title, $header, $exception];

        return preg_replace($pattern, $replacement, self::$_html_blade);
    }
}
