<?php

declare(strict_types=1);

function validateName(string $name): string
{
    $name = trim($name);

    if ($name === '') {
        throw new InvalidArgumentException(
            'El nombre es obligatorio.'
        );
    }

    if (mb_strlen($name) > 100) {
        throw new InvalidArgumentException(
            'El nombre no puede superar los 100 caracteres.'
        );
    }

    return $name;
}


function validateEmail(string $email): string
{
    $email = trim($email);

    if ($email === '') {
        throw new InvalidArgumentException(
            'El correo es obligatorio.'
        );
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException(
            'El correo electrónico no es válido.'
        );
    }

    if (mb_strlen($email) > 150) {
        throw new InvalidArgumentException(
            'El correo no puede superar los 150 caracteres.'
        );
    }

    return $email;
}


function validatePassword(string $password): string
{
    if (strlen($password) < 8) {
        throw new InvalidArgumentException(
            'La contraseña debe tener al menos 8 caracteres.'
        );
    }

    return $password;
}