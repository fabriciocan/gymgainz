<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->index('user_id');
            $table->string('name');
            $table->enum('muscle_group', [
                'chest', 'back', 'shoulders', 'biceps', 'triceps',
                'forearms', 'core', 'glutes', 'quadriceps', 'hamstrings',
                'calves', 'full_body',
            ]);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
