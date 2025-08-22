<?php

namespace App\Livewire\Institute;

use Livewire\Component;
use App\Models\BlueprintSection;
use App\Models\SectionRule;

class SectionCard extends Component
{
    /** Passed from parent */
    public BlueprintSection $section;
    public int  $remaining   = 0;      // remaining marks from parent
    public bool $canAddMore  = true;   // parent computed flag

    /** Create form fields */
    public string $question_type = 'mcq';
    public ?int $marks_per_question = null;
    public ?int $number_of_questions_to_select = null;
    public ?int $total_questions_to_display = null;

    /** Section edit state */
    public bool $editingSection = false;
    public string $edit_section_name = '';
    public ?string $edit_section_instructions = null;

    /** Rule edit state (one at a time) */
    public ?int $editingRuleId = null;
    public string $edit_question_type = 'mcq';
    public ?int $edit_marks_per_question = null;
    public ?int $edit_number_of_questions_to_select = null;
    public ?int $edit_total_questions_to_display = null;

    public function mount(BlueprintSection $section): void
    {
        abort_if($section->paperBlueprint->institute_id !== auth()->id(), 403);
        $this->section = $section->load(['rules', 'paperBlueprint']);
    }

    /* --------------------------
     * SECTION: create / edit / delete
     * ------------------------ */

    public function addRule(): void
    {
        $this->validate([
            'question_type'                    => ['required', 'in:mcq,short,long,true_false'],
            'marks_per_question'               => ['required', 'integer', 'min:1'],
            'number_of_questions_to_select'    => ['required', 'integer', 'min:1'],
            'total_questions_to_display'       => ['nullable', 'integer', 'min:1', 'gte:number_of_questions_to_select'],
        ]);

        $mpq = (int) $this->marks_per_question;
        $qty = (int) $this->number_of_questions_to_select;
        $proposed = $mpq * $qty;

        if ($proposed > (int) $this->remaining) {
            $this->addError('marks_per_question', 'This rule exceeds the remaining marks.');
            return;
        }

        $this->section->rules()->create([
            'question_type'                     => $this->question_type,
            'marks_per_question'                => $mpq,
            'number_of_questions_to_select'     => $qty,
            'total_questions_to_display'        => $this->total_questions_to_display,
        ]);

        $this->section->refresh()->load('rules');
        $this->dispatch('ruleChanged')->to(\App\Livewire\Institute\BlueprintManager::class);

        // reset create form
        $this->reset(['marks_per_question', 'number_of_questions_to_select', 'total_questions_to_display']);
        $this->question_type = 'mcq';

        $this->dispatch('toast', message: 'Rule added');
    }

    public function startEditSection(): void
    {
        $this->editingSection = true;
        $this->edit_section_name = $this->section->name;
        $this->edit_section_instructions = $this->section->instructions;
    }

    public function cancelEditSection(): void
    {
        $this->editingSection = false;
        $this->reset(['edit_section_name', 'edit_section_instructions']);
    }

    public function updateSection(): void
    {
        $this->validate([
            'edit_section_name'         => ['required','string','max:255'],
            'edit_section_instructions' => ['nullable','string'],
        ]);

        $this->section->update([
            'name'         => $this->edit_section_name,
            'instructions' => $this->edit_section_instructions,
        ]);

        $this->editingSection = false;
        $this->dispatch('sectionUpdated')->to(\App\Livewire\Institute\BlueprintManager::class);
        $this->dispatch('toast', message: 'Section updated');
    }

    public function deleteSection(): void
    {
        $blueprint = $this->section->paperBlueprint;
        abort_if($blueprint->institute_id !== auth()->id(), 403);

        $this->section->rules()->delete(); // optional if DB cascade is set
        $this->section->delete();

        $this->dispatch('sectionDeleted')->to(\App\Livewire\Institute\BlueprintManager::class);
        $this->dispatch('toast', message: 'Section deleted');
    }

    /* --------------------------
     * RULE: edit / delete
     * ------------------------ */

    public function startEditRule(int $ruleId): void
    {
        $rule = $this->section->rules()->findOrFail($ruleId);
        abort_if($rule->blueprintSection->paperBlueprint->institute_id !== auth()->id(), 403);

        $this->editingRuleId = $rule->id;
        $this->edit_question_type = $rule->question_type;
        $this->edit_marks_per_question = (int) $rule->marks_per_question;
        $this->edit_number_of_questions_to_select = (int) $rule->number_of_questions_to_select;
        $this->edit_total_questions_to_display = $rule->total_questions_to_display;
    }

    public function cancelEditRule(): void
    {
        $this->editingRuleId = null;
        $this->reset([
            'edit_question_type',
            'edit_marks_per_question',
            'edit_number_of_questions_to_select',
            'edit_total_questions_to_display'
        ]);
    }

    public function updateRule(): void
    {
        $this->validate([
            'edit_question_type'                 => ['required', 'in:mcq,short,long,true_false'],
            'edit_marks_per_question'            => ['required', 'integer', 'min:1'],
            'edit_number_of_questions_to_select' => ['required', 'integer', 'min:1'],
            'edit_total_questions_to_display'    => ['nullable', 'integer', 'min:1', 'gte:edit_number_of_questions_to_select'],
        ]);

        $rule = $this->section->rules()->findOrFail((int) $this->editingRuleId);

        // original & proposed contributions
        $orig = (int) $rule->marks_per_question * (int) $rule->number_of_questions_to_select;
        $new  = (int) $this->edit_marks_per_question * (int) $this->edit_number_of_questions_to_select;

        // allowed = remaining (from parent) + original (since we're editing this rule)
        $allowed = (int) $this->remaining + $orig;

        if ($new > $allowed) {
            $this->addError('edit_marks_per_question', 'This change would exceed remaining marks.');
            return;
        }

        $rule->update([
            'question_type'                 => $this->edit_question_type,
            'marks_per_question'            => (int) $this->edit_marks_per_question,
            'number_of_questions_to_select' => (int) $this->edit_number_of_questions_to_select,
            'total_questions_to_display'    => $this->edit_total_questions_to_display,
        ]);

        $this->section->refresh()->load('rules');
        $this->editingRuleId = null;

        $this->dispatch('ruleChanged')->to(\App\Livewire\Institute\BlueprintManager::class);
        $this->dispatch('toast', message: 'Rule updated');
    }

    public function deleteRule(int $ruleId): void
    {
        $rule = SectionRule::findOrFail($ruleId);
        abort_if($rule->blueprintSection->paperBlueprint->institute_id !== auth()->id(), 403);

        $rule->delete();

        $this->section->refresh()->load('rules');
        $this->dispatch('ruleChanged')->to(\App\Livewire\Institute\BlueprintManager::class);
        $this->dispatch('toast', message: 'Rule deleted');
    }

    public function render()
    {
        $this->section->load('rules');

        return view('livewire.institute.section-card', [
            'section' => $this->section,
            'rules'   => $this->section->rules,
        ]);
    }
}
