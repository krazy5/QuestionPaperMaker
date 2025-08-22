<?php

namespace App\Livewire\Institute;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Paper;
use App\Models\Chapter;
use App\Models\Question;
use Livewire\Attributes\Url;

class SelectQuestions extends Component
{
    use WithPagination;

    public Paper $paper;

    // UI state
   public string $selectedChapter = 'all';
   public array $types = [];
   #[Url(as: 'chapter', except: 'all')]
   

    public int $perPage = 10;

   // For Livewire 3, use this syntax:
   protected $queryString = [
       'selectedChapter' => ['except' => 'all', 'as' => 'chapter'],
       'types' => ['except' => [], 'as' => 'types'],
       'page' => ['except' => 1],
   ];

   public function mount(Paper $paper, string $selectedChapter = 'all', array $types = [])
   {
       $this->paper = $paper;
       
       // Initialize from request parameters
       $this->selectedChapter = request()->query('chapter', $selectedChapter);
       $this->types = request()->query('types', $types);
   }
 

    // Watch for changes and reset pagination
    public function updatedSelectedChapter()
    {
        $this->resetPage();
    }

    public function updatedTypes()
    {
        $this->resetPage();
    }

    public function getExistingQuestionIdsProperty()
    {
        // IDs of already-attached questions (for checked state)
        return $this->paper->questions()->pluck('questions.id');
    }

    public function render()
    {
        $chapters = Chapter::where('subject_id', $this->paper->subject_id)
            ->orderBy('name')->get();

        $query = Question::query()
            ->where('board_id', $this->paper->board_id)
            ->whereJsonContains('class_id', $this->paper->class_id)
            ->whereJsonContains('subject_id', $this->paper->subject_id)
            ->where(fn ($q) => $q->where('approved', true)->orWhere('institute_id', auth()->id()));

        // Apply chapter filter
        if ($this->selectedChapter !== 'all' && $this->selectedChapter !== '') {
            $query->where('chapter_id', $this->selectedChapter);
        }

        // Apply type filters
        if (!empty($this->types)) {
            $query->whereIn('question_type', $this->types);
        }

        $questions = $query->latest()->paginate($this->perPage);

        return view('livewire.institute.select-questions', [
            'chapters' => $chapters,
            'questions' => $questions,
            'existingQuestionIds' => $this->existingQuestionIds,
        ]);
    }
}
