<?php

class ApiUser
{
    private $conn;
    private $table_name = "api_users";

    public $id;
    public $username;
    public $email;
    public $password_hash;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function findByUsername($username)
    {
        $query = "SELECT id, username, email, password_hash, status,
                         created_at, updated_at
                  FROM " . $this->table_name . "
                  WHERE username = :username
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return false;
        }

        $this->id = $row['id'];
        $this->username = $row['username'];
        $this->email = $row['email'];
        $this->password_hash = $row['password_hash'];
        $this->status = $row['status'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];

        return true;
    }

    public function findById($id)
    {
        $query = "SELECT id, username, email, password_hash, status,
                         created_at, updated_at
                  FROM " . $this->table_name . "
                  WHERE id = :id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return false;
        }

        $this->id = $row['id'];
        $this->username = $row['username'];
        $this->email = $row['email'];
        $this->password_hash = $row['password_hash'];
        $this->status = $row['status'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];

        return true;
    }
}
?>