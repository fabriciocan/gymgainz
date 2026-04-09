<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'manager',
        'department',
        'employeeNumber',
        'dataAssinaturaTermo',
        'nomeSetor',
        'isExterno',
        'orgao',
        'cpf',
        'telefone',
        'empresa',
        'cargo',
        'data_nascimento',
        'status',
        'active',
        'guid',
        'domain',
        'trial_ends_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'active'            => 'boolean',
            'isExterno'         => 'boolean',
            'trial_ends_at'     => 'datetime',
        ];
    }

    public function subscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): ?\Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latestOfMany();
    }

    public function workouts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Workout::class);
    }

    public function trainingSessions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TrainingSession::class);
    }

    public function bodyMeasurements(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BodyMeasurement::class);
    }

    public function hasActiveAccess(): bool
    {
        if ($this->trial_ends_at && $this->trial_ends_at->isFuture()) {
            return true;
        }

        $subscription = $this->subscriptions()
            ->where('status', 'active')
            ->where('current_period_end', '>', now())
            ->latest()
            ->first();

        return $subscription !== null;
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }
        return ! ! $role->intersect($this->roles)->count();
    }

    public function hasAnyRole($roles): bool
    {
        if (is_string($roles)) {
            return $this->hasRole($roles);
        }
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        return false;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPassword($token));
    }
}
