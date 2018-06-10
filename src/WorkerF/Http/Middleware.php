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

    public static function run(array $middlewares, Requests $request)
    {
        if (empty($middlewares)) {
            return $request;
        }

        $pipes = [];

        foreach ($middlewares as $middleware) {
            if (NULL === ($middleware_instance = IOCContainer::getSingleton($middleware))) {
                $middleware_instance = IOCContainer::getInstance($middleware);
                IOCContainer::singleton($middleware_instance);
            }

            $pipes[] = [$middleware_instance, 'handle'];
        }

        $pipeline = new Pipeline($pipes);

        return $pipeline->flow($request);
    }
}
