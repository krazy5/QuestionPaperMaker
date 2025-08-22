<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900">

        {{-- Header: view or edit section --}}
        <div class="flex items-start justify-between">
            <div class="flex-1">
                @if($editingSection)
                    <div class="space-y-2">
                        <input type="text"
                               wire:model.defer="edit_section_name"
                               class="block w-full rounded-md border-gray-300"
                               placeholder="Section name">
                        <textarea rows="2"
                               wire:model.defer="edit_section_instructions"
                               class="block w-full rounded-md border-gray-300"
                               placeholder="Instructions (optional)"></textarea>
                        <div class="space-x-2">
                            <button wire:click="updateSection"
                                    class="px-3 py-1.5 text-xs bg-green-600 text-white rounded-md hover:bg-green-700">
                                Save
                            </button>
                            <button wire:click="cancelEditSection"
                                    class="px-3 py-1.5 text-xs border rounded-md">
                                Cancel
                            </button>
                        </div>
                    </div>
                @else
                    <h3 class="text-lg font-medium">{{ $section->name }}</h3>
                    @if($section->instructions)
                        <p class="mt-1 text-sm text-gray-600">{{ $section->instructions }}</p>
                    @endif

                    @php
                        $__sectionTotal = 0;
                        foreach ($rules as $__r) {
                            $__sectionTotal += (int) ($__r->marks_per_question ?? 0) * (int) ($__r->number_of_questions_to_select ?? 0);
                        }
                    @endphp
                    <p class="mt-2 text-xs text-gray-600">
                        Section subtotal: <span class="font-semibold">{{ $__sectionTotal }}</span> marks
                    </p>
                @endif
            </div>

            <div class="space-x-2">
                @if(!$editingSection)
                    <button wire:click="startEditSection"
                            class="px-3 py-1.5 text-xs bg-gray-800 text-white rounded-md hover:bg-gray-900">
                        Edit Section
                    </button>
                @endif

                <button wire:click="deleteSection"
                        onclick="return confirm('Delete this entire section and its rules?')"
                        class="px-3 py-1.5 text-xs bg-red-600 text-white rounded-md hover:bg-red-700">
                    Delete Section
                </button>
            </div>
        </div>

        {{-- Rules table --}}
        <table class="min-w-full divide-y divide-gray-200 mt-4">
            <thead class="bg-gray-50 text-xs uppercase">
                <tr>
                    <th class="px-4 py-2 text-left">TYPE</th>
                    <th class="px-4 py-2 text-left">MARKS EACH</th>
                    <th class="px-4 py-2 text-left">QUESTIONS TO SELECT</th>
                    <th class="px-4 py-2 text-left">TOTAL TO DISPLAY</th>
                    <th class="px-4 py-2 text-right">ACTIONS</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                @forelse ($rules as $rule)
                    @php
                        $orig = (int) $rule->marks_per_question * (int) $rule->number_of_questions_to_select;
                        $allowed = (int) ($remaining ?? 0) + $orig; // while editing this row
                        $editMpq = (int) ($edit_marks_per_question ?? 0);
                        $editQty = (int) ($edit_number_of_questions_to_select ?? 0);
                        $editProposed = $editMpq * $editQty;
                        $wouldExceed = $editingRuleId === $rule->id && $editProposed > $allowed;
                    @endphp

                    <tr wire:key="rule-row-{{ $rule->id }}">
                        @if($editingRuleId === $rule->id)
                            {{-- Edit row --}}
                            <td class="px-4 py-2">
                                <select wire:model="edit_question_type" class="w-full rounded-md border-gray-300 text-sm">
                                    <option value="mcq">MCQ</option>
                                    <option value="short">Short Answer</option>
                                    <option value="long">Long Answer</option>
                                    <option value="true_false">True/False</option>
                                </select>
                                @error('edit_question_type') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" min="1" wire:model.live="edit_marks_per_question"
                                       class="w-full rounded-md border-gray-300 text-sm">
                                @error('edit_marks_per_question') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" min="1" wire:model.live="edit_number_of_questions_to_select"
                                       class="w-full rounded-md border-gray-300 text-sm">
                                @error('edit_number_of_questions_to_select') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" min="1" wire:model.defer="edit_total_questions_to_display"
                                       class="w-full rounded-md border-gray-300 text-sm">
                                @error('edit_total_questions_to_display') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror

                                <div class="text-xs mt-2">
                                    <div>New total for this rule: <span class="font-semibold">{{ $editProposed }}</span> marks</div>
                                    <div>Allowed (remaining + original): <span class="font-semibold">{{ $allowed }}</span> marks</div>
                                    @if($wouldExceed)
                                        <div class="text-red-600">This change would exceed remaining marks.</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <button wire:click="updateRule"
                                        @disabled($wouldExceed)
                                        class="px-3 py-1.5 text-xs bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50">
                                    Save
                                </button>
                                <button wire:click="cancelEditRule"
                                        class="px-3 py-1.5 text-xs border rounded-md">
                                    Cancel
                                </button>
                            </td>
                        @else
                            {{-- Read row --}}
                            <td class="px-4 py-2">{{ $rule->question_type }}</td>
                            <td class="px-4 py-2">{{ $rule->marks_per_question }}</td>
                            <td class="px-4 py-2">{{ $rule->number_of_questions_to_select }}</td>
                            <td class="px-4 py-2">{{ $rule->total_questions_to_display ?? '—' }}</td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <button wire:click="startEditRule({{ $rule->id }})"
                                        class="px-3 py-1.5 text-xs bg-gray-800 text-white rounded-md hover:bg-gray-900">
                                    Edit
                                </button>
                                <button wire:click="deleteRule({{ $rule->id }})"
                                        onclick="return confirm('Delete this rule?')"
                                        class="px-3 py-1.5 text-xs bg-red-600 text-white rounded-md hover:bg-red-700"
                                        wire:loading.attr="disabled">
                                    Delete
                                </button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">No rules yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Create new rule --}}
        <div class="mt-4 p-4 border-t space-y-4" wire:key="section-{{ $section->id }}-addrule">
            <h4 class="font-semibold">Add New Rule</h4>

            @php
                $mpq         = (int) ($marks_per_question ?? 0);
                $qty         = (int) ($number_of_questions_to_select ?? 0);
                $proposed    = $mpq * $qty;
                $remainingVal= (int) ($remaining ?? 0);
                $exceeds     = $proposed > $remainingVal;
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="text-sm">Question Type</label>
                    <select wire:model.defer="question_type" class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                        <option value="mcq">MCQ</option>
                        <option value="short">Short Answer</option>
                        <option value="long">Long Answer</option>
                        <option value="true_false">True/False</option>
                    </select>
                    @error('question_type') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm">Marks per Question</label>
                    <input type="number" min="1"
                           wire:model.live="marks_per_question"
                           class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                    @error('marks_per_question') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm">Questions to Select</label>
                    <input type="number" min="1"
                           wire:model.live="number_of_questions_to_select"
                           class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                    @error('number_of_questions_to_select') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm">Total to Display (optional)</label>
                    <input type="number" min="1"
                           wire:model.defer="total_questions_to_display"
                           class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                    @error('total_questions_to_display') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="text-sm">
                <div>New rule adds: <span class="font-semibold">{{ $proposed }}</span> marks</div>
                <div>Remaining (blueprint): <span class="font-semibold">{{ $remainingVal }}</span> marks</div>
                @if($exceeds)
                    <div class="text-red-600 mt-1">This rule would exceed the remaining marks.</div>
                @endif
            </div>

            <div class="text-right">
                <button
                    wire:click="addRule"
                    wire:loading.attr="disabled"
                    @disabled($exceeds || !$canAddMore)
                    class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm
                           disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove>Add Rule</span>
                    <span wire:loading>Saving…</span>
                </button>
            </div>
        </div>

    </div>
</div>
