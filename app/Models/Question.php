<?php

namespace App\Models;

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
    protected $fillable = [
        'source',
        'institute_id',
        'board_id',
        'class_id',
        'subject_id',
        'chapter_id',
        'question_text',
        'question_type',
        'options',
        'correct_answer',
        'answer_text',
        'solution_text',
        'marks',
        'difficulty',
        'approved',
    ];

    /**
     * The attributes that should be cast.
     * This helps handle special data types automatically.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'array',    // Convert the JSON 'options' column to a PHP array
        'approved' => 'boolean', // Convert the 'approved' column to true/false
    ];

    // --- RELATIONSHIPS ---

    /**
     * Get the institute that created the question (if any).
     * This relationship is optional because an admin might create questions.
     */
    public function institute(): BelongsTo
    {
        return $this->belongsTo(User::class, 'institute_id');
    }

    /**
     * Get the board for this question.
     */
    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    /**
     * Get the class for this question.
     */
    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClassModel::class, 'class_id');
    }

    /**
     * Get the subject for this question.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the chapter for this question.
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    /**
     * The papers that this question belongs to.
     */
    public function papers(): BelongsToMany
    {
        return $this->belongsToMany(Paper::class, 'paper_questions');
    }
}