<?php

namespace App\Helpers;

class ContarLetrasHelper
{
    public static function contar(string $texto): array
    {
        $texto = strtoupper(str_replace(' ', '', $texto)); 
        return self::contarRecursivo(str_split($texto));
    }

    private static function contarRecursivo(array $letras, array $frecuencia = []): array
    {
        if (empty($letras)) return $frecuencia;

        $letra = array_shift($letras);
        $frecuencia[$letra] = ($frecuencia[$letra] ?? 0) + 1;

        return self::contarRecursivo($letras, $frecuencia);
    }
}
