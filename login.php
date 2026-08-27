<?php

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/auth.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Todos los campos son obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo electrónico no es válido.';
    } else {
        try {
            $pdo = getDatabaseConnection();

            $stmt = $pdo->prepare(
                'SELECT id, nombre, password
                 FROM usuarios
                 WHERE email = :email
                 LIMIT 1'
            );

            $stmt->execute([
                'email' => $email,
            ]);

            $user = $stmt->fetch();

            if (
                $user &&
                password_verify(
                    $password,
                    $user['password']
                )
            ) {
                loginUser(
                    (int) $user['id'],
                    $user['nombre']
                );

                header('Location: dashboard.php');
                exit;
            }

            $error = 'Correo o contraseña incorrectos.';
        } catch (Throwable $exception) {
            $error = 'No fue posible conectarse al servidor.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/pagina.css">
    <title>WebAdmin - Iniciar sesión</title>
</head>

<body>

    <h1>WebAdmin</h1>

    <h2>Iniciar sesión</h2>

    <?php if ($error !== null): ?>

        <p>
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>

    <?php endif; ?>

    <form method="POST" action="login.php">

        <div>
            <label for="email">
                Correo electrónico:
            </label>

            <input
                type="email"
                id="email"
                name="email"
                maxlength="150"
                required
            >
        </div>

        <br>

        <div>
            <label for="password">
                Contraseña:
            </label>

            <input
                type="password"
                id="password"
                name="password"
                required
            >
        </div>

        <br>

        <button type="submit">
            Iniciar sesión
        </button>

    </form>

</body>

</html>