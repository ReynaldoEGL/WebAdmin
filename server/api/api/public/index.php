<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../core/Router.php';
require_once '../resources/v1/ProductResource.php';

$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$basePath = $scriptName;

$router = new Router('v1', $basePath);

$ProductResource = new ProductResource();

// =========================
// Rutas de usuarios
// =========================

$router->addRoute('GET', '/products', [$ProductResource, 'index']);
$router->addRoute('GET', '/products/{id}', [$ProductResource, 'show']);
$router->addRoute('POST', '/products', [$ProductResource, 'store']);
$router->addRoute('PUT', '/products/{id}', [$ProductResource, 'update']);
$router->addRoute('DELETE', '/products/{id}', [$ProductResource, 'destroy']);

// =========================
// Rutas de productos
// =========================

$router->dispatch();

?>
