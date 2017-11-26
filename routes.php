<?php
$router = new Router();

$routes = [
    $router->doRequest('avatar', 'get') => true,
];

if(!isset($routes[true])){
    $router->sendError();
}
