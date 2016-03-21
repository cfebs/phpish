<?php

require __DIR__ . '/vendor/autoload.php';
require_once './router.php';
require_once './request.php';
require_once './response.php';

try {
    $request = new Request($_GET, $_POST, $_SERVER);
    require_once './routes.php';
    $response = $router->run($request);

    if ($response) {
        return $response->emit();
    }
}
catch (Exception $e) {
    throw $e;
}

