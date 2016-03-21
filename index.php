<?php

require __DIR__ . '/vendor/autoload.php';
require_once './router.php';
require_once './request.php';

try {
    $request = new Request($_GET, $_POST, $_SERVER);
    $router = new Router($request);

    $result = $router->addStrict('/', [], function() {
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
        ->run();

    echo 'complete';
} catch (Exception $e) {
    throw $e;
}

