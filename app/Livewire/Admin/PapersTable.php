<?php

namespace App\Livewire\Admin;

use App\Livewire\Institute\PapersTable as InstitutePapersTable;
use App\Models\Paper;

class PapersTable extends InstitutePapersTable
{
    /** Admin delete (no institute_id guard) */
    public function deletePaper(int $id): void
    {
        $paper = Paper::findOrFail($id);

        // detach pivots if you store selected questions
        if (method_exists($paper, 'questions')) {
            $paper->questions()->detach();
        }

        $paper->delete();

        if ($this->page > 1 && $this->isCurrentPageEmptyAfterDelete()) {
            $this->page = max(1, $this->page - 1);
        }

        $this->dispatch('toast', message: 'Paper deleted');
    }

    /** Recompute emptiness without institute filter */
    protected function isCurrentPageEmptyAfterDelete(): bool
    {
        $query = Paper::query();

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

    /** Render admin view + show all papers (no institute filter) */
    public function render()
    {
        $query = Paper::query()
            ->with(['subject.academicClass']);

        $search = trim($this->q);
        if ($search !== '') {
            $query->where('title', 'like', '%'.$search.'%');
        }

        if (!is_null($this->board_id))   $query->where('board_id',   $this->board_id);
        if (!is_null($this->class_id))   $query->where('class_id',   $this->class_id);
        if (!is_null($this->subject_id)) $query->where('subject_id', $this->subject_id);

        $papers = $query->latest()->paginate(10);

        return view('livewire.admin.papers-table', [
            'papers'   => $papers,
            'subjects' => $this->subjects,
        ]);
    }
}
