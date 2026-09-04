<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once '/../core/Router.php';
require_once '/../core/AuthMiddleware.php';

require_once '/../resources/v1/UserResource.php';
require_once '/../resources/v1/ProductResource.php';

require_once '/../resources/v2/AuthResource.php';

$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$basePath = $scriptName;


/*
|--------------------------------------------------------------------------
| V1 - SIN AUTENTICACIÓN
|--------------------------------------------------------------------------
*/

$routerV1 = new Router('v1', $basePath);

$userResourceV1 = new UserResource();
$productResourceV1 = new ProductResource();


// USERS V1

$routerV1->addRoute(
    'GET',
    '/users',
    [$userResourceV1, 'index']
);

$routerV1->addRoute(
    'GET',
    '/users/{id}',
    [$userResourceV1, 'show']
);

$routerV1->addRoute(
    'POST',
    '/users',
    [$userResourceV1, 'store']
);

$routerV1->addRoute(
    'PUT',
    '/users/{id}',
    [$userResourceV1, 'update']
);

$routerV1->addRoute(
    'DELETE',
    '/users/{id}',
    [$userResourceV1, 'destroy']
);


// PRODUCTS V1

$routerV1->addRoute(
    'GET',
    '/products',
    [$productResourceV1, 'index']
);

$routerV1->addRoute(
    'GET',
    '/products/{id}',
    [$productResourceV1, 'show']
);

$routerV1->addRoute(
    'POST',
    '/products',
    [$productResourceV1, 'store']
);

$routerV1->addRoute(
    'PUT',
    '/products/{id}',
    [$productResourceV1, 'update']
);

$routerV1->addRoute(
    'DELETE',
    '/products/{id}',
    [$productResourceV1, 'destroy']
);


/*
|--------------------------------------------------------------------------
| V2 - AUTENTICADA
|--------------------------------------------------------------------------
*/

$routerV2 = new Router('v2', $basePath);

$authResourceV2 = new AuthResource();


// LOGIN - PÚBLICO

$routerV2->addRoute(
    'POST',
    '/login',
    [$authResourceV2, 'login']
);


// LOGOUT - PROTEGIDO

$routerV2->addRoute(
    'POST',
    '/logout',
    [$authResourceV2, 'logout'],
    ['AuthMiddleware::handle']
);


// ME - PROTEGIDO

$routerV2->addRoute(
    'GET',
    '/me',
    [$authResourceV2, 'me'],
    ['AuthMiddleware::handle']
);


// USERS V2
// Se reutiliza UserResource.
// La diferencia es que aquí el Router agrega AuthMiddleware.

$routerV2->addRoute(
    'GET',
    '/users',
    [$userResourceV1, 'index'],
    ['AuthMiddleware::handle']
);

$routerV2->addRoute(
    'GET',
    '/users/{id}',
    [$userResourceV1, 'show'],
    ['AuthMiddleware::handle']
);

$routerV2->addRoute(
    'POST',
    '/users',
    [$userResourceV1, 'store'],
    ['AuthMiddleware::handle']
);

$routerV2->addRoute(
    'PUT',
    '/users/{id}',
    [$userResourceV1, 'update'],
    ['AuthMiddleware::handle']
);

$routerV2->addRoute(
    'DELETE',
    '/users/{id}',
    [$userResourceV1, 'destroy'],
    ['AuthMiddleware::handle']
);


// PRODUCTS V2
// Se reutiliza ProductResource con middleware.

$routerV2->addRoute(
    'GET',
    '/products',
    [$productResourceV1, 'index'],
    ['AuthMiddleware::handle']
);

$routerV2->addRoute(
    'GET',
    '/products/{id}',
    [$productResourceV1, 'show'],
    ['AuthMiddleware::handle']
);

$routerV2->addRoute(
    'POST',
    '/products',
    [$productResourceV1, 'store'],
    ['AuthMiddleware::handle']
);

$routerV2->addRoute(
    'PUT',
    '/products/{id}',
    [$productResourceV1, 'update'],
    ['AuthMiddleware::handle']
);

$routerV2->addRoute(
    'DELETE',
    '/products/{id}',
    [$productResourceV1, 'destroy'],
    ['AuthMiddleware::handle']
);


/*
|--------------------------------------------------------------------------
| Seleccionar versión
|--------------------------------------------------------------------------
*/

$uri = parse_url(
    $_SERVER['REQUEST_URI'],
    PHP_URL_PATH
);

if (strpos($uri, '/api/v2/') !== false) {
    $routerV2->dispatch();
} else {
    $routerV1->dispatch();
}

?>