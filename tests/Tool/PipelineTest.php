<?php
use WorkerF\Tool\Pipeline;

class PipelineFake extends Pipeline
{
    public function getPipes()
    {
        return $this->_pipes;
    }
}

class PipelineTest extends PHPUnit_Framework_TestCase
{
    public function testArrayMode()
    {
        $pipes = [
            function($payload) {
                return $payload + 1;
            },
            function($payload) {
                return $payload + 2;
            },
            function($payload) {
                return $payload + 3;
            },
        ];

        $pipeline = new PipelineFake($pipes);

        $this->assertEquals($pipes, $pipeline->getPipes());
    }
    

    public function testSingleMode()
    {

        $pipes = [
            function($payload) {
                return $payload * 2;
            },
            function($payload) {
                return $payload * 3;
            },
        ];

        $pipeline = new PipelineFake();

        $pipeline->pipe($pipes[0])->pipe($pipes[1]);

        $this->assertEquals($pipes, $pipeline->getPipes());
    }

    public function testFlow()
    {
        $pipes = [
            function($payload) {
                return $payload + 1;
            },
            function($payload) {
                return $payload + 2;
            },
            function($payload) {
                return $payload + 3;
            },
        ];

        $pipeline = new PipelineFake($pipes);

        $payload = 10;

        $this->assertEquals(16, $pipeline->flow($payload));
    }

    public function testFlowStop()
    {
        $pipes = [
            function($payload) {
                return $payload + 1;
            },
            function($payload) {
                // return Closure to stop pipeline flow
                return function() use($payload) {
                    return "stop: last value is $payload";
                };
            },
            function($payload) {
                return $payload + 3;
            },
        ];

        $pipeline = new PipelineFake($pipes);

        $payload = 10;

        $this->assertEquals('stop: last value is 11', $pipeline->flow($payload));
    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testPipeInvalidException1()
    {
        $pipeline = new PipelineFake();

        $pipeline->pipe("sss");
    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testPipeInvalidException2()
    {
        $pipes = [
            function($payload) {
                return $payload + 1;
            },
            2333,
            function($payload) {
                return $payload + 3;
            },
        ];

        $pipeline = new PipelineFake($pipes);
    }
}
