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
    Schema::create('metodo_pagos', function (Blueprint $table) {
        $table->id();
        
        // Ej: "BCP", "Interbank", "Yape", "Plin"
        $table->string('nombre_banco'); 
        
        // Ej: "Cuenta Bancaria" o "Billetera Digital"
        $table->enum('tipo', ['Cuenta Bancaria', 'Billetera Digital']);
        
        // El número de cuenta o celular
        $table->string('numero_cuenta'); 
        
        // Opcional: El CCI para bancos
        $table->string('cci')->nullable(); 
        
        // Ej: "Edesip SAC" o "Juan Perez"
        $table->string('titular'); 
        
        // Ruta de la imagen del QR (puede ser null si es solo cuenta bancaria)
        $table->string('qr_imagen')->nullable();
        
        // Instrucciones extra. Ej: "Enviar captura al WhatsApp..."
        $table->text('instrucciones')->nullable();
        
        // Para ocultar cuentas viejas sin borrarlas
        $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metodo_pagos');
    }
};
