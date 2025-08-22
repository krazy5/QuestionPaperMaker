<?php

namespace App\Livewire\Institute;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Paper;
use App\Models\Board;
use App\Models\AcademicClassModel;
use App\Models\Subject;

class PapersTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Filters (persist in URL – Livewire v3)
    #[Url(as: 'q', except: '')]
    public string $q = '';

    #[Url(except: null)]
    public ?int $board_id = null;

    #[Url(except: null)]
    public ?int $class_id = null;

    #[Url(except: null)]
    public ?int $subject_id = null;

    // Pagination page -> keep in URL too
    #[Url(except: 1)]
    public int $page = 1;

    // Dropdown data
    public $boards = [];
    public $classes = [];

    public function mount(): void
    {
        $this->boards  = Board::orderBy('name')->get(['id','name']);
        $this->classes = AcademicClassModel::orderBy('name')->get(['id','name']);
    }

    /** Reset subject + page when class changes */
    public function updatedClassId(): void
    {
        $this->subject_id = null;
        $this->resetPage();
    }

    /** Reset page when any filter changes */
    public function updated($name, $value): void
    {
        if (in_array($name, ['q','board_id','class_id','subject_id'])) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->q = '';
        $this->board_id = null;
        $this->class_id = null;
        $this->subject_id = null;
        $this->resetPage();
    }

    /** Dependent subjects list */
    public function getSubjectsProperty()
    {
        if (!$this->class_id) return collect();
        return Subject::where('class_id', $this->class_id)
            ->orderBy('name')
            ->get(['id','name']);
    }

    /** Delete paper (auth-guarded) */
    public function deletePaper(int $id): void
    {
        $paper = Paper::where('id', $id)
            ->where('institute_id', auth()->id())
            ->firstOrFail();

        // Clean up pivot (if you store selected questions)
        $paper->questions()->detach();

        $paper->delete();

        // If the current page became empty, go back a page
        if ($this->page > 1 && $this->isCurrentPageEmptyAfterDelete()) {
            $this->page = max(1, $this->page - 1);
        }

        $this->dispatch('toast', message: 'Paper deleted');
    }

    protected function isCurrentPageEmptyAfterDelete(): bool
    {
        $query = Paper::query()->where('institute_id', auth()->id());

        $search = trim((string) $this->q);
        if ($search !== '') $query->where('title', 'like', '%'.$search.'%');
        if (!is_null($this->board_id))   $query->where('board_id',   $this->board_id);
        if (!is_null($this->class_id))   $query->where('class_id',   $this->class_id);
        if (!is_null($this->subject_id)) $query->where('subject_id', $this->subject_id);

        $total   = (int) $query->count();
        $perPage = 10;
        $maxPage = max(1, (int) ceil($total / $perPage));

        return $this->page > $maxPage;
    }

    public function render()
    {
        $query = Paper::query()
            ->where('institute_id', auth()->id())
            ->with(['subject.academicClass']); // eager load for table

        $search = trim($this->q);
        if ($search !== '') {
            $query->where('title', 'like', '%'.$search.'%');
        }

        if (!is_null($this->board_id))   $query->where('board_id',   $this->board_id);
        if (!is_null($this->class_id))   $query->where('class_id',   $this->class_id);
        if (!is_null($this->subject_id)) $query->where('subject_id', $this->subject_id);

        $papers = $query->latest()->paginate(10);

        return view('livewire.institute.papers-table', [
            'papers'   => $papers,
            'subjects' => $this->subjects, // computed
        ]);
    }
}
