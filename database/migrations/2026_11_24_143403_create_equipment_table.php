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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('inventory_code')->unique(); // ex: CAM-03 (empêche le doublon ARD-04)
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Désignation
            $table->string('condition')->default('good'); // 'good', 'worn', 'damaged', 'out_of_service'
            $table->string('location')->default('A.04');
            $table->text('default_accessories')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
