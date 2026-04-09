<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionSet extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'session_id',
        'workout_exercise_id',
        'set_number',
        'reps_done',
        'weight_kg',
    ];

    protected function casts(): array
    {
        return [
            'weight_kg'  => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class, 'session_id');
    }

    public function workoutExercise(): BelongsTo
    {
        return $this->belongsTo(WorkoutExercise::class);
    }
}
