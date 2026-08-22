<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/validation.php';

requireAuthentication();

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id) {
    header('Location: usuarios.php');
    exit;
}

$pdo = getDatabaseConnection();

$stmt = $pdo->prepare(
    'SELECT id, nombre, email
     FROM usuarios
     WHERE id = :id'
);

$stmt->execute([
    'id' => $id,
]);

$usuario = $stmt->fetch();

if (!$usuario) {
    header('Location: usuarios.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        $nombre = validateName(
            $_POST['nombre'] ?? ''
        );

        $email = validateEmail(
            $_POST['email'] ?? ''
        );

        $stmt = $pdo->prepare(
            'UPDATE usuarios
             SET nombre = :nombre,
                 email = :email
             WHERE id = :id'
        );

        $stmt->execute([
            'nombre' => $nombre,
            'email' => $email,
            'id' => $id,
        ]);

        header('Location: usuarios.php');
        exit;

    } catch (Throwable $e) {

        $error = 'No fue posible modificar el usuario.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar usuario</title>
    <link rel="stylesheet" href="css/pagina.css">
</head>
<body>

<h1>Editar usuario</h1>

<?php if ($error): ?>
    <p><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">

    <label>
        Nombre:
        <input
            type="text"
            name="nombre"
            value="<?= htmlspecialchars($usuario['nombre']) ?>"
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
            value="<?= htmlspecialchars($usuario['email']) ?>"
            maxlength="150"
            required
        >
    </label>

    <br><br>

    <button type="submit">
        Guardar cambios
    </button>

</form>

</body>
</html>