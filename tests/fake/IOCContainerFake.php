<?php

require __DIR__.'/../../vendor/autoload.php';

use WorkerF\IOCContainer;

class IOCContainerFake extends IOCContainer
{
    public static function getInstance(ReflectionClass $reflector)
    {
        return self::_getInstance($reflector);
    }

    public static function getDiParams(array $params)
    {
        return self::_getDiParams($reflector);
    }

}
