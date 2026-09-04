<?php

class ApiToken
{
    private $conn;
    private $table_name = "api_tokens";

    public $id;
    public $user_id;
    public $token;
    public $expires_at;
    public $revoked;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function revokeUserTokens($user_id)
    {
        $query = "UPDATE " . $this->table_name . "
                  SET revoked = TRUE
                  WHERE user_id = :user_id
                  AND revoked = FALSE";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);

        return $stmt->execute();
    }

    public function create($user_id, $expires_at)
    {
        $this->token = bin2hex(random_bytes(32));
        $this->user_id = $user_id;
        $this->expires_at = $expires_at;

        $query = "INSERT INTO " . $this->table_name . "
                  (user_id, token, expires_at, revoked)
                  VALUES (:user_id, :token, :expires_at, FALSE)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':token', $this->token);
        $stmt->bindParam(':expires_at', $this->expires_at);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    public function findValidToken($token)
    {
        $query = "SELECT id, user_id, token, expires_at, revoked, created_at
                  FROM " . $this->table_name . "
                  WHERE token = :token
                    AND revoked = FALSE
                    AND expires_at > NOW()
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return false;
        }

        return $row;
    }

    public function revoke($token)
    {
        $query = "UPDATE " . $this->table_name . "
                  SET revoked = TRUE
                  WHERE token = :token";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);

        return $stmt->execute();
    }
}
?>