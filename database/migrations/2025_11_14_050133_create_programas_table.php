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
        Schema::create('programas', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('tipo_programa_id')->constrained('tipos_programa');
            $table->foreignId('plantilla_contrato_id')->constrained('plantillas_contrato');

            $table->string('nombre'); 
            $table->text('descripcion_detallada')->nullable();
            $table->integer('horas_totales')->nullable();
            $table->string('imagen_promocional_url')->nullable();
            $table->string('brochure_pdf_url')->nullable();
            $table->string('estado')->default('Activo'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programas');
    }
};
