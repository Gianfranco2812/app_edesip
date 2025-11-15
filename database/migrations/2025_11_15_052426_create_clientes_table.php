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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('email')->unique();
            $table->string('telefono');

            $table->string('tipo_documento')->nullable();
            $table->string('numero_documento')->nullable()->unique();
            
            $table->string('direccion')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            
            $table->enum('estado', [
                'Prospecto', 
                'En Proceso', 
                'Confirmado', 
                'Alumno Activo', 
                'Finalizado'
            ])->default('Prospecto');

            // Relación con el Asesor/Vendedor que lo creó
            $table->foreignId('creado_por_vendedor_id')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps(); // Esto maneja 'fecha_creacion'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
