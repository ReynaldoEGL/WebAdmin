<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ApiUser.php';
require_once __DIR__ . '/../models/ApiToken.php';

class AuthMiddleware
{
    private static $authenticatedUser = null;

    public static function handle()
    {
        header("Content-Type: application/json");

        $authorization = '';

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authorization = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authorization = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }

        if (empty($authorization)) {
            http_response_code(401);

            echo json_encode([
                "error" => "unauthorized",
                "message" => "Token inválido, expirado o no proporcionado"
            ]);

            return false;
        }

        if (
            !preg_match(
                '/^Bearer\s+(.+)$/i',
                $authorization,
                $matches
            )
        ) {
            http_response_code(401);

            echo json_encode([
                "error" => "unauthorized",
                "message" => "Token inválido, expirado o no proporcionado"
            ]);

            return false;
        }

        $token = trim($matches[1]);

        if ($token === '') {
            http_response_code(401);

            echo json_encode([
                "error" => "unauthorized",
                "message" => "Token inválido, expirado o no proporcionado"
            ]);

            return false;
        }

        try {

            $database = new Database();
            $db = $database->getConnection();

            if (!$db) {
                http_response_code(500);

                echo json_encode([
                    "error" => "server_error",
                    "message" => "No se pudo conectar con la base de datos"
                ]);

                return false;
            }

            $tokenModel = new ApiToken($db);

            $tokenData = $tokenModel->findValidToken($token);

            if (!$tokenData) {
                http_response_code(401);

                echo json_encode([
                    "error" => "unauthorized",
                    "message" => "Token inválido, expirado o no proporcionado"
                ]);

                return false;
            }

            $userModel = new ApiUser($db);

            if (!$userModel->findById($tokenData['user_id'])) {
                http_response_code(401);

                echo json_encode([
                    "error" => "unauthorized",
                    "message" => "Token inválido, expirado o no proporcionado"
                ]);

                return false;
            }

            if ($userModel->status !== 'ACTIVE') {
                http_response_code(401);

                echo json_encode([
                    "error" => "unauthorized",
                    "message" => "Token inválido, expirado o no proporcionado"
                ]);

                return false;
            }

            self::$authenticatedUser = [
                "id" => $userModel->id,
                "username" => $userModel->username,
                "email" => $userModel->email,
                "status" => $userModel->status
            ];

            return true;

        } catch (Exception $e) {

            http_response_code(500);

            echo json_encode([
                "error" => "server_error",
                "message" => "Error interno del servidor"
            ]);

            return false;
        }
    }

    public static function user()
    {
        return self::$authenticatedUser;
    }

    public static function getToken()
    {
        $authorization = '';

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authorization = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authorization = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }

        if (
            preg_match(
                '/^Bearer\s+(.+)$/i',
                $authorization,
                $matches
            )
        ) {
            return trim($matches[1]);
        }

        return null;
    }
}
?>