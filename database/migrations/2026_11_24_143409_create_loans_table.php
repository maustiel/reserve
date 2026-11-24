<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // ex: 2026-087
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('requested'); // 'requested', 'active', 'returned', 'cancelled'
            $table->string('purpose')->nullable();
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('handed_over_at')->nullable();
            $table->date('due_at');
            $table->timestamp('returned_at')->nullable();
            $table->boolean('extended_once')->default(false);
            $table->timestamps();
        });
        // Règle R1 en SQLite : impossible d'avoir 2 prêts ouverts pour le même appareil (même simultanément)
        DB::statement("
    CREATE UNIQUE INDEX loans_one_open_per_equipment
    ON loans (equipment_id)
    WHERE status IN ('requested', 'active')
");
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
