<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/auth.php';

requireAuthentication();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>WebAdmin - Dashboard</title>
    <link rel="stylesheet" href="css/pagina.css">
</head>
<body>

<h1>WebAdmin</h1>

<p>
    Bienvenido,
    <?= htmlspecialchars($_SESSION['usuario_nombre']) ?>
</p>

<nav>
    <a href="usuarios.php">Administrar usuarios</a>
    |
    <a href="logout.php">Cerrar sesión</a>
</nav>

</body>
</html>