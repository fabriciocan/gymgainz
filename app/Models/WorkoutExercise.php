<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkoutExercise extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'workout_id',
        'exercise_id',
        'sets',
        'reps',
        'rest_seconds',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public function sessionSets(): HasMany
    {
        return $this->hasMany(SessionSet::class);
    }
}
