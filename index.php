<?php

require __DIR__ . '/vendor/autoload.php';
require_once './router.php';
require_once './request.php';
require_once './response.php';
require_once './controller.php';
require_once './blogcontroller.php';

try {
    $request = new Request($_GET, $_POST, $_SERVER);
    $router = new Router($request);

    $response = $router->addStrict('/', [], function() {
            echo "matched home!<br>";
        })
        ->addRegex('\/blog\/(cool[a-z])', [], function($r, $m) {
            dump($r, $m);
            echo "matched blog REGEX!<br>";
        })
        ->add('/blog/#id', [], function($r, $m) {
            dump($r, $m);
            echo "matched blog id NUM!<br>";
        })
        ->add('/blog/@id', [], function() {
            echo "matched blog id ALPHA!<br>";
        })
        ->add('/blog/:id', [], function() {
            echo "matched blog id MIXED!<br>";
        })
        ->add('/blog/*id', [], function() {
            echo "matched blog id ANY!<br>";
        })
        ->add('/blog', [], function() {
            echo "matched blogz!<br>";
        })
        ->add('/control/1', [], function($r, $p) {
            return (new BlogController($r, $p))->home();
        })
        ->add('/control/2', [], function($r, $p) {
            return (new BlogController($r, $p))->redirect();
        })
        ->add('/control/3', [], function($r, $p) {
            return (new BlogController($r, $p))->json();
        })
        ->run();

    if ($response) {
        return $response->emit();
    }

    echo 'complete';
} catch (Exception $e) {
    throw $e;
}

