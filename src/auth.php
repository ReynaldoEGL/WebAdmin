<?php

declare(strict_types=1);

function startApplicationSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'httponly' => true,
            'secure' => false,
            'samesite' => 'Lax',
        ]);

        session_start();
    }
}

startApplicationSession();


function requireAuthentication(): void
{
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: /login.php');
        exit;
    }
}


function loginUser(
    int $userId,
    string $name
): void {
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
            [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => 'Lax',
            ]
        );
    }

    session_destroy();
}