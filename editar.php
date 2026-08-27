<?php

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/auth.php';
require_once __DIR__ . '/src/validation.php';

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
     WHERE id = :id
     LIMIT 1'
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

        $newPassword = $_POST['password'] ?? '';

        if ($newPassword !== '') {

            $password = validatePassword(
                $newPassword
            );

            $hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $pdo->prepare(
                'UPDATE usuarios
                 SET nombre = :nombre,
                     email = :email,
                     password = :password
                 WHERE id = :id'
            );

            $stmt->execute([
                'nombre' => $nombre,
                'email' => $email,
                'password' => $hash,
                'id' => $id,
            ]);

        } else {

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
        }

        header('Location: usuarios.php');
        exit;

    } catch (PDOException $exception) {

        if ((int) $exception->errorInfo[1] === 1062) {
            $error = 'El correo electrónico ya está registrado.';
        } else {
            $error = 'No fue posible modificar el usuario.';
        }

    } catch (InvalidArgumentException $exception) {

        $error = $exception->getMessage();

    } catch (Throwable $exception) {

        $error = 'No fue posible modificar el usuario.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/pagina.css">
    <title>WebAdmin - Editar usuario</title>
</head>

<body>

    <h1>Editar usuario</h1>

    <?php if ($error !== null): ?>

        <p>
            <?= htmlspecialchars(
                $error,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <label>
            Nombre:

            <input
                type="text"
                name="nombre"
                maxlength="100"
                required
                value="<?= htmlspecialchars(
                    $usuario['nombre'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
        </label>

        <br><br>

        <label>
            Correo:

            <input
                type="email"
                name="email"
                maxlength="150"
                required
                value="<?= htmlspecialchars(
                    $usuario['email'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
        </label>

        <br><br>

        <label>
            Nueva contraseña:

            <input
                type="password"
                name="password"
                minlength="8"
            >
        </label>

        <p>
            Deja este campo vacío si no deseas cambiar la contraseña.
        </p>

        <button type="submit">
            Guardar cambios
        </button>

    </form>

    <p>
        <a href="usuarios.php">
            Regresar
        </a>
    </p>

</body>

</html>