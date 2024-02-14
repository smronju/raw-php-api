<?php

use App\Controllers\SubscriberController;

require_once 'bootstrap.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

if ($uri[1] !== 'subscriber') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

$requestMethod = $_SERVER['REQUEST_METHOD'];
$controller = new SubscriberController($requestMethod, $databaseConnection);

if ((isset($uri[1]) && $uri[1] === 'subscriber') && (isset($uri[2]) && $uri[2] === 'seed')) {
    $controller->seedDatabase();
} else {
    $controller->processRequest();
}


