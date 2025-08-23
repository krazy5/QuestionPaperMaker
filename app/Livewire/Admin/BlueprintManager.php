<?php

namespace App\Livewire\Admin;

use App\Livewire\Institute\BlueprintManager as InstituteBlueprintManager;
use App\Models\PaperBlueprint;

class BlueprintManager extends InstituteBlueprintManager
{
    public function mount(PaperBlueprint $blueprint): void
    {
        // Admins can manage any blueprint; no institute_id guard here.
        $this->blueprint = $blueprint->load(['sections.rules']);
        $this->calculateTotals();
    }

    public function render()
    {
        // Keep totals fresh, but use the ADMIN parent view (calls admin.section-card)
        $this->blueprint->load(['sections.rules']);
        $this->calculateTotals();

        return view('livewire.admin.blueprint-manager', [
            'blueprint' => $this->blueprint,
            'sections'  => $this->blueprint->sections,
        ]);
    }
}
