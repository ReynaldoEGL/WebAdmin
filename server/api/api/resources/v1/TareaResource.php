<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Tarea.php';

class TareaResource
{
    private function database()
    {
        $database = new Database();

        return $database->getConnection();
    }

    private function json($data, $status = 200)
    {
        http_response_code($status);

        header(
            'Content-Type: application/json; charset=utf-8'
        );

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
        );
    }

    private function body()
    {
        $raw = file_get_contents('php://input');

        if ($raw === false || trim($raw) === '') {
            return null;
        }

        $data = json_decode(
            $raw,
            true
        );

        if (
            json_last_error() !== JSON_ERROR_NONE ||
            !is_array($data)
        ) {
            return null;
        }

        return $data;
    }

    private function validar($data)
    {
        if (!isset($data['titulo'])) {
            return 'El campo titulo es obligatorio';
        }

        if (
            !is_string($data['titulo']) ||
            trim($data['titulo']) === ''
        ) {
            return 'El campo titulo debe ser un texto no vacío';
        }

        if (!isset($data['completada'])) {
            return 'El campo completada es obligatorio';
        }

        if (!is_bool($data['completada'])) {
            return 'El campo completada debe ser booleano';
        }

        return null;
    }

    public function index()
    {
        try {

            $db = $this->database();

            $tarea =
                new Tarea($db);

            $stmt =
                $tarea->read();

            $tareas = [];

            while (
                $row =
                    $stmt->fetch(
                        PDO::FETCH_ASSOC
                    )
            ) {

                $tareas[] = [
                    'id' =>
                        (int) $row['id'],

                    'titulo' =>
                        $row['titulo'],

                    'completada' =>
                        (bool) $row['completada'],

                    'fecha_creacion' =>
                        $row['fecha_creacion']
                ];
            }

            $this->json($tareas);

        } catch (Throwable $e) {

            $this->json([
                'error' => 'server_error',
                'message' =>
                    'No se pudieron obtener las tareas'
            ], 500);
        }
    }

    public function show($id)
    {
        try {

            if (!ctype_digit((string) $id)) {

                $this->json([
                    'error' => 'invalid_id',
                    'message' =>
                        'El ID debe ser numérico'
                ], 400);

                return;
            }

            $db = $this->database();

            $tarea =
                new Tarea($db);

            $tarea->id =
                (int) $id;

            if (!$tarea->readOne()) {

                $this->json([
                    'error' => 'not_found',
                    'message' =>
                        'Tarea no encontrada'
                ], 404);

                return;
            }

            $this->json([
                'id' =>
                    $tarea->id,

                'titulo' =>
                    $tarea->titulo,

                'completada' =>
                    $tarea->completada,

                'fecha_creacion' =>
                    $tarea->fecha_creacion
            ]);

        } catch (Throwable $e) {

            $this->json([
                'error' => 'server_error',
                'message' =>
                    'No se pudo obtener la tarea'
            ], 500);
        }
    }

    public function store()
    {
        try {

            $data = $this->body();

            if ($data === null) {

                $this->json([
                    'error' => 'invalid_json',
                    'message' =>
                        'El cuerpo JSON no es válido'
                ], 400);

                return;
            }

            $error =
                $this->validar($data);

            if ($error !== null) {

                $this->json([
                    'error' => 'validation_error',
                    'message' => $error
                ], 400);

                return;
            }

            $db =
                $this->database();

            $tarea =
                new Tarea($db);

            $tarea->titulo =
                trim($data['titulo']);

            $tarea->completada =
                $data['completada'];

            if (!$tarea->create()) {

                $this->json([
                    'error' => 'create_error',
                    'message' =>
                        'No se pudo crear la tarea'
                ], 500);

                return;
            }

            $tarea->readOne();

            $this->json([
                'id' =>
                    $tarea->id,

                'titulo' =>
                    $tarea->titulo,

                'completada' =>
                    $tarea->completada,

                'fecha_creacion' =>
                    $tarea->fecha_creacion
            ], 201);

        } catch (Throwable $e) {

            $this->json([
                'error' => 'server_error',
                'message' =>
                    'No se pudo crear la tarea'
            ], 500);
        }
    }

    public function update($id)
    {
        try {

            if (!ctype_digit((string) $id)) {

                $this->json([
                    'error' => 'invalid_id',
                    'message' =>
                        'El ID debe ser numérico'
                ], 400);

                return;
            }

            $data =
                $this->body();

            if ($data === null) {

                $this->json([
                    'error' => 'invalid_json',
                    'message' =>
                        'El cuerpo JSON no es válido'
                ], 400);

                return;
            }

            $error =
                $this->validar($data);

            if ($error !== null) {

                $this->json([
                    'error' => 'validation_error',
                    'message' => $error
                ], 400);

                return;
            }

            $db =
                $this->database();

            $tarea =
                new Tarea($db);

            $tarea->id =
                (int) $id;

            if (!$tarea->readOne()) {

                $this->json([
                    'error' => 'not_found',
                    'message' =>
                        'Tarea no encontrada'
                ], 404);

                return;
            }

            $tarea->titulo =
                trim($data['titulo']);

            $tarea->completada =
                $data['completada'];

            if (!$tarea->update()) {

                $this->json([
                    'error' => 'update_error',
                    'message' =>
                        'No se pudo actualizar la tarea'
                ], 500);

                return;
            }

            $tarea->readOne();

            $this->json([
                'id' =>
                    $tarea->id,

                'titulo' =>
                    $tarea->titulo,

                'completada' =>
                    $tarea->completada,

                'fecha_creacion' =>
                    $tarea->fecha_creacion
            ]);

        } catch (Throwable $e) {

            $this->json([
                'error' => 'server_error',
                'message' =>
                    'No se pudo actualizar la tarea'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {

            if (!ctype_digit((string) $id)) {

                $this->json([
                    'error' => 'invalid_id',
                    'message' =>
                        'El ID debe ser numérico'
                ], 400);

                return;
            }

            $db =
                $this->database();

            $tarea =
                new Tarea($db);

            $tarea->id =
                (int) $id;

            if (!$tarea->readOne()) {

                $this->json([
                    'error' => 'not_found',
                    'message' =>
                        'Tarea no encontrada'
                ], 404);

                return;
            }

            if (!$tarea->delete()) {

                $this->json([
                    'error' => 'delete_error',
                    'message' =>
                        'No se pudo eliminar la tarea'
                ], 500);

                return;
            }

            $this->json([
                'message' =>
                    'Tarea eliminada correctamente'
            ]);

        } catch (Throwable $e) {

            $this->json([
                'error' => 'server_error',
                'message' =>
                    'No se pudo eliminar la tarea'
            ], 500);
        }
    }
}
?>