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
     * The route middleware map.
     *
     * @var array
     */
    protected static $_middleware_map_tree = [];

    /**
     * The variable route map.
     *
     * @var array
     */
    protected static $_variable_map_tree = []; 

    /**
     * The variable route cache, avoid foreach regexp match everytime.
     * Use route cache, time complexity from O(n) to O(1)
     *
     * @var array
     */
    protected static $_variable_route_cache = []; 

    /**
     * The variable route regexp mode.
     * accept letter, underscore. cannot start with a number 
     *
     * @var array
     */
    protected static $_variable_regexp = '/\{([a-z|A-Z|_]+[0-9]*)\}/';

    /**
     * The variable route regexp replacement.
     * accept letter, underscore, number
     *
     * @var array
     */
    protected static $_variable_replacement = '([0-9|a-z|A-Z|_]+)';

    /**
     * route config filter.
     *
     * @var array
     */
    protected static $_filter = [
        'prefix'     => '',
        'namespace'  => '',
        'middleware' => [],
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
        self::_setMapTree($method, $params[0], $params[1]);
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
        $middleware    = self::$_filter['middleware'];

        // set filter path prefix
        if(isset($filter['prefix'])) {
            self::$_filter['prefix'] .= '/'.$filter['prefix'].'/';
        }
        // set filter namespace prefix
        if(isset($filter['namespace'])) {
            self::$_filter['namespace'] .= '\\'.$filter['namespace'].'\\';
        }
        // set filter middleware
        if(isset($filter['middleware'])) {
            self::$_filter['middleware'][] = $filter['middleware'];
        }
        // call route setting
        call_user_func($routes);
        // recover sttribute
        self::$_filter['prefix']     = $tmp_prefix;
        self::$_filter['namespace']  = $tmp_namespace;
        self::$_filter['middleware'] = $middleware;
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
     * check route is variable route or not.
     *
     * @param  string  $path
     * @return boolean
     */
    protected static function _isVariableRoute($path)
    {
        $matched = [];

        preg_match_all(self::$_variable_regexp, $path, $matched);

        if (empty($matched[0])) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * replace variable route path to regexp string.
     *
     * @param  string  $path
     * @return boolean
     */
    protected static function _variablePathReplace($path)
    {
        return '@^'.preg_replace(self::$_variable_regexp, self::$_variable_replacement, $path).'$@';
    }

    /**
     * Parse variable route info (like params, route callback).
     *
     * @param  string  $path
     * @param  string  $method
     * @return mixed
     */
    protected static function _variablePathParse($path, $method)
    {
        // Why use array_reverse? Because if route is renamed, we want to use the last one
        $variable_paths = array_keys(array_reverse(self::$_variable_map_tree));
        
        $path_info = [];
        // variable route matched or not
        foreach ($variable_paths as $variable_path) {
            
            preg_match($variable_path, $path, $matched);

            if ( ! empty($matched)) {
                // get params    
                array_shift($matched);
                $path_info['params'] = $matched;
                // save variable path
                $path_info['callback'] = self::$_variable_map_tree[$variable_path][$method];
                // save middleware symbols
                $path_info['middleware'] = self::$_middleware_map_tree[$variable_path][$method];
                // stop find
                break;
            }
        }
    
        return empty($matched) ? NULL : $path_info;
    }  

    /**
     * set map tree.
     *
     * @param  string  $method
     * @param  string  $path
     * @param  mixed  $content
     * @return void
     */
    protected static function _setMapTree($method, $path, $content)
    {
        $path     = self::_pathParse(self::$_filter['prefix'].$path);
        $callback = is_string($content) ?
                    self::_namespaceParse('\\'.self::$_filter['namespace'].$content) : $content;
        
        if (self::_isVariableRoute($path)) { // is variable route
            $path = self::_variablePathReplace($path);
            self::$_variable_map_tree[$path][strtoupper($method)] = $callback;
        } else { // is usual route
            self::$_map_tree[$path][strtoupper($method)] = $callback;
        }

        self::$_middleware_map_tree[$path][strtoupper($method)] = self::$_filter['middleware'];  
    }

    /**
     * get redirect url
     * 
     * @param  string  $path
     * @param  array  $param
     * @return string
     */
    protected static function _getRedirectUrl($path, $param) 
    {
        $base_url = rtrim(Config::get('app.base_url'), '/');
        $path = self::_pathParse($path);
        $url = $base_url.$path.'?'.http_build_query($param);

        return $url;
    }

    /**
     * set map tree.
     *
     * @param  WorkerF\Http\Requests $request
     * @param  array $middleware_symbols
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected static function _checkMiddleware(Requests $request, $middleware_symbols)
    {
        // get all route middlewares
        $route_middlewares = Config::get('middleware.route');    
        // get current request route middlewares
        $request_middlewares = [];
        foreach ($middleware_symbols as $middleware_symbol) {
            // if middleware_symbol is not in route_middlewares
            if ( ! array_key_exists($middleware_symbol, $route_middlewares)) {
                throw new \InvalidArgumentException("route middleware $middleware_symbol is not exists!");
            }
            $request_middlewares[] = $route_middlewares[$middleware_symbol];  
        }
        // run middleware flow
        return Middleware::run($request_middlewares, $request);
    }

    /**
     * run dispatch.
     *
     * @param WorkerF\Http\Requests $request
     * @param mixed $callback
     * @param array $middleware_symbols
     * @param array $params
     * @return mixed
     * @throws \LogicException
     * @throws \BadMethodCallException
     * @throws \InvalidArgumentException
     */
    protected static function _runDispatch(Requests $request, $callback, $middleware_symbols, $params = [])
    {
        // check route middlewares
        $request = self::_checkMiddleware($request, $middleware_symbols);
        // if middlewares check is not passed
        if ( ! ($request instanceof Requests)) {
            return $request;
        }

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
            return IOCContainer::run($class, $method, $params);
        }
        // is callback
        if(is_callable($callback)) {
            // call function
            return call_user_func_array($callback, $params);
        }
    }

    /**
     * dispatch route.
     *
     * @param WorkerF\Http\Requests $request
     * @return mixed
     * @throws \LogicException
     * @throws \BadMethodCallException
     * @throws \InvalidArgumentException
     */
    public static function dispatch(Requests $request)
    {
        // get request param
        $path   = self::_pathParse($request->path());
        $method = $request->method();

        // router exist or not
        if(array_key_exists($path, self::$_map_tree) &&
           array_key_exists($method, self::$_map_tree[$path])
        ) {
            // get current request route middleware symbols
            $middleware_symbols = self::$_middleware_map_tree[$path][$method];
            // get callback info
            $callback = self::$_map_tree[$path][$method];
            
            return self::_runDispatch($request, $callback, $middleware_symbols);
        }

        // route in variable route cache or not
        if(array_key_exists($path, self::$_variable_route_cache) &&
           array_key_exists($method, self::$_variable_route_cache[$path])
        ) {
            // get variable route info
            $callback           = self::$_variable_route_cache[$path][$method]['callback'];
            $params             = self::$_variable_route_cache[$path][$method]['params'];
            $middleware_symbols = self::$_variable_route_cache[$path][$method]['middleware'];
            // dispatch variable route
            return self::_runDispatch($request, $callback, $middleware_symbols, $params);
        }

        // router is variable router or not
        if (NULL !== ($path_info = self::_variablePathParse($path, $method))) {
            // get variable route info
            $callback           = $path_info['callback'];
            $params             = $path_info['params'];
            $middleware_symbols = $path_info['middleware'];
            // save variable route to cache
            self::$_variable_route_cache[$path][$method]['callback']   = $callback;
            self::$_variable_route_cache[$path][$method]['params']     = $params;
            self::$_variable_route_cache[$path][$method]['middleware'] = $middleware_symbols;
            // dispatch variable route
            return self::_runDispatch($request, $callback, $middleware_symbols, $params);
        } 

        // route is not found
        $e = new \LogicException("route rule path: $path <==> method : $method is not set!");
        $e->httpCode = 404;
        throw $e; 
    }

    /**
     * redirect. warning: only for get method
     * 
     * @param  string  $path
     * @param  array  $param
     * @return \Closure
     */
    public static function redirect($path, $param = [])
    {
        $url = self::_getRedirectUrl($path, $param);
        
        return Response::redirect($url);
    }

}
