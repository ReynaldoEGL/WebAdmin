<?php

declare(strict_types=1);

require_once __DIR__ . '/src/auth.php';

requireAuthentication();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/pagina.css">
    <title>WebAdmin - Dashboard</title>
</head>

<body>

    <h1>WebAdmin</h1>

    <h2>Panel de administración</h2>

    <p>
        Bienvenido,
        <?= htmlspecialchars(
            $_SESSION['usuario_nombre'],
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </p>

    <nav>
        <ul>
            <li>
                <a href="usuarios.php">
                    Administrar usuarios
                </a>
            </li>

            <li>
                <a href="logout.php">
                    Cerrar sesión
                </a>
            </li>
        </ul>
    </nav>

</body>

</html>