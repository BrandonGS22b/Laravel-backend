<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contribuyente extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_documento',
        'documento',
        'nombres',
        'apellidos',
        'nombre_completo',
        'direccion',
        'telefono',
        'celular',
        'email',
        'usuario',
        'fecha_registro'
    ];


    
    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    protected static function booted()
{
    static::creating(function ($contribuyente) {
        $contribuyente->nombre_completo = trim($contribuyente->nombres . ' ' . $contribuyente->apellidos);
    });
}
/*
    protected $appends = ['nombre_completo'];

    protected static function booted()
    {
         static::saving(function ($contribuyente) {
            // Solo separar nombres y apellidos si es NIT
            if (strtoupper($contribuyente->tipo_documento) === 'NIT') {
                $parts = explode(' ', trim($contribuyente->nombres . ' ' . $contribuyente->apellidos));
                if (count($parts) > 1) {
                   
                    if (count($parts) >= 3) {
                        $contribuyente->nombres = implode(' ', array_slice($parts, 0, -2));
                        $contribuyente->apellidos = implode(' ', array_slice($parts, -2));
                    } else {
                        $contribuyente->nombres = $parts[0];
                        $contribuyente->apellidos = $parts[1] ?? '';
                    }
                }
            }

            // Generar nombre completo
            $contribuyente->nombre_completo = trim($contribuyente->nombres . ' ' . $contribuyente->apellidos);
        });
    }

    public function getNombreCompletoAttribute($value)
    {
        return $value ?? trim($this->nombres . ' ' . $this->apellidos);
    }
        */
}