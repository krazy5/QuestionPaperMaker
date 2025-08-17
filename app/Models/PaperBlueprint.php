<?php
// File: app/Models/PaperBlueprint.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaperBlueprint extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'board_id',
        'class_id',
        'subject_id',
        'total_marks', 
        'institute_id',      // ✅ Add this line
        'selected_chapters',
    ];

    protected $casts = [
        'total_marks' => 'integer', // ✅ Add this line
        'selected_chapters' => 'array', 
    ];
    /**
     * Get the sections for this blueprint.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(BlueprintSection::class)->orderBy('sort_order');
    }

    /**
     * Get the board that this blueprint belongs to.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the class that this blueprint belongs to.
     */
    public function academicClass()
    {
        return $this->belongsTo(AcademicClassModel::class, 'class_id');
    }

    /**
     * Get the board that this blueprint belongs to.
     */
    public function board()
    {
        return $this->belongsTo(Board::class);
    }
}