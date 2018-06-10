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
                  $singleton = self::getSingleton($class->name);
                  $di_params[] = $singleton ? $singleton : new $class->name();
              }
          }

          return $di_params;
      }

      /**
       * set a singleton instance.
       *
       * @param  object $instance
       * @return void
       * @throws \InvalidArgumentException
       */
      public static function singleton($instance)
      {
          if( ! is_object($instance)) {
              throw new \InvalidArgumentException("Object need!");
          }
          // singleton not exist, create
          if( ! array_key_exists(get_class($instance), self::$_singleton)) {
              self::$_singleton[get_class($instance)] = $instance;
          }
      }

      /**
       * get a singleton instance.
       *
       * @param  string $class_name
       * @return object
       */
      public static function getSingleton($class_name)
      {
          return array_key_exists($class_name, self::$_singleton) ?
                 self::$_singleton[$class_name] : NULL;
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
       * get Instance (Singleton) from reflection info.
       *
       * @param  string  $class_name
       * @return object
       */
      public static function getInstanceSingleton($class_name)
      {
          // instance already singleton
          if (NULL != ($instance = self::getSingleton($class_name))) {
              return $instance;
          }
          // create instance
          $instance = self::getInstance($class_name);
          // set singleton
          self::singleton($instance);

          return $instance;
      }

      /**
       * run class method.
       *
       * @param  string $class_name
       * @param  string $method
       * @return mixed
       * @throws \BadMethodCallException
       */
      public static function run($class_name, $method)
      {
          // class exist ?
          if( ! class_exists($class_name)) {
              throw new \BadMethodCallException("Class $class_name is not found!");
          }
          // method exist ?
          if( ! method_exists($class_name, $method)) {
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
          return call_user_func_array([$instance, $method], $di_params);
      }
 }
