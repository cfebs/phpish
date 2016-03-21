<?php

require_once './router.php';
require_once './controller.php';
require_once './blogcontroller.php';

$router = new Router();
$router->addStrict('/', [], function() {
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
});
