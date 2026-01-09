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
        Schema::create('reporte_pagos', function (Blueprint $table) {
            $table->id();
            // Relacionamos con la Venta (Matrícula) para saber de quién es el pago
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            
            $table->decimal('monto', 10, 2);
            $table->string('metodo_pago'); // 'Yape', 'Plin', 'BCP', etc.
            $table->string('numero_operacion'); // El código único del banco
            $table->string('comprobante_imagen')->nullable(); // Ruta de la foto
            
            // Estados: Pendiente (Recién enviado), Aprobado (Ya lo revisaste), Rechazado (Era falso/ilegible)
            $table->enum('estado', ['Pendiente', 'Aprobado', 'Rechazado'])->default('Pendiente');
            
            $table->text('observacion_admin')->nullable(); // Por si rechazas, explicar por qué
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reporte_pagos');
    }
};
