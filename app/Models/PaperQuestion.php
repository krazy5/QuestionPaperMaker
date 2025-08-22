<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaperQuestion extends Model
{
    //

     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'paper_questions';

    public function questions()
{
    return $this->belongsToMany(Question::class, 'paper_questions')
                ->withPivot('marks', 'section_rule_id', 'correct_answer')
                ->withTimestamps();
}

}
