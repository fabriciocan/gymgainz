<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('body_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->index('user_id');
            $table->date('date');
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->decimal('body_fat_pct', 4, 2)->nullable();
            $table->decimal('bicep_cm', 5, 2)->nullable();
            $table->decimal('chest_cm', 5, 2)->nullable();
            $table->decimal('waist_cm', 5, 2)->nullable();
            $table->decimal('hip_cm', 5, 2)->nullable();
            $table->decimal('thigh_cm', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('body_measurements');
    }
};
