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
    protected $_get;
    /**
     * post param.
     *
     * @var array
     */
    protected $_post;
    /**
     * request param.
     *
     * @var array
     */
    protected $_request;
    /**
     * server info.
     *
     * @var array
     */
    protected $_server;
    /**
     * cokkie info.
     *
     * @var array
     */
    protected $_cookie;
    /**
     * upload file info.
     *
     * @var array
     */
    protected $_files;

    /**
     * http request raw body data.
     *
     * @var string
     */
    protected $_rawData;

    /**
     * get http request param.
     *
     */
    public function __construct()
    {
        $this->_get     = (object) $_GET;
        $this->_post    = (object) $_POST;
        $this->_request = (object) $_REQUEST;
        $this->_server  = (object) $_SERVER;
        $this->_cookie  = (object) $_COOKIE;
        $this->_files   = (object) $_FILES;
        $this->_rawData = $GLOBALS['HTTP_RAW_POST_DATA'];
    }

    /**
     * return http request get data.
     *
     * @return object
     */
    public function get()
    {
        return $this->_get;
    }

    /**
     * return http request post data.
     *
     * @return object
     */
    public function post()
    {
        return $this->_post;
    }
    
    /**
     * return http request request data.
     *
     * @return object
     */
    public function request()
    {
        return $this->_request;
    }

    /**
     * return http request server data.
     *
     * @return object
     */
    public function server()
    {
        return $this->_server;
    }

    /**
     * return http request cookie data.
     *
     * @return object
     */
    public function cookie()
    {
        return $this->_cookie;
    }

    /**
     * return http request files data.
     *
     * @return object
     */
    public function files()
    {
        return $this->_files;
    }

    /**
     * return http request rawData data.
     *
     * @return string
     */
    public function rawData()
    {
        return $this->_rawData;
    }

    /**
     * return http request method.
     *
     * @return string
     */
    public function method()
    {
        return $this->_server->REQUEST_METHOD;
    }

    /**
     * check http request is https or not.
     *
     * @return boolean
     */
    public function isHttps()
    {
        if ((isset($this->_server->HTTPS) && $this->_server->HTTPS == 'on')) {
            return TRUE;
        } 
        
        if ((isset($this->_server->HTTP_X_FORWARDED_PROTO) && 
            $this->_server->HTTP_X_FORWARDED_PROTO == 'https')
        ) {
            return TRUE;
        }
        
        return FALSE;
    }

    /**
     * return http request url.
     *
     * @return string
     */
    public function url()
    {
        $protocol = $this->isHttps() ? 'https://' : 'http://';
        return $protocol.$this->_server->HTTP_HOST.parse_url(($this->_server->REQUEST_URI))['path'];
    }

    /**
     * return http request full url (with query string).
     *
     * @return string
     */
    public function fullUrl()
    {
        $protocol = $this->isHttps() ? 'https://' : 'http://';
        return $protocol.$this->_server->HTTP_HOST.$this->_server->REQUEST_URI;
    }

    /**
     * return http request path.
     *
     * @return string
     */
    public function path()
    {
        return parse_url(($request->_server->REQUEST_URI))['path'];
    }

    /**
     * return http request query string.
     *
     * @return string
     */
    public function queryString()
    {
        return $this->_server->QUERY_STRING;
    }

    /**
     * return http request ip.
     *
     * @return string
     */
    public function ip()
    {
        if (isset($this->_server->HTTP_X_FORWARDED_FOR)){
            return $this->_server->HTTP_X_FORWARDED_FOR;
        }

        if (isset($this->_server->HTTP_CLIENT_IP)) {
            return $this->_server->HTTP_CLIENT_IP;
        }

        return $this->_server->REMOTE_ADDR;
    }

    /**
     * Get an input element from the request.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->_request)) {
            return $this->_request->$key;
        }
        return NULL;
    }
}
