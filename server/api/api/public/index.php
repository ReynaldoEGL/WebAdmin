<?php

header(
    "Access-Control-Allow-Origin: *"
);

header(
    "Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"
);

header(
    "Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With"
);

if (
    $_SERVER['REQUEST_METHOD'] === 'OPTIONS'
) {
    http_response_code(204);
    exit;
}



require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../core/AuthMiddleware.php';



require_once
    __DIR__ . '/../resources/v1/UserResource.php';

require_once
    __DIR__ . '/../resources/v1/ProductResource.php';

require_once
    __DIR__ . '/../resources/v1/TareaResource.php';




require_once
    __DIR__ . '/../resources/v2/AuthResource.php';

require_once
    __DIR__ . '/../core/PasswordSecurity.php';

require_once
    __DIR__ . '/../models/PasswordSecurityUser.php';

require_once
    __DIR__ . '/../models/PasswordSecurityHistory.php';

require_once
    __DIR__ . '/../resources/password-security/PasswordSecurityResource.php';

require_once
    __DIR__ . '/../resources/password-security/PasswordAuthResource.php';

$scriptName =
    dirname($_SERVER['SCRIPT_NAME']);

$basePath =
    rtrim($scriptName, '/');


/*
|--------------------------------------------------------------------------
| V1
|--------------------------------------------------------------------------
| Sin autenticación
|--------------------------------------------------------------------------
*/

$routerV1 =
    new Router(
        'v1',
        $basePath
    );

$userResourceV1 =
    new UserResource();

$productResourceV1 =
    new ProductResource();


/*
|--------------------------------------------------------------------------
| USERS V1
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| PRODUCTS V1
|--------------------------------------------------------------------------
*/

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
| V2
|--------------------------------------------------------------------------
| Autenticada
|--------------------------------------------------------------------------
*/

$routerV2 =
    new Router(
        'v2',
        $basePath
    );

$authResourceV2 =
    new AuthResource();


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
| Público
|--------------------------------------------------------------------------
*/

$routerV2->addRoute(
    'POST',
    '/login',
    [$authResourceV2, 'login']
);


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

$routerV2->addRoute(
    'POST',
    '/logout',
    [$authResourceV2, 'logout'],
    ['AuthMiddleware::handle']
);


/*
|--------------------------------------------------------------------------
| ME
|--------------------------------------------------------------------------
*/

$routerV2->addRoute(
    'GET',
    '/me',
    [$authResourceV2, 'me'],
    ['AuthMiddleware::handle']
);


/*
|--------------------------------------------------------------------------
| USERS V2
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| PRODUCTS V2
|--------------------------------------------------------------------------
*/

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


$routerTareas =
    new Router(
        '',
        $basePath,
        ''
    );

$tareaResource =
    new TareaResource();


$routerTareas->addRoute(
    'GET',
    '/tareas',
    [$tareaResource, 'index']
);

$routerTareas->addRoute(
    'GET',
    '/tareas/{id}',
    [$tareaResource, 'show']
);

$routerTareas->addRoute(
    'POST',
    '/tareas',
    [$tareaResource, 'store']
);

$routerTareas->addRoute(
    'PUT',
    '/tareas/{id}',
    [$tareaResource, 'update']
);

$routerTareas->addRoute(
    'DELETE',
    '/tareas/{id}',
    [$tareaResource, 'destroy']
);

$routerPasswordSecurity =
    new Router(
        'v1',
        $basePath
    );

$passwordSecurityResource =
    new PasswordSecurityResource();

$passwordAuthResource =
    new PasswordAuthResource();

$routerPasswordSecurity->addRoute(
    'POST',
    '/passwords/generate',
    [
        $passwordSecurityResource,
        'generate'
    ]
);

$routerPasswordSecurity->addRoute(
    'POST',
    '/passwords/validate',
    [
        $passwordSecurityResource,
        'validate'
    ]
);

$routerPasswordSecurity->addRoute(
    'GET',
    '/passwords/policy',
    [
        $passwordSecurityResource,
        'policy'
    ]
);

$routerPasswordSecurity->addRoute(
    'POST',
    '/auth/register',
    [
        $passwordAuthResource,
        'register'
    ]
);

$routerPasswordSecurity->addRoute(
    'POST',
    '/auth/login',
    [
        $passwordAuthResource,
        'login'
    ]
);

/*
|--------------------------------------------------------------------------
| Selección del Router
|--------------------------------------------------------------------------
*/

$uri =
    parse_url(
        $_SERVER['REQUEST_URI'],
        PHP_URL_PATH
    );


if (
    preg_match(
        '#/api/v2(?:/|$)#',
        $uri
    )
) {

    $routerV2->dispatch();

} elseif (
    preg_match(
        '#^/tareas(?:/|$)#',
        $uri
    )
) {

    $routerTareas->dispatch();

}  elseif (
    preg_match(
        '#^/api/v1/(passwords|auth)(?:/|$)#',
        $uri
    )
) {

    $routerPasswordSecurity->dispatch();

} else {

    $routerV1->dispatch();
}

?>