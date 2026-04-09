<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BodyMeasurement extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'date',
        'weight_kg',
        'body_fat_pct',
        'bicep_left_cm',
        'bicep_right_cm',
        'chest_cm',
        'waist_cm',
        'abdomen_cm',
        'hip_cm',
        'thigh_left_cm',
        'thigh_right_cm',
        'calf_left_cm',
        'calf_right_cm',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date'           => 'date',
            'weight_kg'      => 'decimal:2',
            'body_fat_pct'   => 'decimal:2',
            'bicep_left_cm'  => 'decimal:2',
            'bicep_right_cm' => 'decimal:2',
            'chest_cm'       => 'decimal:2',
            'waist_cm'       => 'decimal:2',
            'abdomen_cm'     => 'decimal:2',
            'hip_cm'         => 'decimal:2',
            'thigh_left_cm'  => 'decimal:2',
            'thigh_right_cm' => 'decimal:2',
            'calf_left_cm'   => 'decimal:2',
            'calf_right_cm'  => 'decimal:2',
            'created_at'     => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
