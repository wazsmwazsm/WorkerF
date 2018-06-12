<?php
namespace WorkerF\Tests\Http;

use PHPUnit_Framework_TestCase;
use WorkerF\Http\Route;
use WorkerF\Http\Requests;
use WorkerF\Config;
use WorkerF\Http\MiddlewareInterface;

class RouteFake extends Route
{
    public static function getMapTree()
    {
        return self::$_map_tree;
    }

    public static function getMiddlewareMapTree()
    {
        return self::$_middleware_map_tree;
    }

    public static function setMapTree($method, $path, $content)
    {
        return self::_setMapTree($method, $path, $content);
    }  

    public static function cleanMapTree()
    {
        self::$_map_tree = [];
        self::$_middleware_map_tree = [];
    }

    public static function pathParse($path)
    {
        return self::_pathParse($path);
    }

    public static function namespaceParse($namespace)
    {
        return self::_namespaceParse($namespace);
    }

    public static function getRedirectUrl($path, $param)
    {
        return self::_getRedirectUrl($path, $param);
    }

    public static function checkMiddleware(Requests $request, $path, $method)
    {
        return self::_checkMiddleware($request, $path, $method);
    }
}

class Fuck
{
    public function bar()
    {
        return 'hello bar!';
    }

    public function getRequest(Requests $request)
    {
        return $request->request();
    }
}

class M3 implements MiddlewareInterface
{
    public function handle(Requests $request)
    {
        return $request;
    }
}

class M4 implements MiddlewareInterface
{
    public function handle(Requests $request)
    {
        return function() {
            return 'stop at m4!';
        };
    }
}


class RouteTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // clean map tree
        RouteFake::cleanMapTree();
        // init global variables
        $GLOBALS['HTTP_RAW_POST_DATA'] = '{"a":"test"}';
        $_REQUEST = ['foo' => 'bar', 'foz' => 'baz'];
        $_SERVER  = [
          'REQUEST_URI'    => 'http://test.com/pre/test?foo=bar',
          'REQUEST_METHOD' => 'GET',
        ];
        Config::set('middleware.route', []);
        
    }

    public function testUriParse()
    {
        $result = RouteFake::pathParse('usr//local///bin');

        $this->assertEquals('/usr/local/bin', $result);

        $result = RouteFake::pathParse('/');

        $this->assertEquals('/', $result);
    }

    public function testNamespaceParse()
    {
        $result = RouteFake::namespaceParse('\\a\\\\a\\\\\\g\d');

        $this->assertEquals("\\a\\a\\g\\d", $result);
    }

    public function testSetMapTree()
    {
        RouteFake::setMapTree('GET', '/test', 'TestController@test');

        $map = RouteFake::getMapTree();

        $this->assertEquals('\TestController@test', $map['/test']['GET']);

        RouteFake::setMapTree('GET', '/a', function() {
            return 'a';
        });

        $map = RouteFake::getMapTree();

        $this->assertEquals('a', $map['/a']['GET']());
    }

    public function testSetRoute()
    {
        // GET, callback
        RouteFake::get('/a', function() {
            return 'a';
        });

        $map = RouteFake::getMapTree();

        $this->assertEquals('a', $map['/a']['GET']());

        // GET, string
        RouteFake::get('/b', 'Test\Controller@get');

        $map = RouteFake::getMapTree();

        $this->assertEquals('\Test\Controller@get', $map['/b']['GET']);

        // POST
        RouteFake::post('/c', function() {
            return 'c';
        });

        $map = RouteFake::getMapTree();

        $this->assertEquals('c', $map['/c']['POST']());

        // PUT (same as DELETE\PATCH)
        RouteFake::put('/d/e', 'Test\Controller@get');

        $map = RouteFake::getMapTree();

        $this->assertEquals('\Test\Controller@get', $map['/d/e']['PUT']);

        // Middleware
        RouteFake::get('hello/', 'TestController@test');
        $middleware_map = RouteFake::getMiddlewareMapTree();
        $this->assertEquals([], $middleware_map['/hello']['GET']);
    }

    public function testGroup()
    {       
        RouteFake::group(['prefix' => '/pre', 'namespace' => 'App\Controller', 'middleware' => 'auth'], function() {
            RouteFake::get('control/', 'TestController@test');
            RouteFake::post('call1/', function() {
                return 'hello1';
            });
            RouteFake::get('call2/', function() {
                return 'hello2';
            });
        });

        $map = RouteFake::getMapTree();
        $middleware_map = RouteFake::getMiddlewareMapTree();

        $this->assertEquals('\App\Controller\TestController@test', $map['/pre/control']['GET']);
        $this->assertEquals('hello1', $map['/pre/call1']['POST']());
        $this->assertEquals('hello2', $map['/pre/call2']['GET']());

        $this->assertEquals(['auth'], $middleware_map['/pre/control']['GET']);
        $this->assertEquals(['auth'], $middleware_map['/pre/call1']['POST']);
        $this->assertEquals(['auth'], $middleware_map['/pre/call2']['GET']);

        // group nesting
        RouteFake::group(['prefix' => '/g1', 'namespace' => 'App', 'middleware' => 'auth'], function() {
            RouteFake::group(['prefix' => '/g2', 'namespace' => 'Controller', 'middleware' => 'jwt'], function() {
                RouteFake::get('test', function() {
                    return 'g1 g2 test success';
                });
                RouteFake::get('con', "TestController@test");
            });

            RouteFake::get('test', function() {
                return 'g1 test success';
            });
        });

        $map = RouteFake::getMapTree();
        $middleware_map = RouteFake::getMiddlewareMapTree();

        $this->assertEquals('g1 g2 test success', $map['/g1/g2/test']['GET']());
        $this->assertEquals('\App\Controller\TestController@test', $map['/g1/g2/con']['GET']);
        $this->assertEquals('g1 test success', $map['/g1/test']['GET']());

        $this->assertEquals(['auth'], $middleware_map['/g1/test']['GET']);
        $this->assertEquals(['auth', 'jwt'], $middleware_map['/g1/g2/test']['GET']);
        $this->assertEquals(['auth', 'jwt'], $middleware_map['/g1/g2/con']['GET']);

    }

    public function testCheckMiddleware()
    {
        // middleware check passed
        Config::set('middleware.route', ['auth' => 'WorkerF\Tests\Http\M3']);
        $request = new Requests();
        RouteFake::group(['prefix' => '/pre', 'middleware' => 'auth'], function() {    
            RouteFake::get('/test', 'WorkerF\Tests\Http\Fuck@bar');
        });    

        $result = RouteFake::checkMiddleware($request, '/pre/test', 'GET');

        $this->assertEquals($request, $result);
        
        // middleware check not passed
        Config::set('middleware.route', ['auth' => 'WorkerF\Tests\Http\M4']);
        $request = new Requests();
        RouteFake::group(['prefix' => '/pre', 'middleware' => 'auth'], function() {    
            RouteFake::get('/test', 'WorkerF\Tests\Http\Fuck@bar');
        });    

        $result = RouteFake::checkMiddleware($request, '/pre/test', 'GET');

        $this->assertInstanceOf('Closure', $result);
        $this->assertEquals('stop at m4!', call_user_func($result));
    }

    public function testDispatch()
    {
        $request = new Requests();

        RouteFake::get('/pre/test', 'WorkerF\Tests\Http\Fuck@bar');
        $result = RouteFake::dispatch($request);

        $this->assertEquals('hello bar!', $result);

        // class@method DI
        RouteFake::get('/pre/test', 'WorkerF\Tests\Http\Fuck@getRequest');
        $result = RouteFake::dispatch($request);

        $this->assertEquals((object) $_REQUEST, $result);

        // callback
        RouteFake::get('/pre/test', function($request) {
            return $request->foz;
        });
        $result = RouteFake::dispatch($request);

        $this->assertEquals('baz', $result);

        // with middleware
        // middleware check passed
        Config::set('middleware.route', ['auth' => 'WorkerF\Tests\Http\M3']);
        $request = new Requests();
        RouteFake::group(['prefix' => '/pre', 'middleware' => 'auth'], function() {    
            RouteFake::get('/test', 'WorkerF\Tests\Http\Fuck@bar');
        });    

        $result = RouteFake::dispatch($request);

        $this->assertEquals('hello bar!', $result);

        // middleware check not passed
        Config::set('middleware.route', ['auth' => 'WorkerF\Tests\Http\M4']);
        $request = new Requests();
        RouteFake::group(['prefix' => '/pre', 'middleware' => 'auth'], function() {    
            RouteFake::get('/test', 'WorkerF\Tests\Http\Fuck@bar');
        });    

        $result = RouteFake::dispatch($request);

        $this->assertInstanceOf('Closure', $result);
        $this->assertEquals('stop at m4!', call_user_func($result));
    }

    public function testGetRedirectUrl()
    {
        Config::set('app.base_url', 'http://test.com/');
        $url = RouteFake::getRedirectUrl('/pre/test', ['foo' => 1, 'bar' => 2]);

        $this->assertEquals('http://test.com/pre/test?foo=1&bar=2', $url);
    }

    /**
    * @expectedException \LogicException
    */
    public function testDispatchRouteNotSetException()
    {
        $request = new Requests();

        RouteFake::dispatch($request);
    }

    /**
    * @expectedException \LogicException
    */
    public function testDispatchMethodNotMatchException()
    {
        $request = new Requests();

        RouteFake::get('/pre/test', 'ssssss');
        RouteFake::dispatch($request);
    }

    /**
    * @expectedException \BadMethodCallException
    */
    public function testDispatchMethodNotFoundException()
    {
        $request = new Requests();

        RouteFake::get('/pre/test', 'Foz@baz');
        RouteFake::dispatch($request);
    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testCheckMiddlewareException()
    {
        // middleware check passed
        Config::set('middleware.route', ['auth' => 'WorkerF\Tests\Http\M3']);
        $request = new Requests();
        RouteFake::group(['prefix' => '/pre', 'middleware' => 'some'], function() {    
            RouteFake::get('/test', 'WorkerF\Tests\Http\Fuck@bar');
        });    

        $result = RouteFake::checkMiddleware($request, '/pre/test', 'GET');
    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testDispatchCheckMiddlewareException()
    {
        // middleware check passed
        Config::set('middleware.route', ['auth' => 'WorkerF\Tests\Http\M3']);
        $request = new Requests();
        RouteFake::group(['prefix' => '/pre', 'middleware' => 'some'], function() {    
            RouteFake::get('/test', 'WorkerF\Tests\Http\Fuck@bar');
        });    

        $result = RouteFake::dispatch($request);
    }
}
