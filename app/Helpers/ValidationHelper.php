<?php

namespace App\Helpers;

class ValidationHelper
{
    /**
     * Valida que un email tenga formato correcto.
     *
     * @param string $email
     * @return bool
     */
    public static function isValidEmail(string $email): bool
    {
        // Valida RFC y dominio (DNS)
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
