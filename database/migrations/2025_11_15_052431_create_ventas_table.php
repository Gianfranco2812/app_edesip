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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('grupo_id')->constrained('grupos');
            $table->foreignId('vendedor_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamp('fecha_venta');
            
            $table->enum('estado', ['En Proceso', 'Cerrada', 'Anulada']);

            // Campos "Snapshot" para congelar los datos financieros de la venta
            $table->decimal('costo_total_venta', 10, 2);
            $table->decimal('costo_matricula_venta', 10, 2);
            $table->integer('nro_cuotas_venta');
            $table->string('texto_promocional_venta')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
