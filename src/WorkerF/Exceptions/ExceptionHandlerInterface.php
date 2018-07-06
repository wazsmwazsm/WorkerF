<?php
namespace WorkerF\Exceptions;

/**
 * ExceptionHandlerInterface.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
interface ExceptionHandlerInterface 
{
    /**
     * ExceptionHandler handle.
     *
     * @param \Exception $e
     * @return mixed
     */
    public function handle(\Exception $e);
}
