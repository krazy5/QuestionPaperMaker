<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute; // <-- Import this
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Question extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // In app/Models/Question.php

    protected $fillable = [
        'source',
        'institute_id',
        'board_id',
        'class_id',
        'subject_id',
        'chapter_id',
        'question_text',
        'question_image_path', // This is correct
        'question_type',
        'options',
        'correct_answer',
        'answer_text',
        'solution_text',
        'answer_image_path', // <-- ADD THIS LINE
        'marks',
        'difficulty',
        'approved',
    ];

    /**
     * The attributes that should be cast.
     * We will keep this for good practice.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'approved' => 'boolean',
    ];

    /**
     * THIS IS THE NEW, ROBUST FIX.
     *
     * This is an accessor that automatically decodes the 'options' attribute
     * from a JSON string into a PHP array whenever you access it.
     * This is more explicit than $casts and will solve the issue.
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
        // Note: You may need to add 'use App\Models\User;' at the top
        return $this->belongsTo(User::class, 'institute_id');
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClassModel::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function papers(): BelongsToMany
    {
        return $this->belongsToMany(Paper::class, 'paper_questions');
    }
}
