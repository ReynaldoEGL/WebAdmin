<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';

class UserResource
{
	private $db;
	private $user;

	public function __construct()
	{
		$database = new Database();
		$this->db = $database->getConnection();
		$this->user = new User($this->db);
	}

	// GET /api/v1/users
	public function index()
	{
		header("Content-Type: application/json");

		$stmt = $this->user->read();
		$users_arr = array("records" => array());

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$users_arr["records"][] = array(
				"id" => $row['id'],
				"nombre" => $row['nombre'],
				"email" => $row['email'],
				"fecha_registro" => $row['fecha_registro']
			);
		}

		http_response_code(200);
		echo json_encode($users_arr);
	}

	// GET /api/v1/users/{id}
	public function show($id)
	{
		header("Content-Type: application/json");
		$this->user->id = $id;

		if ($this->user->readOne()) {
			http_response_code(200);
			echo json_encode(array(
				"id" => $this->user->id,
				"nombre" => $this->user->nombre,
				"email" => $this->user->email,
				"fecha_registro" => $this->user->fecha_registro
			));
			return;
		}

		http_response_code(404);
		echo json_encode(array("message" => "Usuario no encontrado"));
	}

	// POST /api/v1/users
	public function store()
	{
		header("Content-Type: application/json");
		$data = json_decode(file_get_contents("php://input"));

		if (!$data || empty($data->nombre) || empty($data->email) || empty($data->password)) {
			http_response_code(400);
			echo json_encode(array("message" => "Datos incompletos"));
			return;
		}

		$this->user->nombre = $data->nombre;
		$this->user->email = $data->email;
		$this->user->password = $data->password;

		if ($this->user->create()) {
			http_response_code(201);
			echo json_encode(array(
				"message" => "Usuario creado exitosamente",
				"id" => $this->user->id
			));
			return;
		}

		http_response_code(503);
		echo json_encode(array("message" => "No se pudo crear el usuario"));
	}

	// PUT /api/v1/users/{id}
	public function update($id)
	{
		header("Content-Type: application/json");
		$data = json_decode(file_get_contents("php://input"));

		if (!$data || empty($data->nombre) || empty($data->email)) {
			http_response_code(400);
			echo json_encode(array("message" => "Datos incompletos"));
			return;
		}

		$this->user->id = $id;
		$this->user->nombre = $data->nombre;
		$this->user->email = $data->email;
		$this->user->password = $data->password ?? null;

		if ($this->user->update()) {
			http_response_code(200);
			echo json_encode(array("message" => "Usuario actualizado exitosamente"));
			return;
		}

		http_response_code(503);
		echo json_encode(array("message" => "No se pudo actualizar el usuario"));
	}

	// DELETE /api/v1/users/{id}
	public function destroy($id)
	{
		header("Content-Type: application/json");
		$this->user->id = $id;

		if ($this->user->delete()) {
			http_response_code(200);
			echo json_encode(array("message" => "Usuario eliminado exitosamente"));
			return;
		}

		http_response_code(503);
		echo json_encode(array("message" => "No se pudo eliminar el usuario"));
	}
}
?>
