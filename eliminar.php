<?php

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/auth.php';

requireAuthentication();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /usuarios.php');
    exit;
}

$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id) {
    header('Location: usuarios.php');
    exit;
}

try {

    $pdo = getDatabaseConnection();

    $stmt = $pdo->prepare(
        'DELETE FROM usuarios
         WHERE id = :id'
    );

    $stmt->execute([
        'id' => $id,
    ]);

} catch (Throwable $exception) {
    // En un proyecto más grande registraríamos el error.
}

header('Location: usuarios.php');
exit;