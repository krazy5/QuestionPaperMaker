<?php

namespace App\Livewire\Admin;

use App\Livewire\Institute\SectionCard as InstituteSectionCard;
use App\Models\BlueprintSection;
use App\Models\SectionRule;

class SectionCard extends InstituteSectionCard
{


    public function addRule(): void
    {
        $this->validate([
            'question_type'                 => ['required', 'in:mcq,short,long,true_false'],
            'marks_per_question'            => ['required', 'integer', 'min:1'],
            'number_of_questions_to_select' => ['required', 'integer', 'min:1'],
            'total_questions_to_display'    => ['nullable', 'integer', 'min:1', 'gte:number_of_questions_to_select'],
        ]);

        $mpq = (int) $this->marks_per_question;
        $qty = (int) $this->number_of_questions_to_select;
        $proposed = $mpq * $qty;

        if ($proposed > (int) $this->remaining) {
            $this->addError('marks_per_question', 'This rule exceeds the remaining marks.');
            return;
        }

        $this->section->rules()->create([
            'question_type'                 => $this->question_type,
            'marks_per_question'            => $mpq,
            'number_of_questions_to_select' => $qty,
            'total_questions_to_display'    => $this->total_questions_to_display,
        ]);

        $this->section->refresh()->load('rules');

        // 👇 send to ADMIN parent so it recalculates
        $this->dispatch('ruleChanged')->to(\App\Livewire\Admin\BlueprintManager::class);

        // reset form
        $this->reset(['marks_per_question', 'number_of_questions_to_select', 'total_questions_to_display']);
        $this->question_type = 'mcq';

        $this->dispatch('toast', message: 'Rule added');
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

        // 👇 send to ADMIN parent
        $this->dispatch('sectionUpdated')->to(\App\Livewire\Admin\BlueprintManager::class);
        $this->dispatch('toast', message: 'Section updated');
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

        $orig    = (int) $rule->marks_per_question * (int) $rule->number_of_questions_to_select;
        $new     = (int) $this->edit_marks_per_question * (int) $this->edit_number_of_questions_to_select;
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

        // 👇 send to ADMIN parent
        $this->dispatch('ruleChanged')->to(\App\Livewire\Admin\BlueprintManager::class);
        $this->dispatch('toast', message: 'Rule updated');
    }


    public function mount(BlueprintSection $section): void
    {
        // Admins can manage any section; remove institute ownership guard.
        $this->section = $section->load(['rules', 'paperBlueprint']);
    }

    public function deleteSection(): void
    {
        $this->section->rules()->delete(); // optional if cascades exist
        $this->section->delete();

        $this->dispatch('sectionDeleted')->to(\App\Livewire\Admin\BlueprintManager::class);
        $this->dispatch('toast', message: 'Section deleted');
    }

    public function startEditRule(int $ruleId): void
    {
        $rule = $this->section->rules()->findOrFail($ruleId);

        $this->editingRuleId = $rule->id;
        $this->edit_question_type = $rule->question_type;
        $this->edit_marks_per_question = (int) $rule->marks_per_question;
        $this->edit_number_of_questions_to_select = (int) $rule->number_of_questions_to_select;
        $this->edit_total_questions_to_display = $rule->total_questions_to_display;
    }

    public function deleteRule(int $ruleId): void
    {
        $rule = SectionRule::findOrFail($ruleId);
        $rule->delete();

        $this->section->refresh()->load('rules');
        $this->dispatch('ruleChanged')->to(\App\Livewire\Admin\BlueprintManager::class);
        $this->dispatch('toast', message: 'Rule deleted');
    }

    public function render()
    {
        $this->section->load('rules');

        // Reuse the SAME child view from institute (no institute-specific tags inside)
        return view('livewire.institute.section-card', [
            'section' => $this->section,
            'rules'   => $this->section->rules,
        ]);
    }
}
