<?php

class PasswordAuthResource
{
    public function register()
    {
        $body = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (!is_array($body)) {
            $this->json([
                'error' => 'Bad Request',
                'message' => 'JSON inválido'
            ], 400);

            return;
        }

        $required = [
            'username',
            'email',
            'password'
        ];

        foreach ($required as $field) {

            if (
                !isset($body[$field]) ||
                !is_string($body[$field]) ||
                trim($body[$field]) === ''
            ) {
                $this->json([
                    'error' => 'Bad Request',
                    'message' =>
                        "El campo {$field} es obligatorio"
                ], 400);

                return;
            }
        }

        $username =
            trim($body['username']);

        $email =
            trim($body['email']);

        $password =
            $body['password'];

        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $this->json([
                'error' => 'Bad Request',
                'message' =>
                    'El email no es válido'
            ], 400);

            return;
        }

        $validation =
            PasswordSecurity::validate(
                $password,
                $username
            );

        if (!$validation['isValid']) {

            $this->json(
                $validation,
                422
            );

            return;
        }

        try {

            $userModel =
                new PasswordSecurityUser();

            if (
                $userModel->findByUsername(
                    $username
                )
            ) {
                $this->json([
                    'error' => 'Conflict',
                    'message' =>
                        'El username ya existe'
                ], 409);

                return;
            }

            if (
                $userModel->findByEmail(
                    $email
                )
            ) {
                $this->json([
                    'error' => 'Conflict',
                    'message' =>
                        'El email ya existe'
                ], 409);

                return;
            }

            $id =
                $this->uuid();

            $passwordHash =
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

            $user =
                $userModel->create(
                    $id,
                    $username,
                    $email,
                    $passwordHash
                );

            $historyModel =
                new PasswordSecurityHistory();

            $historyModel->add(
                $id,
                $passwordHash
            );

            $this->json([
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'createdAt' =>
                    date('c', strtotime(
                        $user['created_at']
                    ))
            ], 201);

        } catch (PDOException $e) {

            $this->json([
                'error' => 'Conflict',
                'message' =>
                    'No fue posible registrar el usuario'
            ], 409);
        }
    }

    public function login()
    {
        $body = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (
            !is_array($body) ||
            !isset($body['username']) ||
            !isset($body['password'])
        ) {
            $this->json([
                'error' => 'Bad Request',
                'message' =>
                    'username y password son obligatorios'
            ], 400);

            return;
        }

        $username =
            trim($body['username']);

        $password =
            $body['password'];

        $userModel =
            new PasswordSecurityUser();

        $user =
            $userModel->findByUsername(
                $username
            );

        if (!$user) {
            $this->json([
                'error' => 'Unauthorized',
                'message' =>
                    'Credenciales inválidas'
            ], 401);

            return;
        }

        if ($user['status'] !== 'active') {
            $this->json([
                'error' => 'Unauthorized',
                'message' =>
                    'El usuario está inactivo'
            ], 401);

            return;
        }

        if (
            $user['locked_until'] !== null &&
            strtotime(
                $user['locked_until']
            ) > time()
        ) {
            $this->json([
                'error' => 'Locked',
                'message' =>
                    'La cuenta está temporalmente bloqueada'
            ], 423);

            return;
        }

        if (
            !password_verify(
                $password,
                $user['password_hash']
            )
        ) {

            $attempts =
                $userModel->recordFailedLogin(
                    $user['id']
                );

            if ($attempts >= 5) {

                $userModel->lock(
                    $user['id'],
                    15
                );

                $this->json([
                    'error' => 'Locked',
                    'message' =>
                        'Cuenta bloqueada temporalmente después de múltiples intentos fallidos'
                ], 423);

                return;
            }

            $this->json([
                'error' => 'Unauthorized',
                'message' =>
                    'Credenciales inválidas'
            ], 401);

            return;
        }

        $userModel->resetFailedAttempts(
            $user['id']
        );

        $token =
            bin2hex(
                random_bytes(32)
            );

        $this->json([
            'accessToken' => $token,
            'tokenType' => 'Bearer',
            'expiresIn' => 3600
        ], 200);
    }

    private function uuid()
    {
        $data = random_bytes(16);

        $data[6] =
            chr(
                ord($data[6]) & 0x0f | 0x40
            );

        $data[8] =
            chr(
                ord($data[8]) & 0x3f | 0x80
            );

        return vsprintf(
            '%s%s-%s-%s-%s-%s%s%s',
            str_split(
                bin2hex($data),
                4
            )
        );
    }

    private function json(
        $data,
        $status
    ) {
        http_response_code($status);

        header(
            'Content-Type: application/json; charset=utf-8'
        );

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES |
            JSON_PRETTY_PRINT
        );
    }
}