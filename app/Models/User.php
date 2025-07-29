<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany; // Import HasMany

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'institute_name', // ADDED: So you can save the institute's name
        'role',           // ADDED: So you can set the user's role
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- RELATIONSHIPS (Add this entire section) ---

    /**
     * Get all of the papers for the User (Institute).
     * This defines a one-to-many relationship. One institute can have many papers.
     */
    public function papers(): HasMany
    {
        return $this->hasMany(Paper::class, 'institute_id');
    }

    /**
     * Get all of the questions for the User (Institute).
     * One institute can create many questions.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'institute_id');
    }

    /**
     * Get all of the subscriptions for the User.
     * One user can have many subscription records over time.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'user_id');
    }
}