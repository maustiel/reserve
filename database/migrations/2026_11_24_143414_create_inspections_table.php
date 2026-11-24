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
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // 'departure' ou 'return'
            $table->string('condition'); // 'good', 'worn', 'damaged'
            $table->text('accessories')->nullable();
            $table->text('notes')->nullable();
            $table->string('photo_path')->nullable();
            $table->boolean('borrower_signed')->default(false);
            $table->boolean('manager_signed')->default(false);
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();

            $table->unique(['loan_id', 'type']); // 1 seul constat départ et 1 seul retour par prêt
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
