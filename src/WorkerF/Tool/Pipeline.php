<?php
namespace WorkerF\Tool;

use InvalidArgumentException;
use Closure;

/**
 * Pipeline, pipe mode.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class Pipeline
{
    /**
     * Pipes.
     *
     * @var array
     */
    protected $pipes = [];

    /**
     * set pipes, array mode.
     * 
     * @param $pipes
     * @throws \InvalidArgumentException
     */
    public function __construct($pipes = [])
    {
        foreach ($pipes as $pipe) {
            if (FALSE === is_callable($pipe)) {
                throw new InvalidArgumentException('All pipes should be callable.');
            }
        }
        $this->pipes = $pipes;
    }

    /**
     * set pipes, single mode.
     *
     * @param $pipe
     * @return self
     * @throws \InvalidArgumentException
     */
    public function pipe($pipe) 
    {
        if (FALSE === is_callable($pipe)) {
            throw new InvalidArgumentException('pipe should be callable.');
        }

        $this->pipes[] = $pipe;

        return $this;
    }

    /**
     * process pipeline flow, when payload passed as a closure, stop pipeline flow.
     *
     * @param $payload
     * @return mixed
     */
    public function flow($payload) 
    {
        foreach ($this->pipes as $pipe) {

            if ($payload instanceOf Closure) {
                // if payload is a closure, stop pipeline flow
                return call_user_func($payload);
            } 
            // process pipe
            $payload = call_user_func($pipe, $payload);
        }

        return $payload;
    }
}
