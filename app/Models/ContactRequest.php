<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactRequest extends Model
{
    protected $fillable = [
        'user_id','plan_name','name','email','phone',
        'preferred_date','preferred_time','message','status',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'preferred_time' => 'datetime:H:i',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
