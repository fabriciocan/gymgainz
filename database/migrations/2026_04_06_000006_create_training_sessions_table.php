<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('workout_id');
            $table->foreign('user_id', 'fk_training_sessions_user_id')
                ->references('id')->on('users')->onDelete('cascade');
            $table->foreign('workout_id', 'fk_training_sessions_workout_id')
                ->references('id')->on('workouts')->onDelete('cascade');
            $table->index('user_id');
            $table->index('workout_id');
            $table->date('date');
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_sessions');
    }
};
