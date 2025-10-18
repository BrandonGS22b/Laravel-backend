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

}