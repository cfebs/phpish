<?php

abstract class BaseResponse
{
    protected $_body;
    protected $_location;
    protected $_code = 200;
    protected $_headers = [];

    public function emit()
    {
        header_remove('X-Powered-By');
        http_response_code($this->_code);
        if ($this->_location) {
            header("Location: " . $this->_location);
            return;
        }

        foreach ($this->_headers as $key => $value) {
            header("$key: $value");

        }

        if (!empty($this->_body)) {
            echo $this->_body;
        }
    }

    // @chain
    public function setCode($code)
    {
        $this->_code = $code;
        return $this;
    }

    // @chain
    public function setHeader($key, $value)
    {
        $this->_headers[$key] = $value;
        return $this;
    }
}

class Response extends \BaseResponse
{
    public function __construct($content, $code = 200)
    {
        $this->setCode($code);
        $this->_body = $content;
    }
}

class JsonResponse extends \BaseResponse
{
    public function __construct($content)
    {
        $this->_body = json_encode($content);
        $this->setHeader('Content-Type', 'application/json');
    }
}

class RedirectResponse extends \BaseResponse
{
    public function __construct($location, $code = 302)
    {
        $this->setCode($code);
        $this->_location = $location;
    }
}
