<?php
use WorkerF\Http\Route;

class RouteFake extends Route
{
    public static function getMapTree($uri)
    {
        return self::$_map_tree;
    }

    public static function getFilter($uri)
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
}
