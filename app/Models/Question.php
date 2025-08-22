<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'source', 'institute_id', 'board_id', 'class_id', 'subject_id',
        'chapter_id', 'question_text', 'question_image_path', 'question_type',
        'options', 'correct_answer', 'answer_text', 'solution_text',
        'answer_image_path', 'marks', 'difficulty', 'approved',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'approved' => 'boolean',
        'class_id' => 'array',
        'subject_id' => 'array',
    ];

    /**
     * Accessor/Mutator for the 'options' attribute.
     */
    protected function options(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }

    // --- RELATIONSHIPS ---

    public function institute(): BelongsTo
    {
        return $this->belongsTo(User::class, 'institute_id');
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    // The academicClass() and subject() relationships have been removed
    // because class_id and subject_id are JSON columns.

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function papers()
    {
        return $this->belongsToMany(
            \App\Models\Paper::class,
            'paper_question',
            'question_id',
            'paper_id'
        )->withPivot(['marks','section_rule_id','sort_order'])
        ->withTimestamps();
    }

}