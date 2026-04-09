<?php

namespace App\Policies;

use App\Models\BodyMeasurement;
use App\Models\User;

class BodyMeasurementPolicy
{
    public function update(User $user, BodyMeasurement $measurement): bool
    {
        return $user->id === $measurement->user_id;
    }

    public function delete(User $user, BodyMeasurement $measurement): bool
    {
        return $user->id === $measurement->user_id;
    }
}
