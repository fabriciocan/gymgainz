<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->constrained()->onDelete('cascade');
            $table->index('workout_id');
            $table->foreignId('exercise_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('sets')->default(3);
            $table->unsignedTinyInteger('reps')->default(10);
            $table->unsignedSmallInteger('rest_seconds')->default(60);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_exercises');
    }
};
