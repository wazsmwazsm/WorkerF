<?php
use WorkerF\Http\Route;
use WorkerF\Http\Requests;

class RouteFake extends Route
{
    public static function getMapTree()
    {
        return self::$_map_tree;
    }

    public static function cleanMapTree()
    {
        self::$_map_tree = [];
    }

    public static function pathParse($path)
    {
        return self::_pathParse($path);
    }

    public static function namespaceParse($namespace)
    {
        return self::_namespaceParse($namespace);
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
        return $request->request;
    }
}

class RouteTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // clean map tree
        RouteFake::cleanMapTree();
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

        // PUT
        RouteFake::put('/d/e', 'Test\Controller@get');

        $map = RouteFake::getMapTree();

        $this->assertEquals('\Test\Controller@get', $map['/d/e']['PUT']);
    }

    public function testGroup()
    {
        RouteFake::group(['prefix' => '/pre', 'namespace' => 'App\Controller'], function() {
            RouteFake::get('control/', 'TestController@test');
            RouteFake::post('call1/', function() {
                return 'hello1';
            });
            RouteFake::get('call2/', function() {
                return 'hello2';
            });
        });

        $map = RouteFake::getMapTree();

        $this->assertEquals('\App\Controller\TestController@test', $map['/pre/control']['GET']);
        $this->assertEquals('hello1', $map['/pre/call1']['POST']());
        $this->assertEquals('hello2', $map['/pre/call2']['GET']());

        // group nesting
        RouteFake::group(['prefix' => '/g1', 'namespace' => 'App'], function() {
            RouteFake::group(['prefix' => '/g2', 'namespace' => 'Controller'], function() {
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

        $this->assertEquals('g1 g2 test success', $map['/g1/g2/test']['GET']());
        $this->assertEquals('\App\Controller\TestController@test', $map['/g1/g2/con']['GET']);
        $this->assertEquals('g1 test success', $map['/g1/test']['GET']());

    }

    public function testDispatch()
    {
        // class@method
        $_REQUEST = (object) ['foo' => 'bar', 'foz' => 'baz'];
        $_SERVER  = (object) [
          'REQUEST_URI'    => 'http://test.com/pre/test?foo=bar',
          'REQUEST_METHOD' => 'GET',
        ];

        $request = new Requests();

        RouteFake::get('/pre/test', 'Fuck@bar');
        $result = RouteFake::dispatch($request);

        $this->assertEquals('hello bar!', $result);

        // class@method DI
        RouteFake::get('/pre/test', 'Fuck@getRequest');
        $result = RouteFake::dispatch($request);

        $this->assertEquals($_REQUEST, $result);

        // callback
        RouteFake::get('/pre/test', function($request) {
            return $request->foz;
        });
        $result = RouteFake::dispatch($request);

        $this->assertEquals('baz', $result);
    }

    /**
    * @expectedException \LogicException
    */
    public function testDispatchRouteNotSetException()
    {
        $_REQUEST = (object) ['foo' => 'bar', 'foz' => 'baz'];
        $_SERVER  = (object) [
          'REQUEST_URI'    => 'http://test.com/pre/test?foo=bar',
          'REQUEST_METHOD' => 'GET',
        ];

        $request = new Requests();

        RouteFake::dispatch($request);
    }

    /**
    * @expectedException \LogicException
    */
    public function testDispatchMethodNotMatchException()
    {
        $_REQUEST = (object) ['foo' => 'bar', 'foz' => 'baz'];
        $_SERVER  = (object) [
          'REQUEST_URI'    => 'http://test.com/pre/test?foo=bar',
          'REQUEST_METHOD' => 'GET',
        ];

        $request = new Requests();

        RouteFake::get('/pre/test', 'ssssss');
        RouteFake::dispatch($request);
    }

    /**
    * @expectedException \BadMethodCallException
    */
    public function testDispatchMethodNotFoundException()
    {
        $_REQUEST = (object) ['foo' => 'bar', 'foz' => 'baz'];
        $_SERVER  = (object) [
          'REQUEST_URI'    => 'http://test.com/pre/test?foo=bar',
          'REQUEST_METHOD' => 'GET',
        ];

        $request = new Requests();

        RouteFake::get('/pre/test', 'Foz@baz');
        RouteFake::dispatch($request);
    }
}
