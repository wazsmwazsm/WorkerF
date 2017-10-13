<?php
namespace WorkerF;

/**
 * Config.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class Config
{

    /**
     * config.
     *
     * @var array
     */
    private static $_config = [];

    /**
     * set config.
     *
     * @param  string  $file
     * @param  string  $conf
     * @return void
     */
    public static function set($file, $conf)
    {
        self::$_config[$file] = $conf;
    }

    /**
     * get config.
     *
     * @param  string  $key
     * @return mixed
     */
    public static function get($key)
    {
        $path = explode('.', $key);
        list($file, $conf) = [$path[0], $path[1]];

        return self::$_config[$file][$conf];
    }

    /**
     * get root path.
     *
     * @return string
     */
    public static function getRootPath()
    {
        return getcwd();
    }

}
