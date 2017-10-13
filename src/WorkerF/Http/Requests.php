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
     * @var array
     */
    public $get;
    /**
     * post param.
     *
     * @var array
     */
    public $post;
    /**
     * request param.
     *
     * @var array
     */
    public $requset;
    /**
     * server info.
     *
     * @var array
     */
    public $server;
    /**
     * cokkie info.
     *
     * @var array
     */
    public $cookie;
    /**
     * upload file info.
     *
     * @var array
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
