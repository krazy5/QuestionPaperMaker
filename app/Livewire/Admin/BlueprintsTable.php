<?php

namespace App\Livewire\Admin;

use App\Livewire\Institute\BlueprintsTable as InstituteBlueprintsTable;
use App\Models\PaperBlueprint;

class BlueprintsTable extends InstituteBlueprintsTable
{
    /** Delete without institute_id guard (admin can delete any) */
    public function deleteBlueprint(int $id): void
    {
        $bp = PaperBlueprint::findOrFail($id);
        $bp->delete();

        if ($this->page > 1 && $this->isCurrentPageEmptyAfterDelete()) {
            $this->page = max(1, $this->page - 1);
        }

        $this->dispatch('toast', message: 'Blueprint deleted');
    }

    /** Recompute emptiness w/ same filters, but no institute_id filter */
    protected function isCurrentPageEmptyAfterDelete(): bool
    {
        $query = PaperBlueprint::query();

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

    /** Render admin view + show all blueprints (no institute filter) */
    public function render()
    {
        $query = PaperBlueprint::query()
            ->with(['board','academicClass','subject']);

        $search = trim($this->q);
        if ($search !== '') {
            $query->where('name', 'like', '%'.$search.'%');
        }

        if (!is_null($this->board_id))   $query->where('board_id',   $this->board_id);
        if (!is_null($this->class_id))   $query->where('class_id',   $this->class_id);
        if (!is_null($this->subject_id)) $query->where('subject_id', $this->subject_id);

        $blueprints = $query->latest()->paginate(10);

        return view('livewire.admin.blueprints-table', [
            'blueprints' => $blueprints,
            'subjects'   => $this->subjects,
        ]);
    }
}
