<?php
namespace WorkerF\Http;
use WorkerF\Http\Requests;
use WorkerF\Http\Response;
use WorkerF\IOCContainer;
use WorkerF\Config;
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
     * @var array
     */
    protected static $_map_tree = [];
    /**
     * route config filter.
     *
     * @var array
     */
    protected static $_filter = [
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
        // set map tree
        self::setMapTree($method, $params[0], $params[1]);
    }

    /**
     * set map tree.
     *
     * @param  string  $method
     * @param  string  $path
     * @param  mixed  $content
     * @return void
     */
    public static function setMapTree($method, $path, $content)
    {
        $path      = self::_pathParse(self::$_filter['prefix'].$path);
        $callback = is_string($content) ?
                    self::_namespaceParse('\\'.self::$_filter['namespace'].$content) : $content;

        self::$_map_tree[$path][strtoupper($method)] = $callback;
    }

    /**
     * set group route.
     *
     * @param  array    $filter
     * @param  \Closure  $routes
     * @return void
     */
    public static function group(array $filter, Closure $routes)
    {
        // save sttribute
        $tmp_prefix    = self::$_filter['prefix'];
        $tmp_namespace = self::$_filter['namespace'];

        // set filter path prefix
        if(isset($filter['prefix'])) {
            self::$_filter['prefix'] .= '/'.$filter['prefix'].'/';
        }
        // set filter namespace prefix
        if(isset($filter['namespace'])) {
            self::$_filter['namespace'] .= '\\'.$filter['namespace'].'\\';
        }
        // call route setting
        call_user_func($routes);
        // recover sttribute
        self::$_filter['prefix']    = $tmp_prefix;
        self::$_filter['namespace'] = $tmp_namespace;
    }

    /**
     * Parse path.
     *
     * @param  string  $path
     * @return string
     */
    protected static function _pathParse($path)
    {
        // make path as /a/b/c mode
        $path = ($path == '/') ? $path : '/'.rtrim($path, '/');
        $path = preg_replace('/\/+/', '/', $path);

        return $path;
    }

    /**
     * Parse namespace.
     *
     * @param  string  $namespace
     * @return string
     */
    protected static function _namespaceParse($namespace)
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
        $path = self::_pathParse(parse_url(($request->server->REQUEST_URI))['path']);
        $method = $request->server->REQUEST_METHOD;
        // router exist or not
        if( ! array_key_exists($path, self::$_map_tree) ||
            ! array_key_exists($method, self::$_map_tree[$path])
        )
        {
            $e = new \LogicException("route rule path: $path <==> method : $method is not set!");
            $e->httpCode = 404;
            throw $e;
        }
        // get callback info
        $callback = self::$_map_tree[$path][$method];

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
                $e = new \BadMethodCallException("Class@method: $callback is not found!");
                $e->httpCode = 404;
                throw $e;
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

    /**
     * redirect. warning: only for get method
     * 
     * @param  string  $path
     * @param  array  $param
     * @return void
     */
    public static function redirect($path, $param = [])
    {
        $base_url = rtrim(Config::get('app.base_url'), '/');
        $path = self::_pathParse($path);
        $url = $base_url.$path.'?'.http_build_query($param);
        
        return Response::redirect($url);
    }
}
