<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

$pdo = getDatabaseConnection();

$name = 'Administrador';
$email = 'admin@webadmin.local';
$password = 'AdminWeb2026!';

$hash = password_hash(
    $password,
    PASSWORD_DEFAULT
);

$stmt = $pdo->prepare(
    'INSERT INTO usuarios (nombre, email, password)
     VALUES (:nombre, :email, :password)'
);

$stmt->execute([
    'nombre' => $name,
    'email' => $email,
    'password' => $hash,
]);

echo "Usuario administrador creado correctamente." . PHP_EOL;