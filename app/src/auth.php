<?php

declare(strict_types=1);

session_start();

function requireAuthentication(): void
{
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit;
    }
}

function loginUser(int $userId, string $name): void
{
    session_regenerate_id(true);

    $_SESSION['usuario_id'] = $userId;
    $_SESSION['usuario_nombre'] = $name;
}

function logoutUser(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}