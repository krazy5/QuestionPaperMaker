<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;

/**
 * App\Models\User
 *
 * The User model represents both admin and institute accounts. For institutes,
 * this model exposes relationships to manual subscriptions as well as helper
 * methods for checking the active subscription.
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'institute_name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationship: subscriptions via Laravel Cashier (Stripe subscriptions).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Relationship: papers created by an institute.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function papers()
    {
        return $this->hasMany(Paper::class, 'institute_id');
    }

    /**
     * Relationship: questions authored by an institute.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions()
    {
        return $this->hasMany(Question::class, 'institute_id');
    }

    /**
     * Relationship: manual subscriptions assigned by an admin. These are custom
     * plans outside of Stripe that have specific start and end datetimes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function manualSubscriptions()
    {
        return $this->hasMany(\App\Models\ManualSubscription::class, 'user_id');
    }

    /**
     * Helper to retrieve the currently active manual subscription, if any.
     *
     * A manual subscription is considered active when its status is 'active',
     * its start date/time has passed, and its end date/time is in the future.
     *
     * @return \App\Models\ManualSubscription|null
     */
    public function activeManualSubscription()
    {
        $now = now();
        return $this->manualSubscriptions()
            ->where('status', 'active')
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>',  $now)
            ->orderBy('ends_at', 'asc')
            ->first();
    }
}