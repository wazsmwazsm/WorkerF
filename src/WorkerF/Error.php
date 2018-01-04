<?php
namespace WorkerF;

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
     * @var string
     */
    protected static $_html_blade = <<<EOF
<!DOCTYPE html>
<html>
  <head>
    <title>{{title}}</title>
    <style>
      body{width:35em;margin:0 auto;font-family:Tahoma,Verdana,Arial,sans-serif}
    </style>
  </head>
  <body>
    <center>
      <h1>{{header}}</h1>
      <div style="text-align:left;line-height:22px">{{exception}}</div>
    </center>
  </body>
</html>
EOF;
    /**
     * print error.
     *
     * @param  mixed $e
     * @return void
     */
    public static function printError($e)
    {
        echo '['.date('Y-m-d H:i:s', time()).']'."\n".$e."\n";
    }

    /**
     * return error html.
     *
     * @param  mixed $e
     * @param  int $header
     * @param  boolean $debug
     * @return string
     */
    public static function errorHtml($e, $header, $debug = TRUE)
    {
        $pattern = [
            '/\{\{title\}\}/',
            '/\{\{header\}\}/',
            '/\{\{exception\}\}/',
        ];

        $title = $header;
        $exception = $debug ? $e : 'something error...';
        $replacement = [$title, $header, $exception];

        return preg_replace($pattern, $replacement, self::$_html_blade);
    }
}
