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
    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    /**
     * Get the class that this blueprint belongs to.
     */
    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClassModel::class, 'class_id');
    }
}