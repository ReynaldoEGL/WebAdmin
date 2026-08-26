<?php

declare(strict_types=1);

require_once __DIR__ . 'src/auth.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

header('Location: login.php');
exit;