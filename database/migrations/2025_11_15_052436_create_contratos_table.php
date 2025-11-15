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
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas');
            $table->foreignId('plantilla_contrato_id')->constrained('plantillas_contrato');

            $table->string('token_acceso')->unique(); // Para el link de firma/confirmación
            $table->longText('contenido_generado'); // El HTML exacto que se generó
            
            $table->enum('estado', ['Pendiente', 'Confirmado'])->default('Pendiente');
            
            $table->timestamp('fecha_confirmacion')->nullable();
            $table->string('ip_confirmacion', 45)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
