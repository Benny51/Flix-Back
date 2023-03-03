<?php

use App\Routing\Router;
use App\Routing\RoutingException;

require 'vendor/autoload.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE');
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header('Content-Type: application/json',"charset=utf-8");

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "OPTIONS") {
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
    header("HTTP/1.1 200 OK");
    die();
}

if(!isset($_GET['url']))
{
    die(json_encode("Aucune route n'a été entrée"));
}

$router = Router::getInstance($_GET['url']);

//Router : users
$router->post('/user/create/',"Users#create");
$router->get('/users/', "Users#getAll");


try {
    $router->run();
} catch (RoutingException $e) {
    die($e->getMessage());
}