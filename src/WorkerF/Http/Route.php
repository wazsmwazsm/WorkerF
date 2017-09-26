<?php
namespace WorkerF\Http;
use WorkerF\Http\Requests;
use WorkerF\IOCContainer;
use Closure;
/**
 * HTTP router.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class Route {
    /**
     * The route map.
     *
     * @var Array
     */
    private static $_map_tree = [];
    /**
     * route config filter.
     *
     * @var Array
     */
    private static $_filter = [
        'prefix'    => '',
        'namespace' => '',
    ];

    /**
     * call method (http methods).
     *
     * @param  string  $method
     * @param  mixed  $params
     * @return void
     * @throws \InvalidArgumentException run out of worker container, not catch, just crash
     */
    public static function __callstatic($method, $params)
    {
        // $param check
        if(count($params) !== 2) {
            // not catch, trigger Fatal error
            throw new \InvalidArgumentException("method $method accept 2 params!");
        }
        // create map tree, exp: $_map_tree['/a/b']['get'] = 'controller@method'
        $uri      = self::_uriParse(self::$_filter['prefix'].$params[0]);
        $callback = is_string($params[1]) ?
                    self::_namespaceParse(self::$_filter['namespace'].$params[1]) : $params[1];

        self::$_map_tree[$uri][strtoupper($method)] = $callback;
    }

    /**
     * set group route.
     *
     * @param  Array    $filter
     * @param  \Closure  $routes
     * @return void
     */
    public static function group(Array $filter, Closure $routes)
    {
        // set filter uri prefix
        if(isset($filter['prefix'])) {
            self::$_filter['prefix'] = '/'.$filter['prefix'].'/';
        }
        // set filter namespace prefix
        if(isset($filter['namespace'])) {
            self::$_filter['namespace'] = '\\'.$filter['namespace'].'\\';
        }
        // call route setting
        call_user_func($routes);
        // out the scope of the group method, empty filter
        self::$_filter['prefix'] = '';
        self::$_filter['namespace'] = '';
    }

    /**
     * Parse uri.
     *
     * @param  String  $uri
     * @return String
     */
    private static function _uriParse($uri)
    {
        // make uri as /a/b/c mode
        $uri = ($uri == '/') ? $uri : '/'.rtrim($uri, '/');
        $uri = preg_replace('/\/+/', '/', $uri);

        return $uri;
    }

    /**
     * Parse namespace.
     *
     * @param  String  $namespace
     * @return String
     */
    private static function _namespaceParse($namespace)
    {
        // make namespace as \a\b\c mode
        // why 4 '\' ? see php document preg_replace
        return preg_replace('/\\\\+/', '\\\\', $namespace);
    }

    /**
     * dispatch route.
     *
     * @return mixed
     * @throws \LogicException
     * @throws \BadMethodCallException
     */
    public static function dispatch(Requests $request)
    {
        // get request param
        $uri = self::_uriParse(parse_url(($request->server->REQUEST_URI))['path']);
        $method = $request->server->REQUEST_METHOD;
        // router exist or not
        if( ! isset(self::$_map_tree[$uri][$method])) {
            throw new \LogicException("route rule uri: $uri <==> method : $method is not set!", 404);
        }
        // get callback info
        $callback = self::$_map_tree[$uri][$method];

        // is class
        if(is_string($callback)) {
            // syntax check
            if( ! preg_match('/^[a-zA-Z0-9_\\\\]+@[a-zA-Z0-9_]+$/', $callback)) {
                throw new \LogicException("Please use controller@method define callback");
            }
            // get controller method info
            $controller = explode('@', $callback);
            list($class, $method) = [$controller[0], $controller[1]];
            // class methods exist ?
            if( ! class_exists($class) || ! method_exists($class, $method)) {
                throw new \BadMethodCallException("Class@method: $callback is not found!", 404);
            }
            // call method
            return IOCContainer::run($class, $method);
        }
        // is callback
        if(is_callable($callback)) {
            // call function
            return call_user_func($callback, $request);
        }
    }
}
