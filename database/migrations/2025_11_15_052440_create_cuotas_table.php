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
        Schema::create('cuotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas');
            
            $table->string('descripcion'); // "Matrícula", "Cuota 1 de 5"
            $table->decimal('monto_cuota', 10, 2);
            $table->date('fecha_vencimiento');
            
            $table->enum('estado_cuota', ['Pendiente', 'Pagada', 'Vencida'])->default('Pendiente');
            
            // Detalles del pago (cuando se pague)
            $table->timestamp('fecha_pago')->nullable();
            $table->string('metodo_pago')->nullable();
            $table->string('transaccion_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuotas');
    }
};
