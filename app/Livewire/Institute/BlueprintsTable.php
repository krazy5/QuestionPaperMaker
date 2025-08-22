<?php

namespace App\Livewire\Institute;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\PaperBlueprint;
use App\Models\Board;
use App\Models\AcademicClassModel;
use App\Models\Subject;

class BlueprintsTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Filters (persist in URL – LW v3)
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

    /** Delete blueprint (with auth guard) */
    public function delete(int $id): void
    {
        $bp = PaperBlueprint::where('id', $id)
            ->where('institute_id', auth()->id())
            ->firstOrFail();

        // If you have cascade rules, this is enough.
        // Otherwise delete sections/rules here as needed.
        $bp->delete();

        // If last item on page deleted, bounce back a page
        if ($this->page > 1 && $this->currentPageEmpty()) {
            $this->page = max(1, $this->page - 1);
        }

        // Re-render
        $this->dispatch('toast', message: 'Blueprint deleted');
    }

    /** Delete blueprint (auth-guarded) */
    public function deleteBlueprint(int $id): void
    {
        $bp = PaperBlueprint::where('id', $id)
            ->where('institute_id', auth()->id())
            ->firstOrFail();

        $bp->delete();

        // If the current page becomes empty, move back a page.
        if ($this->page > 1 && $this->isCurrentPageEmptyAfterDelete()) {
            $this->page = max(1, $this->page - 1);
        }

        // Trigger a re-render; optional toast if you have one
        $this->dispatch('toast', message: 'Blueprint deleted');
    }

    protected function isCurrentPageEmptyAfterDelete(): bool
    {
        // Count using the SAME filters to know total pages correctly
        $query = PaperBlueprint::query()->where('institute_id', auth()->id());

        $search = trim((string) $this->q);
        if ($search !== '') {
            $query->where('name', 'like', '%'.$search.'%');
        }
        if (!is_null($this->board_id))   $query->where('board_id',   $this->board_id);
        if (!is_null($this->class_id))   $query->where('class_id',   $this->class_id);
        if (!is_null($this->subject_id)) $query->where('subject_id', $this->subject_id);

        $total   = (int) $query->count();
        $perPage = 10;
        $maxPage = max(1, (int) ceil($total / $perPage));

        return $this->page > $maxPage;
    }

    protected function currentPageEmpty(): bool
    {
        $count = PaperBlueprint::where('institute_id', auth()->id())->count();
        $perPage = 10;
        $maxPage = (int) ceil($count / $perPage);
        return $this->page > $maxPage;
    }

    public function render()
    {
        $query = PaperBlueprint::query()
            ->where('institute_id', auth()->id())
            ->with(['board','academicClass','subject']);

        $search = trim($this->q);
        if ($search !== '') {
            $like = '%'.$search.'%';
            $query->where('name', 'like', $like);
        }

        if (!is_null($this->board_id))   $query->where('board_id',   $this->board_id);
        if (!is_null($this->class_id))   $query->where('class_id',   $this->class_id);
        if (!is_null($this->subject_id)) $query->where('subject_id', $this->subject_id);

        $blueprints = $query->latest()->paginate(10);

        return view('livewire.institute.blueprints-table', [
            'blueprints' => $blueprints,
            'subjects'   => $this->subjects,
        ]);
    }
}
