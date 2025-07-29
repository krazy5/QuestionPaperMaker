<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'class_id'];

    /**
     * Get the class that this subject belongs to.
     */
    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClassModel::class, 'class_id');
    }

    /**
     * Get the chapters for this subject.
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }
}