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
        Schema::table('reporte_pagos', function (Blueprint $table) {
            $table->foreignId('cuota_id')->nullable()->after('venta_id')->constrained('cuotas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reporte_pagos', function (Blueprint $table) {
            //
        });
    }
};
