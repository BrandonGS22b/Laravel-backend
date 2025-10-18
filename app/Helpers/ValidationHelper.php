<?php

namespace App\Helpers;

class ValidationHelper
{

    public static function isValidEmail(string $email): bool
    {
        // Valida RFC y dominio (DNS)
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
