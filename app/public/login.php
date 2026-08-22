<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/auth.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Todos los campos son obligatorios.';
    } else {
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
            password_verify($password, $user['password'])
        ) {
            loginUser(
                (int) $user['id'],
                $user['nombre']
            );

            header('Location: dashboard.php');
            exit;
        }

        $error = 'Correo o contraseña incorrectos.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>WebAdmin - Login</title>
    <link rel="stylesheet" href="css/pagina.css">
</head>
<body>

<h1>WebAdmin</h1>

<?php if ($error): ?>
    <p><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">

    <label>
        Correo:
        <input
            type="email"
            name="email"
            required
        >
    </label>

    <br><br>

    <label>
        Contraseña:
        <input
            type="password"
            name="password"
            required
        >
    </label>

    <br><br>

    <button type="submit">
        Iniciar sesión
    </button>

</form>

</body>
</html>