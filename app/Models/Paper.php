<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Paper extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * These are the fields you'll fill out when an institute creates a paper.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'institute_id',
        'board_id',
        'class_id',
        'subject_id',
        'title',
        'instructions',
        'total_marks',
        'status',
        'time_allowed',
        'exam_date', 
    ];

    // --- RELATIONSHIPS ---

    /**
     * Get the institute (user) that owns the paper.
     * A Paper belongs to one User.
     */
    public function institute(): BelongsTo
    {
        return $this->belongsTo(User::class, 'institute_id');
    }

    /**
     * Get the board associated with the paper.
     */
    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    /**
     * Get the class associated with the paper.
     * Note: We use academicClass() because class() is a reserved keyword in PHP.
     */
    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClassModel::class, 'class_id');
    }

    /**
     * Get the subject associated with the paper.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * The questions that belong to the paper.
     * This is a many-to-many relationship through the 'paper_questions' pivot table.
     */
    public function questions(): BelongsToMany
    {
        // We must specify the pivot table and the extra columns it holds.
        return $this->belongsToMany(Question::class, 'paper_questions')
                    ->withPivot('marks', 'sort_order');
    }
}