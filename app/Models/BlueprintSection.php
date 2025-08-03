<?php
// File: app/Models/BlueprintSection.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlueprintSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'paper_blueprint_id',
        'name',
        'instructions',
        'sort_order',
    ];

    /**
     * Get the blueprint that this section belongs to.
     */
    public function paperBlueprint(): BelongsTo
    {
        return $this->belongsTo(PaperBlueprint::class);
    }

    /**
     * Get the rules for this section.
     */
    public function rules(): HasMany
    {
        return $this->hasMany(SectionRule::class);
    }
}