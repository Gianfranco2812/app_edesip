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
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('programa_id')->constrained('programas');

            $table->string('codigo_grupo')->unique(); 
            $table->date('fecha_inicio');
            $table->date('fecha_termino');
            $table->string('modalidad'); 
            $table->string('horario_texto'); 
            $table->string('estado')->default('Próximo'); 

            // Campos Financieros
            $table->decimal('costo_total', 10, 2); 
            $table->decimal('costo_matricula', 10, 2)->default(0);
            $table->integer('numero_cuotas')->default(1);
            $table->string('texto_promocional')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};
