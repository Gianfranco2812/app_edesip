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
            
            // Relaciones
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->foreignId('plantilla_contrato_id')->constrained('plantillas_contrato');

            $table->string('token_acceso')->unique(); 
            
            // 1. Primero defines el HTML
            $table->longText('contenido_generado'); 
            
            // 2. Luego defines el PDF (Simplemente poniéndolo debajo)
            // IMPORTANTE: Quité el ->after() porque causaba el error
            $table->string('ruta_pdf')->nullable(); 
            
            // Estados y Firmas
            $table->enum('estado', ['Pendiente', 'Firmado'])->default('Pendiente');
            $table->timestamp('fecha_firma')->nullable();
            $table->string('ip_firma', 45)->nullable();
            
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
