<?php
namespace WorkerF\Http;
use WorkerF\Http\Requests;
use WorkerF\Tool\Pipeline;
use WorkerF\IOCContainer;

/**
 * Middleware.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class Middleware {
    /**
     * dispatch route.
     *
     * @param array $middlewares
     * @param WorkerF\Http\Requests $request
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public static function run(array $middlewares, Requests $request)
    {
        // if middlewares empty, return request as is
        if (empty($middlewares)) {
            return $request;
        }

        $pipes = [];

        foreach ($middlewares as $middleware) {
            // get instance
            $middleware_instance = IOCContainer::getInstance($middleware);
            // set singleton
            IOCContainer::singleton($middleware_instance);
            // create pipes array    
            $pipes[] = [$middleware_instance, 'handle'];
        }

        $pipeline = new Pipeline($pipes);
        // run pipes flow
        return $pipeline->flow($request);
    }
}
