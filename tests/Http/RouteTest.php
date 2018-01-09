<?php
use WorkerF\Http\Route;

class RouteFake extends Route
{
    public static function getMapTree()
    {
        return self::$_map_tree;
    }

    public static function getFilter()
    {
        return self::$_filter;
    }

    public static function uriParse($uri)
    {
        return self::_uriParse($uri);
    }

    public static function namespaceParse($namespace)
    {
        return self::_namespaceParse($namespace);
    }

}

class RouteTest extends PHPUnit_Framework_TestCase
{
    public function testUriParse()
    {
        $result = RouteFake::uriParse('usr//local///bin');

        $this->assertEquals('/usr/local/bin', $result);

        $result = RouteFake::uriParse('/');

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
}
