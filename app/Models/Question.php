<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'source', 'institute_id', 'board_id',
        'class_id', 'subject_id', 'chapter_id',
        'question_text', 'question_image_path', 'question_type',
        'options', 'correct_answer', 'answer_text', 'solution_text',
        'answer_image_path', 'marks', 'difficulty', 'approved',
    ];

    /**
     * IMPORTANT: remove array casts for class_id/subject_id
     * (custom accessors below will handle JSON safely).
     */
    protected $casts = [
        'approved' => 'boolean',
        // 'class_id' => 'array',
        // 'subject_id' => 'array',
    ];

    /* ---------- helpers ---------- */

    private static function normalizeToArray(mixed $value): array
    {
        if (is_array($value)) {
            return array_values($value);
        }
        if (is_null($value) || $value === '') {
            return [];
        }
        if (is_numeric($value)) {
            return [(int) $value];
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return array_values($decoded);
            }
        }
        return [];
    }

    /* ---------- JSON attributes ---------- */

    // options (array< string > or null)
    protected function options(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === null ? null : (json_decode($value, true) ?: []),
            set: fn ($value) => $value === null ? null : json_encode(array_values((array) $value))
        );
    }

    // class_id as JSON array
    protected function classId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => self::normalizeToArray($value),
            set: fn ($value) => json_encode(self::normalizeToArray($value))
        );
    }

    // subject_id as JSON array
    protected function subjectId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => self::normalizeToArray($value),
            set: fn ($value) => json_encode(self::normalizeToArray($value))
        );
    }

    /* ---------- relationships ---------- */

    public function institute(): BelongsTo
    {
        return $this->belongsTo(User::class, 'institute_id');
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

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

    /* ---------- convenient computed props ---------- */

    // First subject model (so `$question->subject?->name` keeps working)
    protected function subject(): Attribute
    {
        return Attribute::make(
            get: function () {
                $ids = $this->subject_id;
                if (!$ids) return null;
                return Subject::whereIn('id', $ids)->orderBy('name')->first();
            }
        );
    }

    protected function subjectsList(): Attribute
    {
        return Attribute::make(
            get: function () {
                $ids = $this->subject_id;
                if (!$ids) return collect();
                return Subject::whereIn('id', $ids)->orderBy('name')->get();
            }
        );
    }

    protected function firstClass(): Attribute
    {
        return Attribute::make(
            get: function () {
                $ids = $this->class_id;
                if (!$ids) return null;
                return AcademicClassModel::whereIn('id', $ids)->orderBy('name')->first();
            }
        );
    }

    protected function classesList(): Attribute
    {
        return Attribute::make(
            get: function () {
                $ids = $this->class_id;
                if (!$ids) return collect();
                return AcademicClassModel::whereIn('id', $ids)->orderBy('name')->get();
            }
        );
    }

    /* ---------- query scopes for JSON arrays ---------- */

    public function scopeWhereHasClass($query, int $classId)
    {
        return $query->whereJsonContains('class_id', $classId);
    }

    public function scopeWhereHasSubject($query, int $subjectId)
    {
        return $query->whereJsonContains('subject_id', $subjectId);
    }
}
