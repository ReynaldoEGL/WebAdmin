<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/auth.php';

requireAuthentication();

$pdo = getDatabaseConnection();

$stmt = $pdo->query(
    'SELECT id, nombre, email, fecha_registro
     FROM usuarios
     ORDER BY id DESC'
);

$usuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>WebAdmin - Usuarios</title>
    <link rel="stylesheet" href="css/pagina.css">
</head>
<body>

<h1>Usuarios</h1>

<p>
    <a href="dashboard.php">Dashboard</a>
    |
    <a href="crear.php">Nuevo usuario</a>
    |
    <a href="logout.php">Cerrar sesión</a>
</p>

<table border="1" cellpadding="8">

<thead>
<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Email</th>
    <th>Fecha de registro</th>
    <th>Acciones</th>
</tr>
</thead>

<tbody>

<?php foreach ($usuarios as $usuario): ?>

<tr>

    <td>
        <?= (int) $usuario['id'] ?>
    </td>

    <td>
        <?= htmlspecialchars($usuario['nombre']) ?>
    </td>

    <td>
        <?= htmlspecialchars($usuario['email']) ?>
    </td>

    <td>
        <?= htmlspecialchars($usuario['fecha_registro']) ?>
    </td>

    <td>

        <a
            href="editar.php?id=<?= (int) $usuario['id'] ?>"
        >
            Editar
        </a>

        <form
            method="POST"
            action="eliminar.php"
            style="display:inline;"
        >

            <input
                type="hidden"
                name="id"
                value="<?= (int) $usuario['id'] ?>"
            >

            <button type="submit">
                Eliminar
            </button>

        </form>

    </td>

</tr>

<?php endforeach; ?>

</tbody>
</table>

</body>
</html>