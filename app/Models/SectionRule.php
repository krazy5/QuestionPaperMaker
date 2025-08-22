<?php
// File: app/Models/SectionRule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SectionRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'blueprint_section_id',
        'question_type',
        'marks_per_question',
        'number_of_questions_to_select',
        'total_questions_to_display',
    ];

    /**
     * Get the section that this rule belongs to.
     */
    public function blueprintSection(): BelongsTo
    {
       return $this->belongsTo(BlueprintSection::class, 'blueprint_section_id');
    }
}