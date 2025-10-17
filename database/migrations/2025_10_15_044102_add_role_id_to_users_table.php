<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        public function up(): void
    {
        Schema::create('contribuyentes', function (Blueprint $table) {
            $table->id();

            // Campos obligatorios
            $table->string('tipo_documento', 10);
            $table->string('documento', 20)->unique(); // Validación unique requerida
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            
            // Campo autogenerado por el modelo
            $table->string('nombre_completo', 201); // 100 (nombres) + 100 (apellidos) + 1 (espacio)
            
            // Campos opcionales (nullable)
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('celular', 20)->nullable();
            
            // Campo obligatorio con validación unique
            $table->string('email')->unique(); // Por defecto es 255, suficiente para email

            // Campo de registro y fecha automática
            $table->string('usuario', 100);
            $table->timestamp('fecha_registro'); // Campo 'fecha del sistema (automática)'
            
            $table->timestamps(); // created_at y updated_at (Laravel por defecto)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contribuyentes');
    }
};
