<?php

declare(strict_types=1);

function getDatabaseConnection(): PDO
{
    $password = trim(
        file_get_contents('/run/secrets/db_password')
    );

    $dsn = 'mysql:host=db;dbname=webadmin_db;charset=utf8mb4';

    return new PDO(
        $dsn,
        'webadmin_user',
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
}