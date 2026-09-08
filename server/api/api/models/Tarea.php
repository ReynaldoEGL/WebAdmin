<?php

class Tarea
{
    private $conn;

    private $table_name = "tareas";

    public $id;
    public $titulo;
    public $completada;
    public $fecha_creacion;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read()
    {
        $query = "
            SELECT
                id,
                titulo,
                completada,
                fecha_creacion
            FROM {$this->table_name}
            ORDER BY id ASC
        ";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt;
    }

    public function readOne()
    {
        $query = "
            SELECT
                id,
                titulo,
                completada,
                fecha_creacion
            FROM {$this->table_name}
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(
            ':id',
            $this->id,
            PDO::PARAM_INT
        );

        $stmt->execute();

        $row = $stmt->fetch(
            PDO::FETCH_ASSOC
        );

        if (!$row) {
            return false;
        }

        $this->id =
            (int) $row['id'];

        $this->titulo =
            $row['titulo'];

        $this->completada =
            (bool) $row['completada'];

        $this->fecha_creacion =
            $row['fecha_creacion'];

        return true;
    }

    public function create()
    {
        $query = "
            INSERT INTO {$this->table_name}
            (
                titulo,
                completada
            )
            VALUES
            (
                :titulo,
                :completada
            )
        ";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(
            ':titulo',
            $this->titulo,
            PDO::PARAM_STR
        );

        $stmt->bindValue(
            ':completada',
            $this->completada,
            PDO::PARAM_BOOL
        );

        if (!$stmt->execute()) {
            return false;
        }

        $this->id =
            (int) $this->conn->lastInsertId();

        return true;
    }

    public function update()
    {
        $query = "
            UPDATE {$this->table_name}
            SET
                titulo = :titulo,
                completada = :completada
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(
            ':titulo',
            $this->titulo,
            PDO::PARAM_STR
        );

        $stmt->bindValue(
            ':completada',
            $this->completada,
            PDO::PARAM_BOOL
        );

        $stmt->bindValue(
            ':id',
            $this->id,
            PDO::PARAM_INT
        );

        return $stmt->execute();
    }

    public function delete()
    {
        $query = "
            DELETE FROM {$this->table_name}
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(
            ':id',
            $this->id,
            PDO::PARAM_INT
        );

        return $stmt->execute();
    }
}
?>