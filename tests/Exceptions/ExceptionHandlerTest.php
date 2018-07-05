<?php

namespace WorkerF\Tests\Exceptions;

use PHPUnit_Framework_TestCase;
use WorkerF\Exceptions\ExceptionHandler;
use WorkerF\Http\Response;
use WorkerF\Config;
use WorkerF\Error;

class ExceptionHandlerTest extends PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        // server error, online mode
        Config::set('app.debug', FALSE);

        $e = new \Exception('Hello, something wrong here!');
        $exceptionHandler = new ExceptionHandler();
        ob_start();
        $result = $exceptionHandler->handle($e);
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('HTTP/1.1 500 Internal Server Error', Response::getHeader('Http-Code'));
        $this->assertEquals('['.date('Y-m-d H:i:s', time()).']'."\n".$e."\n", $output);
        $this->assertEquals(Error::errorHtml($e, 'HTTP/1.1 500 Internal Server Error', FALSE), $result);

        // http error, debug mode
        Config::set('app.debug', TRUE);

        $e = new \Exception('Hello, page not found!');
        $e->httpCode = 404;
        $exceptionHandler = new ExceptionHandler();
        ob_start();
        $result = $exceptionHandler->handle($e);
        $output = ob_get_contents();
        ob_end_clean();
        
        $this->assertEquals('HTTP/1.1 404 Not Found', Response::getHeader('Http-Code'));
        $this->assertEquals('', $output);   
        $this->assertEquals(Error::errorHtml($e, 'HTTP/1.1 404 Not Found', TRUE), $result);
    }
}
