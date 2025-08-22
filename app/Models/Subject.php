<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'class_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'class_id' => 'array', // It's good practice to add this cast
    ];

    // The academicClass() relationship has been removed because class_id is a JSON column.

    /**
     * Get the chapters for this subject.
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }
    public function academicClass()
    {
        // adjust 'class_id' if your FK is named differently
        return $this->belongsTo(AcademicClassModel::class, 'class_id');
    }


    
}