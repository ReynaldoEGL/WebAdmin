<?php

class PasswordSecurityResource
{
    public function generate()
    {
        $body = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (
            $body === null &&
            json_last_error() !== JSON_ERROR_NONE
        ) {
            $this->json([
                'error' => 'Bad Request',
                'message' => 'JSON inválido'
            ], 400);

            return;
        }

        try {

            $result =
                PasswordSecurity::generate(
                    is_array($body)
                        ? $body
                        : []
                );

            $this->json($result, 200);

        } catch (LengthException $e) {

            $this->json([
                'error' =>
                    'Unprocessable Entity',
                'message' => $e->getMessage()
            ], 422);

        } catch (RuntimeException $e) {

            $this->json([
                'error' =>
                    'Unprocessable Entity',
                'message' => $e->getMessage()
            ], 422);

        } catch (InvalidArgumentException $e) {

            $this->json([
                'error' => 'Bad Request',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function validate()
    {
        $body = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (
            !is_array($body) ||
            !isset($body['password']) ||
            !is_string($body['password'])
        ) {
            $this->json([
                'error' => 'Bad Request',
                'message' =>
                    'El campo password es obligatorio'
            ], 400);

            return;
        }

        $username = null;

        if (
            isset($body['username']) &&
            is_string($body['username'])
        ) {
            $username =
                $body['username'];
        }

        $result =
            PasswordSecurity::validate(
                $body['password'],
                $username
            );

        $this->json(
            $result,
            200
        );
    }

    public function policy()
    {
        $this->json(
            PasswordSecurity::getPolicy(),
            200
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