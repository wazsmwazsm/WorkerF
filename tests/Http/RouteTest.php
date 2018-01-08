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

        $this->assertEquals('Test\Controller@get', $map['/b']['GET']);

        // POST
        RouteFake::post('/c', function() {
            return 'c';
        });

        $map = RouteFake::getMapTree();

        $this->assertEquals('c', $map['/c']['POST']());

        // PUT
        RouteFake::put('/d', 'Test\Controller@get');

        $map = RouteFake::getMapTree();

        $this->assertEquals('Test\Controller@get', $map['/d']['PUT']);
    }
}
