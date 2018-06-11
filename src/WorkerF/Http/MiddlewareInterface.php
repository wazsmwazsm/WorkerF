<?php
namespace WorkerF\Http;
use WorkerF\Http\Requests;

/**
 * MiddlewareInterface.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
interface MiddlewareInterface 
{
    /**
     * Middleware handle.
     *
     * @param WorkerF\Http\Requests $request
     * @return mixed
     */
    public function handle(Requests $request);
}
