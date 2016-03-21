<?php

class Request
{
    protected $_get;
    protected $_post;
    protected $_server;

    public function __construct($get, $post, $server)
    {
        $this->_get = $get;
        $this->_post = $post;
        $this->_server = $server;
    }

    public function getParam($param)
    {
        if (isset($this->_get[$param])) {
            return $this->_get[$param];
        }

        if (isset($this->_post[$param])) {
            return $this->_post[$param];
        }
    }

    public function getMethod()
    {
        return $this->_server['REQUEST_METHOD'];
    }

    public function getIp()
    {
        return $this->_server['REMOTE_ADDR'];
    }

    public function getUri()
    {
        return $this->_server['REQUEST_URI'];
    }

    public function getReferer()
    {
        return $this->_server['HTTP_HOST'];
    }

    public function getUserAgent()
    {
        return $this->_server['HTTP_USER_AGENT'];
    }

    public function getServerName()
    {
        return $this->_server['SERVER_NAME'];
    }
}
