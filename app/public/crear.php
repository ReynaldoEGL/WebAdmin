<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/validation.php';

requireAuthentication();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        $nombre = validateName(
            $_POST['nombre'] ?? ''
        );

        $email = validateEmail(
            $_POST['email'] ?? ''
        );

        $password = validatePassword(
            $_POST['password'] ?? ''
        );

        $hash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $pdo = getDatabaseConnection();

        $stmt = $pdo->prepare(
            'INSERT INTO usuarios
             (nombre, email, password)
             VALUES
             (:nombre, :email, :password)'
        );

        $stmt->execute([
            'nombre' => $nombre,
            'email' => $email,
            'password' => $hash,
        ]);

        header('Location: usuarios.php');
        exit;

    } catch (Throwable $e) {

        $error = 'No fue posible crear el usuario.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo usuario</title>
    <link rel="stylesheet" href="css/pagina.css">
</head>
<body>

<h1>Nuevo usuario</h1>

<?php if ($error): ?>
    <p><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">

    <label>
        Nombre:
        <input
            type="text"
            name="nombre"
            maxlength="100"
            required
        >
    </label>

    <br><br>

    <label>
        Email:
        <input
            type="email"
            name="email"
            maxlength="150"
            required
        >
    </label>

    <br><br>

    <label>
        Contraseña:
        <input
            type="password"
            name="password"
            minlength="8"
            required
        >
    </label>

    <br><br>

    <button type="submit">
        Crear
    </button>

</form>

<p>
    <a href="usuarios.php">Regresar</a>
</p>

</body>
</html>