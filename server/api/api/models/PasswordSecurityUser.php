<?php

class PasswordSecurityUser
{
    private $db;

    public function __construct()
    {
        $database = new Database();

        $this->db = $database->getConnection();
    }

    public function findByUsername($username)
    {
        $sql = "
            SELECT *
            FROM password_security_users
            WHERE username = :username
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':username' => $username
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function findByEmail($email)
    {
        $sql = "
            SELECT *
            FROM password_security_users
            WHERE email = :email
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':email' => $email
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function create(
        $id,
        $username,
        $email,
        $passwordHash
    ) {
        $sql = "
            INSERT INTO password_security_users
            (
                id,
                username,
                email,
                password_hash
            )
            VALUES
            (
                :id,
                :username,
                :email,
                :password_hash
            )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id,
            ':username' => $username,
            ':email' => $email,
            ':password_hash' => $passwordHash
        ]);

        return $this->findByUsername($username);
    }

    public function recordFailedLogin($id)
    {
        $sql = "
            UPDATE password_security_users
            SET failed_login_attempts =
                failed_login_attempts + 1
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $sql = "
            SELECT failed_login_attempts
            FROM password_security_users
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function lock($id, $minutes = 15)
    {
        $sql = "
            UPDATE password_security_users
            SET
                locked_until =
                    DATE_ADD(NOW(), INTERVAL :minutes MINUTE),
                failed_login_attempts = 0
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(
            ':minutes',
            $minutes,
            PDO::PARAM_INT
        );

        $stmt->bindValue(
            ':id',
            $id
        );

        $stmt->execute();
    }

    public function resetFailedAttempts($id)
    {
        $sql = "
            UPDATE password_security_users
            SET
                failed_login_attempts = 0,
                locked_until = NULL
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);
    }
}