<?php
require_once __DIR__.'/../vendor/autoload.php';

use App\Shared\Infrastructure\Bootstrap\EnvironmentLoader;
use App\Shared\Infrastructure\Di\ContainerConfig;
use App\Shared\Infrastructure\Router\Router;
use App\Shared\Infrastructure\Http\Response;

EnvironmentLoader::load();
$container = ContainerConfig::create();

$request_method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['PATH_INFO'] ?? $_SERVER['REQUEST_URI'];
$path_clean = explode('?',$path)[0];

$controller = Router::resolve($request_method,$path_clean);

if(!$controller){
    $response = new Response(
        msg: "Recurso no encontrado",
        status: "error"
    );
    $response->send(404);
} else {
    $instance = $container->get($controller);
    $instance->execute();
}

