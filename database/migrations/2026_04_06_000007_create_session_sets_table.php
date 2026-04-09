<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')
                ->constrained('training_sessions')
                ->onDelete('cascade')
                ->index();
            $table->foreignId('workout_exercise_id')
                ->constrained('workout_exercises')
                ->onDelete('cascade');
            $table->unsignedTinyInteger('set_number');
            $table->unsignedTinyInteger('reps_done');
            $table->decimal('weight_kg', 6, 2)->default(0);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_sets');
    }
};
