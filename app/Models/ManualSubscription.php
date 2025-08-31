<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualSubscription extends Model
{
    protected $table = 'manual_subscriptions';

    protected $fillable = [
        'user_id',
        'plan_name',
        'starts_at',
        'ends_at',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];
}
