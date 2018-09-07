<?php
require_once '../autoload.php';
require_once 'router.config.php';

$router = new Router($router_config);
$content = $router->dispatch();

echo $content;