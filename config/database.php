<?php

declare(strict_types=1);

/**
 * Carga las variables de configuración desde .env.
 *
 * El archivo .env se encuentra en:
 * /home/usuario/public_html/.env
 */
function loadEnv(string $path): array
{
    if (!is_readable($path)) {
        throw new RuntimeException(
            'No se puede leer el archivo de configuración de la base de datos.'
        );
    }

    $variables = [];

    $lines = file(
        $path,
        FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
    );

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        [$name, $value] = array_pad(
            explode('=', $line, 2),
            2,
            ''
        );

        $variables[trim($name)] = trim($value);
    }

    return $variables;
}

/**
 * Crea una conexión PDO con la base de datos.
 */
function getDatabaseConnection(): PDO
{
    $envPath = dirname(__DIR__) . '/.env';

    $env = loadEnv($envPath);

    $required = [
        'DB_HOST',
        'DB_USER',
        'DB_PASS',
        'DB_NAME',
    ];

    foreach ($required as $variable) {
        if (!array_key_exists($variable, $env)) {
            throw new RuntimeException(
                "Falta la variable {$variable} en .env."
            );
        }
    }

    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=utf8mb4',
        $env['DB_HOST'],
        $env['DB_NAME']
    );

    return new PDO(
        $dsn,
        $env['DB_USER'],
        $env['DB_PASS'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
}