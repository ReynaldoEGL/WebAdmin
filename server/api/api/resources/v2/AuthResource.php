<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/ApiUser.php';
require_once __DIR__ . '/../../models/ApiToken.php';
require_once __DIR__ . '/../../core/AuthMiddleware.php';

class AuthResource
{
    private $db;
    private $user;
    private $token;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();

        if (!$this->db) {
            throw new Exception(
                "No se pudo conectar con la base de datos"
            );
        }

        $this->user = new ApiUser($this->db);
        $this->token = new ApiToken($this->db);
    }

    // POST /api/v2/login
    public function login()
    {
        header("Content-Type: application/json");

        $data = json_decode(
            file_get_contents("php://input")
        );

        if (
            !$data ||
            empty($data->username) ||
            empty($data->password)
        ) {
            http_response_code(401);

            echo json_encode([
                "error" => "invalid_credentials",
                "message" => "Usuario o contraseña incorrectos"
            ]);

            return;
        }

        $username = trim($data->username);
        $password = $data->password;

        if (!$this->user->findByUsername($username)) {
            http_response_code(401);

            echo json_encode([
                "error" => "invalid_credentials",
                "message" => "Usuario o contraseña incorrectos"
            ]);

            return;
        }

        if (
            $this->user->status !== "ACTIVE" ||
            !password_verify(
                $password,
                $this->user->password_hash
            )
        ) {
            http_response_code(401);

            echo json_encode([
                "error" => "invalid_credentials",
                "message" => "Usuario o contraseña incorrectos"
            ]);

            return;
        }

        // Invalidar tokens anteriores
        $this->token->revokeUserTokens(
            $this->user->id
        );

        // Expira en 60 minutos
        $expires_at = date(
            'Y-m-d H:i:s',
            time() + 3600
        );

        if (
            !$this->token->create(
                $this->user->id,
                $expires_at
            )
        ) {
            http_response_code(500);

            echo json_encode([
                "error" => "server_error",
                "message" => "No se pudo generar el token"
            ]);

            return;
        }

        http_response_code(200);

        echo json_encode([
            "access_token" => $this->token->token,
            "token_type" => "Bearer",
            "expires_at" => $this->token->expires_at
        ]);
    }

    // POST /api/v2/logout
    public function logout()
    {
        header("Content-Type: application/json");

        $token = AuthMiddleware::getToken();

        if (!$token) {
            http_response_code(401);

            echo json_encode([
                "error" => "unauthorized",
                "message" => "Token inválido, expirado o no proporcionado"
            ]);

            return;
        }

        if ($this->token->revoke($token)) {

            http_response_code(200);

            echo json_encode([
                "message" => "Sesión cerrada correctamente"
            ]);

        } else {

            http_response_code(500);

            echo json_encode([
                "error" => "server_error",
                "message" => "No se pudo revocar el token"
            ]);
        }
    }

    // GET /api/v2/me
    public function me()
    {
        header("Content-Type: application/json");

        $user = AuthMiddleware::user();

        if (!$user) {
            http_response_code(401);

            echo json_encode([
                "error" => "unauthorized",
                "message" => "Token inválido, expirado o no proporcionado"
            ]);

            return;
        }

        http_response_code(200);

        echo json_encode([
            "id" => $user["id"],
            "username" => $user["username"],
            "email" => $user["email"],
            "status" => $user["status"]
        ]);
    }
}
?>