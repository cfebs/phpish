<?php

class BlogController extends \Controller
{
    public function __construct($request, $params)
    {
        $this->_request = $request;
        $this->_params = $params;
    }

    public function home()
    {
        $content = '<h1>Home!</h1>';
        return new Response($content);
    }

    public function redirect()
    {
        return new RedirectResponse('/', 301);
    }

    public function json()
    {
        $json = [
            'name' => 'Collin',
            'thing' => 5,
        ];
        return new JsonResponse($json, 400);
    }
}
