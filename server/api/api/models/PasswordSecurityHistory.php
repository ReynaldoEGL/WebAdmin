<?php

class PasswordSecurityHistory
{
    private $db;

    public function __construct()
    {
        $database = new Database();

        $this->db = $database->getConnection();
    }

    public function add(
        $userId,
        $passwordHash
    ) {
        $sql = "
            INSERT INTO
            password_security_password_history
            (
                user_id,
                password_hash
            )
            VALUES
            (
                :user_id,
                :password_hash
            )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':user_id' => $userId,
            ':password_hash' => $passwordHash
        ]);
    }

    public function getByUser($userId)
    {
        $sql = "
            SELECT *
            FROM password_security_password_history
            WHERE user_id = :user_id
            ORDER BY created_at DESC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':user_id' => $userId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}