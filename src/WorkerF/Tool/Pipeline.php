<?php
namespace WorkerF\Tool;

class Pipeline
{

    protected $pipes = [];

    public function pipe($pipe) 
    {
        $this->pipes[] = $pipe;

        return $this;
    }

}
