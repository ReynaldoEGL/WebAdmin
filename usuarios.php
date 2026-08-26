<?php

declare(strict_types=1);

require_once __DIR__ . 'config/database.php';
require_once __DIR__ . 'src/auth.php';

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/pagina.css">
    <title>WebAdmin - Usuarios</title>
</head>

<body>

    <h1>Usuarios</h1>

    <nav>
        <a href="dashboard.php">Dashboard</a>
        |
        <a href="crear.php">Nuevo usuario</a>
        |
        <a href="logout.php">Cerrar sesión</a>
    </nav>

    <br>

    <table border="1" cellpadding="8">

        <thead>

            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Fecha de registro</th>
                <th>Acciones</th>
            </tr>

        </thead>

        <tbody>

            <?php if (!$usuarios): ?>

                <tr>
                    <td colspan="5">
                        No existen usuarios registrados.
                    </td>
                </tr>

            <?php else: ?>

                <?php foreach ($usuarios as $usuario): ?>

                    <tr>

                        <td>
                            <?= (int) $usuario['id'] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $usuario['nombre'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $usuario['email'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $usuario['fecha_registro'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
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

            <?php endif; ?>

        </tbody>

    </table>

</body>

</html>