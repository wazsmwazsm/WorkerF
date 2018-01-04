<?php
use WorkerF\Error;

class ErrorTest extends PHPUnit_Framework_TestCase
{

    public function testPrintError()
    {
        $e = new Exception('something error');
        ob_start();
        Error::printError($e);
        $result = ob_get_contents();
        ob_end_clean();

        $expect = '['.date('Y-m-d H:i:s', time()).']'."\n".$e."\n";

        $this->assertEquals($expect, $result);
    }

    public function testErrorHtml()
    {
        // with debug
        $e = new Exception('something error');
        $expect = <<<EOF
<!DOCTYPE html>
<html>
  <head>
    <title>HTTP/1.1 404 Not Found</title>
    <style>
      body{width:35em;margin:0 auto;font-family:Tahoma,Verdana,Arial,sans-serif}
    </style>
  </head>
  <body>
    <center>
      <h1>HTTP/1.1 404 Not Found</h1>
      <div style="text-align:left;line-height:22px">{$e}</div>
    </center>
  </body>
</html>
EOF;
        $result = Error::errorHtml($e, 'HTTP/1.1 404 Not Found');

        $this->assertEquals($expect, $result);

        // online
        $expect = <<<EOF
<!DOCTYPE html>
<html>
  <head>
    <title>HTTP/1.1 404 Not Found</title>
    <style>
      body{width:35em;margin:0 auto;font-family:Tahoma,Verdana,Arial,sans-serif}
    </style>
  </head>
  <body>
    <center>
      <h1>HTTP/1.1 404 Not Found</h1>
      <div style="text-align:left;line-height:22px">something error...</div>
    </center>
  </body>
</html>
EOF;
        $result = Error::errorHtml($e, 'HTTP/1.1 404 Not Found', FALSE);

        $this->assertEquals($expect, $result);
    }

}
