<?php
namespace WorkerF;

use ReflectionClass;

/**
 * Dependency injection IOC Container.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class IOCContainer
{
    /**
     * singleton instances.
     *
     * @var array
     */
    protected static $_singleton = [];   

    /**
     * create Dependency injection params.
     *
     * @param  array $params
     * @return array
     */
    protected static function _getDiParams(array $params)
    {
        $di_params = [];
        foreach ($params as $param) {
            $class = $param->getClass();
            if ($class) {
                // check dependency is a singleton instance or not
                $singleton = self::getSingleton($class->name);
                $di_params[] = $singleton ? $singleton : self::getInstance($class->name);
            }
        }

        return $di_params;
    }

    /**
     * set a singleton instance.
     *
     * @param  object $instance
     * @param  string $name 
     * @return void
     * @throws \InvalidArgumentException
     */
    public static function singleton($instance, $name = NULL)
    {
        if ( ! is_object($instance)) {
            throw new \InvalidArgumentException("Object need!");
        }

        $class_name = $name == NULL ? get_class($instance) : $name;

        // singleton not exist, create
        if ( ! array_key_exists($class_name, self::$_singleton)) {
            self::$_singleton[$class_name] = $instance;
        }
    }

    /**
     * get a singleton instance.
     *
     * @param  string $class_name
     * @return mixed object or NULL
     */
    public static function getSingleton($class_name)
    {
        return array_key_exists($class_name, self::$_singleton) ?
                self::$_singleton[$class_name] : NULL;
    }

    /**
     * unset a singleton instance.
     *
     * @param  string $class_name
     * @return void
     */
    public static function unsetSingleton($class_name)
    {
        self::$_singleton[$class_name] = NULL;
    }

    /**
     * register class, instantiate class, set instance to singleton.
     *
     * @param  string $abstract abstract class name
     * @param  string $concrete concrete class name, if NULL, use abstract class name
     * @return void
     */
    public static function register($abstract, $concrete = NULL)
    {
        if ($concrete == NULL) {
            $instance = self::getInstance($abstract);
            self::singleton($instance);
        } else {
            $instance = self::getInstance($concrete);
            self::singleton($instance, $abstract);
        }
    }

    /**
     * get Instance from reflection info.
     *
     * @param  string  $class_name
     * @return object
     */
    public static function getInstance($class_name)
    {
        // get class reflector
        $reflector = new ReflectionClass($class_name);
        // get constructor
        $constructor = $reflector->getConstructor();
        // create di params
        $di_params = $constructor ? self::_getDiParams($constructor->getParameters()) : [];
        // create instance
        return $reflector->newInstanceArgs($di_params);
    }

    /**
     * get Instance, if instance is not singleton, set it to singleton.
     *
     * @param  string  $class_name
     * @return object
     */
    public static function getInstanceWithSingleton($class_name)
    {
        // is a singleton instance?
        if (NULL != ($instance = self::getSingleton($class_name))) {
            return $instance;
        }

        $instance = self::getInstance($class_name);
        self::singleton($instance);

        return $instance;
    }

    /**
     * run class method.
     *
     * @param  string $class_name
     * @param  string $method
     * @param  array  $params
     * @return mixed
     * @throws \BadMethodCallException
     */
    public static function run($class_name, $method, $params = [])
    {
        // class exist ?
        if ( ! class_exists($class_name)) {
            throw new \BadMethodCallException("Class $class_name is not found!");
        }
        // method exist ?
        if ( ! method_exists($class_name, $method)) {
            throw new \BadMethodCallException("undefined method $method in $class_name !");
        }
        // create instance
        $instance = self::getInstance($class_name);
        /******* method Dependency injection *******/
        // get class reflector
        $reflector = new ReflectionClass($class_name);
        // get method
        $reflectorMethod = $reflector->getMethod($method);
        // create di params
        $di_params = self::_getDiParams($reflectorMethod->getParameters());
        // run method
        return call_user_func_array([$instance, $method], array_merge($di_params, $params));
    }
}
