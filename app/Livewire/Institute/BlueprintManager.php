<?php

namespace App\Livewire\Institute;

use Livewire\Component;
use App\Models\PaperBlueprint;

class BlueprintManager extends Component
{
    public PaperBlueprint $blueprint;

    // Add-Section form fields
    public string $section_name = '';
    public string $section_instructions = '';

    // Tracker state shown in the sticky bar
    public int $allocated = 0;
    public int $remaining = 0;

    /**
     * Events coming from children (SectionCard) or other actions.
     * Livewire v3: children dispatch with ->to(BlueprintManager::class)
     */
    protected $listeners = [
        'ruleChanged'            => 'recalc',
        'ruleDeleted'            => 'recalc',
        'ruleCreated'            => 'recalc',
        'sectionDeleted'         => 'recalc',
        'sectionAdded'           => 'recalc',
        'sectionUpdated'         => 'recalc',
        'blueprintTotalChanged'  => 'recalc',
    ];

    public function mount(PaperBlueprint $blueprint): void
    {
        abort_if($blueprint->institute_id !== auth()->id(), 403);

        // Load relations once and compute initial totals
        $this->blueprint = $blueprint->load(['sections.rules']);
        $this->calculateTotals();
    }

    /** Add a section */
    public function addSection(): void
    {
        $this->validate([
            'section_name'         => ['required', 'string', 'max:255'],
            'section_instructions' => ['nullable', 'string'],
        ]);

        $sortOrder = $this->blueprint->sections()->count() + 1;

        $this->blueprint->sections()->create([
            'name'         => $this->section_name,
            'instructions' => $this->section_instructions,
            'sort_order'   => $sortOrder,
        ]);

        $this->reset(['section_name', 'section_instructions']);

        // Refresh & recompute
        $this->blueprint->refresh()->load(['sections.rules']);
        $this->calculateTotals();

        // Optional toast UI (you already have a listener in the page)
        $this->dispatch('toast', message: 'Section added');

        // Let others know (if any are listening)
        $this->dispatch('sectionAdded');
    }

    /** Recalculate after a child tells us something changed */
    public function recalc(): void
    {
        $this->blueprint->refresh()->load(['sections.rules']);
        $this->calculateTotals();

        // Optional toast
        // $this->dispatch('toast', message: 'Marks updated');
    }

    /**
     * How a single rule contributes to the blueprint marks.
     * Your schema: marks_per_question × number_of_questions_to_select
     * (total_questions_to_display is layout-only, not marks)
     */
    protected function ruleContribution($rule): int
    {
        $mpq = (int) ($rule->marks_per_question ?? 0);
        $qty = (int) ($rule->number_of_questions_to_select ?? 0);

        // If someday you want to cap by "to_display", you could do:
        // $display = (int) ($rule->total_questions_to_display ?? 0);
        // if ($display > 0) { $qty = min($qty, $display); }

        return max(0, $mpq * $qty);
    }

    /** Sum all rules across all sections; set $allocated/$remaining */
    protected function calculateTotals(): void
    {
        // Ensure rules are loaded (cheap if already loaded)
        $this->blueprint->loadMissing(['sections.rules']);

        $allocated = 0;
        foreach ($this->blueprint->sections as $section) {
            foreach ($section->rules as $rule) {
                $allocated += $this->ruleContribution($rule);
            }
        }

        $this->allocated = (int) $allocated;
        $this->remaining = max(0, (int) $this->blueprint->total_marks - $this->allocated);
    }

    public function render()
    {
        // Keep relations fresh and totals correct on every render
        $this->blueprint->load(['sections.rules']);
        $this->calculateTotals();

        return view('livewire.institute.blueprint-manager', [
            'blueprint' => $this->blueprint,
            'sections'  => $this->blueprint->sections,
        ]);
    }
}
