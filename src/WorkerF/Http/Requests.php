<?php
namespace WorkerF\Http;

/**
 * HTTP requests.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
Class Requests
{
    /**
     * get param.
     *
     * @var Array
     */
    public $get;
    /**
     * post param.
     *
     * @var Array
     */
    public $post;
    /**
     * request param.
     *
     * @var Array
     */
    public $requset;
    /**
     * server info.
     *
     * @var Array
     */
    public $server;
    /**
     * cokkie info.
     *
     * @var Array
     */
    public $cookie;
    /**
     * upload file info.
     *
     * @var Array
     */
    public $files;

    /**
     * get http request param.
     *
     */
    public function __construct()
    {
        $this->get     = (object) $_GET;
        $this->post    = (object) $_POST;
        $this->requset = (object) $_REQUEST;
        $this->server  = (object) $_SERVER;
        $this->cookie  = (object) $_COOKIE;
        $this->files   = (object) $_FILES;
    }

    /**
     * Get an input element from the request.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->requset)) {
            return $this->requset->$key;
        }
        return NULL;
    }
}
