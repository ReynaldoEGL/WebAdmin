<?php

class Router
{
    private $routes = [];

    private $version;

    private $basePath;

    private $prefix;

    public function __construct(
        $version = 'v1',
        $basePath = '',
        $prefix = '/api'
    ) {
        $this->version =
            trim($version, '/');

        $this->basePath =
            rtrim($basePath, '/');

        $this->prefix =
            $prefix === ''
                ? ''
                : '/' . trim($prefix, '/');
    }

    public function addRoute(
        $method,
        $path,
        $handler,
        $middleware = []
    ) {
        $path =
            '/' . ltrim($path, '/');

        if ($this->prefix !== '') {

            $routePath =
                $this->prefix .
                '/' .
                $this->version .
                $path;

        } else {

            $routePath =
                $path;
        }

        $this->routes[] = [
            'method' =>
                strtoupper($method),

            'path' =>
                $routePath,

            'handler' =>
                $handler,

            'middleware' =>
                $middleware
        ];
    }

    public function dispatch()
    {
        $method =
            strtoupper(
                $_SERVER['REQUEST_METHOD']
            );

        $uri =
            parse_url(
                $_SERVER['REQUEST_URI'],
                PHP_URL_PATH
            );

        if (
            $uri === false ||
            $uri === null
        ) {
            $uri = '/';
        }

        if (
            !empty($this->basePath) &&
            strpos(
                $uri,
                $this->basePath
            ) === 0
        ) {

            $uri =
                substr(
                    $uri,
                    strlen($this->basePath)
                );
        }

        $uri =
            '/' . ltrim($uri, '/');

        foreach (
            $this->routes
            as $route
        ) {

            $pattern =
                preg_replace(
                    '/\{[a-zA-Z0-9_]+\}/',
                    '([a-zA-Z0-9_-]+)',
                    $route['path']
                );

            $pattern =
                '#^' .
                $pattern .
                '$#';

            if (
                $route['method'] === $method &&
                preg_match(
                    $pattern,
                    $uri,
                    $matches
                )
            ) {

                foreach (
                    $route['middleware']
                    as $middleware
                ) {

                    if (
                        !is_callable(
                            $middleware
                        )
                    ) {

                        http_response_code(500);

                        header(
                            'Content-Type: application/json'
                        );

                        echo json_encode([
                            'error' =>
                                'server_error',

                            'message' =>
                                'Middleware no válido'
                        ]);

                        return;
                    }

                    $result =
                        call_user_func(
                            $middleware
                        );

                    if ($result === false) {
                        return;
                    }
                }

                array_shift($matches);

                return call_user_func_array(
                    $route['handler'],
                    $matches
                );
            }
        }

        http_response_code(404);

        header(
            'Content-Type: application/json'
        );

        echo json_encode([
            'message' =>
                'Ruta no encontrada',

            'uri' =>
                $uri
        ]);
    }
}
?>