<?php

class User
{
	private $conn;
	private $table_name = "usuarios";

	public $id;
	public $nombre;
	public $email;
	public $password;
	public $fecha_registro;

	public function __construct($db)
	{
		$this->conn = $db;
	}

	// Crear usuario
	public function create()
	{
		$query = "INSERT INTO " . $this->table_name . "
				  SET nombre = :nombre,
					  email = :email,
					  password = :password";

		$stmt = $this->conn->prepare($query);

		$this->nombre = htmlspecialchars(strip_tags($this->nombre));
		$this->email = htmlspecialchars(strip_tags($this->email));
		$this->password = password_hash($this->password, PASSWORD_DEFAULT);

		$stmt->bindParam(":nombre", $this->nombre);
		$stmt->bindParam(":email", $this->email);
		$stmt->bindParam(":password", $this->password);

		if ($stmt->execute()) {
			$this->id = $this->conn->lastInsertId();
			return true;
		}

		return false;
	}

	// Obtener todos los usuarios
	public function read()
	{
		$query = "SELECT id,
						 nombre,
						 email,
						 fecha_registro
				  FROM " . $this->table_name . "
				  ORDER BY fecha_registro DESC";

		$stmt = $this->conn->prepare($query);
		$stmt->execute();

		return $stmt;
	}

	// Obtener un usuario
	public function readOne()
	{
		$query = "SELECT id,
						 nombre,
						 email,
						 fecha_registro
				  FROM " . $this->table_name . "
				  WHERE id = :id
				  LIMIT 1";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":id", $this->id);
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($row) {
			$this->id = $row['id'];
			$this->nombre = $row['nombre'];
			$this->email = $row['email'];
			$this->fecha_registro = $row['fecha_registro'];

			return true;
		}

		return false;
	}

	// Actualizar usuario
	public function update()
	{
		$query = "UPDATE " . $this->table_name . "
				  SET nombre = :nombre,
					  email = :email";

		if (!empty($this->password)) {
			$query .= ", password = :password";
		}

		$query .= " WHERE id = :id";

		$stmt = $this->conn->prepare($query);

		$this->nombre = htmlspecialchars(strip_tags($this->nombre));
		$this->email = htmlspecialchars(strip_tags($this->email));

		$stmt->bindParam(":nombre", $this->nombre);
		$stmt->bindParam(":email", $this->email);
		$stmt->bindParam(":id", $this->id);

		if (!empty($this->password)) {
			$this->password = password_hash($this->password, PASSWORD_DEFAULT);
			$stmt->bindParam(":password", $this->password);
		}

		return $stmt->execute();
	}

	// Eliminar usuario
	public function delete()
	{
		$query = "DELETE FROM " . $this->table_name . "
				  WHERE id = :id";

		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":id", $this->id);

		return $stmt->execute();
	}
}
?>
