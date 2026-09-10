<?php

class PasswordSecurity
{
    private static $policy = [
        'minLength' => 12,
        'maxLength' => 64,
        'requireUppercase' => true,
        'requireLowercase' => true,
        'requireNumbers' => true,
        'requireSymbols' => true,
        'disallowCommonPasswords' => true,
        'disallowUsernameInPassword' => true,
        'disallowSequentialCharacters' => true,
        'passwordHistoryLimit' => 5,
        'expirationDays' => 90
    ];

    private static $commonPasswords = [
        'password',
        'password123',
        '123456',
        '12345678',
        '123456789',
        'qwerty',
        'qwerty123',
        'admin',
        'admin123',
        'welcome',
        'letmein',
        'abc123',
        'contraseña',
        'contraseña123'
    ];

    public static function getPolicy()
    {
        return self::$policy;
    }

    public static function generate($options = [])
    {
        $length = isset($options['length'])
            ? (int) $options['length']
            : 16;

        $includeUppercase =
            isset($options['includeUppercase'])
                ? (bool) $options['includeUppercase']
                : true;

        $includeLowercase =
            isset($options['includeLowercase'])
                ? (bool) $options['includeLowercase']
                : true;

        $includeNumbers =
            isset($options['includeNumbers'])
                ? (bool) $options['includeNumbers']
                : true;

        $includeSymbols =
            isset($options['includeSymbols'])
                ? (bool) $options['includeSymbols']
                : true;

        $excludeSimilar =
            isset($options['excludeSimilarCharacters'])
                ? (bool) $options['excludeSimilarCharacters']
                : false;

        $excludeAmbiguous =
            isset($options['excludeAmbiguousSymbols'])
                ? (bool) $options['excludeAmbiguousSymbols']
                : false;

        $count =
            isset($options['count'])
                ? (int) $options['count']
                : 1;

        if ($length < 8 || $length > 128) {
            throw new InvalidArgumentException(
                'length debe estar entre 8 y 128'
            );
        }

        if ($count < 1 || $count > 50) {
            throw new InvalidArgumentException(
                'count debe estar entre 1 y 50'
            );
        }

        $sets = [];

        if ($includeUppercase) {
            $sets['uppercase'] =
                'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        if ($includeLowercase) {
            $sets['lowercase'] =
                'abcdefghijklmnopqrstuvwxyz';
        }

        if ($includeNumbers) {
            $sets['numbers'] =
                '0123456789';
        }

        if ($includeSymbols) {
            $sets['symbols'] =
                '!@#$%^&*()-_=+[]{};:,.?';
        }

        if ($excludeSimilar) {
            foreach ($sets as $key => $characters) {
                $sets[$key] = str_replace(
                    ['I', 'l', '1', 'O', 'o', '0'],
                    '',
                    $characters
                );
            }
        }

        if ($excludeAmbiguous) {
            if (isset($sets['symbols'])) {
                $sets['symbols'] = str_replace(
                    ['{', '}', '[', ']', '(', ')', '/', '\\'],
                    '',
                    $sets['symbols']
                );
            }
        }

        foreach ($sets as $characters) {
            if ($characters === '') {
                throw new RuntimeException(
                    'Las opciones seleccionadas no permiten generar una contraseña válida'
                );
            }
        }

        if ($length < count($sets)) {
            throw new LengthException(
                'La longitud es insuficiente para cumplir los criterios seleccionados'
            );
        }

        $passwords = [];

        for ($i = 0; $i < $count; $i++) {

            $characters = [];

            foreach ($sets as $set) {
                $characters[] =
                    $set[
                        random_int(
                            0,
                            strlen($set) - 1
                        )
                    ];
            }

            $allCharacters = implode('', $sets);

            while (count($characters) < $length) {
                $characters[] =
                    $allCharacters[
                        random_int(
                            0,
                            strlen($allCharacters) - 1
                        )
                    ];
            }

            shuffle($characters);

            $passwords[] =
                implode('', $characters);
        }

        return [
            'passwords' => $passwords,
            'criteria' => [
                'length' => $length,
                'includeUppercase' => $includeUppercase,
                'includeLowercase' => $includeLowercase,
                'includeNumbers' => $includeNumbers,
                'includeSymbols' => $includeSymbols,
                'excludeSimilarCharacters' => $excludeSimilar,
                'excludeAmbiguousSymbols' => $excludeAmbiguous,
                'count' => $count
            ]
        ];
    }

    public static function validate(
        $password,
        $username = null
    ) {
        $rules = [];
        $suggestions = [];

        $minLength =
            strlen($password) >=
            self::$policy['minLength'];

        $uppercase =
            preg_match('/[A-Z]/', $password) === 1;

        $lowercase =
            preg_match('/[a-z]/', $password) === 1;

        $number =
            preg_match('/[0-9]/', $password) === 1;

        $symbol =
            preg_match('/[^a-zA-Z0-9]/', $password) === 1;

        $common =
            !in_array(
                strtolower($password),
                self::$commonPasswords,
                true
            );

        $sequential =
            !self::hasSequentialCharacters(
                $password
            );

        $usernamePassed = true;

        if (
            $username !== null &&
            $username !== ''
        ) {
            $usernamePassed =
                stripos(
                    $password,
                    $username
                ) === false;
        }

        $rules[] = [
            'rule' => 'minLength',
            'description' =>
                'La contraseña debe tener al menos 12 caracteres',
            'passed' => $minLength
        ];

        $rules[] = [
            'rule' => 'hasUppercase',
            'description' =>
                'Debe contener al menos una letra mayúscula',
            'passed' => $uppercase
        ];

        $rules[] = [
            'rule' => 'hasLowercase',
            'description' =>
                'Debe contener al menos una letra minúscula',
            'passed' => $lowercase
        ];

        $rules[] = [
            'rule' => 'hasNumber',
            'description' =>
                'Debe contener al menos un número',
            'passed' => $number
        ];

        $rules[] = [
            'rule' => 'hasSymbol',
            'description' =>
                'Debe contener al menos un símbolo',
            'passed' => $symbol
        ];

        $rules[] = [
            'rule' => 'noCommonPassword',
            'description' =>
                'No debe ser una contraseña común',
            'passed' => $common
        ];

        $rules[] = [
            'rule' => 'noSequentialChars',
            'description' =>
                'No debe contener caracteres secuenciales',
            'passed' => $sequential
        ];

        if (
            $username !== null &&
            $username !== ''
        ) {
            $rules[] = [
                'rule' =>
                    'noUsernameInPassword',
                'description' =>
                    'No debe contener el nombre de usuario',
                'passed' =>
                    $usernamePassed
            ];
        }

        foreach ($rules as $rule) {
            if (!$rule['passed']) {
                $suggestions[] =
                    $rule['description'];
            }
        }

        $passed = 0;

        foreach ($rules as $rule) {
            if ($rule['passed']) {
                $passed++;
            }
        }

        $score = (int) round(
            ($passed / count($rules)) * 100
        );

        if ($score < 30) {
            $strength = 'muy_debil';
        } elseif ($score < 50) {
            $strength = 'debil';
        } elseif ($score < 70) {
            $strength = 'media';
        } elseif ($score < 90) {
            $strength = 'fuerte';
        } else {
            $strength = 'muy_fuerte';
        }

        return [
            'password' => $password,
            'isValid' => $score === 100,
            'strength' => $strength,
            'score' => $score,
            'rules' => $rules,
            'suggestions' => $suggestions
        ];
    }

    private static function hasSequentialCharacters(
        $password
    ) {
        $password = strtolower($password);

        for (
            $i = 0;
            $i < strlen($password) - 2;
            $i++
        ) {
            $a = ord($password[$i]);
            $b = ord($password[$i + 1]);
            $c = ord($password[$i + 2]);

            if (
                ($b === $a + 1 &&
                 $c === $b + 1)
                ||
                ($b === $a - 1 &&
                 $c === $b - 1)
            ) {
                return true;
            }
        }

        return false;
    }
}