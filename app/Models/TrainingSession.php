<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingSession extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'workout_id',
        'date',
        'duration_minutes',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date'       => 'date',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    public function sets(): HasMany
    {
        return $this->hasMany(SessionSet::class, 'session_id');
    }
}
