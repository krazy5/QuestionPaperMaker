<div class="rounded-xl ring-1 ring-gray-200 dark:ring-gray-700 bg-white dark:bg-gray-900 overflow-hidden">
    <div class="p-6 text-gray-900 dark:text-gray-100">

        {{-- Header: view or edit section --}}
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
                @if($editingSection)
                    <div class="space-y-3">
                        <input
                            type="text"
                            wire:model.defer="edit_section_name"
                            placeholder="Section name"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
                        >
                        <textarea
                            rows="2"
                            wire:model.defer="edit_section_instructions"
                            placeholder="Instructions (optional)"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
                        ></textarea>

                        <div class="flex items-center gap-2">
                            <button
                                wire:click="updateSection"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center px-3 py-1.5 text-xs rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50">
                                <svg wire:loading class="w-3.5 h-3.5 mr-2 animate-spin" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/>
                                    <path d="M4 12a8 8 0 0 1 8-8" stroke="currentColor" stroke-width="3" class="opacity-75"/>
                                </svg>
                                Save
                            </button>
                            <button
                                wire:click="cancelEditSection"
                                class="px-3 py-1.5 text-xs rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                Cancel
                            </button>
                        </div>
                    </div>
                @else
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $section->name }}</h3>
                            @if($section->instructions)
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $section->instructions }}</p>
                            @endif
                        </div>

                        @php
                            $__sectionTotal = 0;
                            foreach ($rules as $__r) {
                                $__sectionTotal += (int) ($__r->marks_per_question ?? 0) * (int) ($__r->number_of_questions_to_select ?? 0);
                            }
                        @endphp
                        <div class="shrink-0">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200">
                                {{ $__sectionTotal }} marks
                            </span>
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-2">
                @if(!$editingSection)
                    <button
                        wire:click="startEditSection"
                        class="px-3 py-1.5 text-xs rounded-lg bg-indigo-600 dark:bg-indigo-500 text-white hover:bg-indigo-700 dark:hover:bg-indigo-600">
                        Edit Section
                    </button>
                @endif

                <button
                    wire:click="deleteSection"
                    onclick="return confirm('Delete this entire section and its rules?')"
                    class="px-3 py-1.5 text-xs rounded-lg bg-red-600 text-white hover:bg-red-700">
                    Delete Section
                </button>
            </div>
        </div>

        {{-- RULES: Mobile cards --}}
        <ul class="sm:hidden mt-5 space-y-3">
            @forelse ($rules as $rule)
                @php
                    $orig        = (int) $rule->marks_per_question * (int) $rule->number_of_questions_to_select;
                    $allowed     = (int) ($remaining ?? 0) + $orig; // while editing this row
                    $editMpq     = (int) ($edit_marks_per_question ?? 0);
                    $editQty     = (int) ($edit_number_of_questions_to_select ?? 0);
                    $editProposed= $editMpq * $editQty;
                    $wouldExceed = $editingRuleId === $rule->id && $editProposed > $allowed;
                @endphp

                <li class="rounded-lg ring-1 ring-gray-200 dark:ring-gray-700 bg-white dark:bg-gray-900 p-4" wire:key="rule-card-{{ $rule->id }}">
                    @if($editingRuleId === $rule->id)
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label class="sr-only">Type</label>
                                <select wire:model="edit_question_type" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="mcq">MCQ</option>
                                    <option value="short">Short Answer</option>
                                    <option value="long">Long Answer</option>
                                    <option value="true_false">True/False</option>
                                </select>
                                @error('edit_question_type') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="sr-only">Marks each</label>
                                    <input type="number" min="1" wire:model.live="edit_marks_per_question"
                                           class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 text-sm focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="Marks">
                                    @error('edit_marks_per_question') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="sr-only">Questions to select</label>
                                    <input type="number" min="1" wire:model.live="edit_number_of_questions_to_select"
                                           class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 text-sm focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="Qty">
                                    @error('edit_number_of_questions_to_select') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div>
                                <label class="sr-only">Total to display</label>
                                <input type="number" min="1" wire:model.defer="edit_total_questions_to_display"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 text-sm focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Total to display">
                                @error('edit_total_questions_to_display') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="text-xs mt-1 space-y-0.5">
                                <div>New total for this rule: <span class="font-semibold">{{ $editProposed }}</span> marks</div>
                                <div>Allowed (remaining + original): <span class="font-semibold">{{ $allowed }}</span> marks</div>
                                @if($wouldExceed)
                                    <div class="text-red-600">This change would exceed remaining marks.</div>
                                @endif
                            </div>

                            <div class="text-right space-x-2">
                                <button wire:click="updateRule"
                                        @disabled($wouldExceed)
                                        wire:loading.attr="disabled"
                                        class="px-3 py-1.5 text-xs rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50">
                                    Save
                                </button>
                                <button wire:click="cancelEditRule"
                                        class="px-3 py-1.5 text-xs rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-xs text-gray-500 dark:text-gray-400">Type</div>
                                <div class="font-medium">{{ strtoupper($rule->question_type) }}</div>
                                <div class="mt-2 grid grid-cols-3 gap-2 text-sm">
                                    <div><span class="text-gray-500 dark:text-gray-400">Marks</span> {{ $rule->marks_per_question }}</div>
                                    <div><span class="text-gray-500 dark:text-gray-400">Select</span> {{ $rule->number_of_questions_to_select }}</div>
                                    <div><span class="text-gray-500 dark:text-gray-400">Display</span> {{ $rule->total_questions_to_display ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="shrink-0 space-x-2">
                                <button wire:click="startEditRule({{ $rule->id }})"
                                        class="px-3 py-1.5 text-xs rounded-lg bg-indigo-600 dark:bg-indigo-500 text-white hover:bg-indigo-700 dark:hover:bg-indigo-600">
                                    Edit
                                </button>
                                <button wire:click="deleteRule({{ $rule->id }})"
                                        onclick="return confirm('Delete this rule?')"
                                        wire:loading.attr="disabled"
                                        class="px-3 py-1.5 text-xs rounded-lg bg-red-600 text-white hover:bg-red-700">
                                    Delete
                                </button>
                            </div>
                        </div>
                    @endif
                </li>
            @empty
                <li class="text-center text-gray-500 dark:text-gray-400">No rules yet.</li>
            @endforelse
        </ul>

        {{-- RULES: Desktop table --}}
        <div class="hidden sm:block overflow-x-auto mt-5 rounded-lg ring-1 ring-gray-200 dark:ring-gray-700">
            <table class="min-w-full bg-white dark:bg-gray-900">
                <thead class="bg-gray-50 dark:bg-gray-800/60 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600 dark:text-gray-300">Type</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600 dark:text-gray-300">Marks each</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600 dark:text-gray-300">Questions to select</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600 dark:text-gray-300">Total to display</th>
                        <th class="px-4 py-2 text-right font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-sm">
                    @forelse ($rules as $rule)
                        @php
                            $orig        = (int) $rule->marks_per_question * (int) $rule->number_of_questions_to_select;
                            $allowed     = (int) ($remaining ?? 0) + $orig; // while editing this row
                            $editMpq     = (int) ($edit_marks_per_question ?? 0);
                            $editQty     = (int) ($edit_number_of_questions_to_select ?? 0);
                            $editProposed= $editMpq * $editQty;
                            $wouldExceed = $editingRuleId === $rule->id && $editProposed > $allowed;
                        @endphp

                        <tr wire:key="rule-row-{{ $rule->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-800/40">
                            @if($editingRuleId === $rule->id)
                                {{-- Edit row --}}
                                <td class="px-4 py-2">
                                    <select wire:model="edit_question_type" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="mcq">MCQ</option>
                                        <option value="short">Short Answer</option>
                                        <option value="long">Long Answer</option>
                                        <option value="true_false">True/False</option>
                                    </select>
                                    @error('edit_question_type') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" min="1" wire:model.live="edit_marks_per_question"
                                           class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 text-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('edit_marks_per_question') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" min="1" wire:model.live="edit_number_of_questions_to_select"
                                           class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 text-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('edit_number_of_questions_to_select') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" min="1" wire:model.defer="edit_total_questions_to_display"
                                           class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 text-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('edit_total_questions_to_display') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror

                                    <div class="text-xs mt-2 space-y-0.5">
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
                                            wire:loading.attr="disabled"
                                            class="px-3 py-1.5 text-xs rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50">
                                        Save
                                    </button>
                                    <button wire:click="cancelEditRule"
                                            class="px-3 py-1.5 text-xs rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                        Cancel
                                    </button>
                                </td>
                            @else
                                {{-- Read row --}}
                                <td class="px-4 py-2">{{ strtoupper($rule->question_type) }}</td>
                                <td class="px-4 py-2">{{ $rule->marks_per_question }}</td>
                                <td class="px-4 py-2">{{ $rule->number_of_questions_to_select }}</td>
                                <td class="px-4 py-2">{{ $rule->total_questions_to_display ?? '—' }}</td>
                                <td class="px-4 py-2 text-right space-x-2">
                                    <button wire:click="startEditRule({{ $rule->id }})"
                                            class="px-3 py-1.5 text-xs rounded-lg bg-indigo-600 dark:bg-indigo-500 text-white hover:bg-indigo-700 dark:hover:bg-indigo-600">
                                        Edit
                                    </button>
                                    <button wire:click="deleteRule({{ $rule->id }})"
                                            onclick="return confirm('Delete this rule?')"
                                            wire:loading.attr="disabled"
                                            class="px-3 py-1.5 text-xs rounded-lg bg-red-600 text-white hover:bg-red-700">
                                        Delete
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No rules yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Create new rule --}}
        <div class="mt-6 pt-5 border-t border-gray-200 dark:border-gray-800 space-y-4" wire:key="section-{{ $section->id }}-addrule">
            <h4 class="font-semibold">Add New Rule</h4>

            @php
                $mpq          = (int) ($marks_per_question ?? 0);
                $qty          = (int) ($number_of_questions_to_select ?? 0);
                $proposed     = $mpq * $qty;
                $remainingVal = (int) ($remaining ?? 0);
                $exceeds      = $proposed > $remainingVal;
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Question Type</label>
                    <select wire:model.defer="question_type"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="mcq">MCQ</option>
                        <option value="short">Short Answer</option>
                        <option value="long">Long Answer</option>
                        <option value="true_false">True/False</option>
                    </select>
                    @error('question_type') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Marks per Question</label>
                    <input type="number" min="1"
                           wire:model.live="marks_per_question"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 text-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('marks_per_question') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Questions to Select</label>
                    <input type="number" min="1"
                           wire:model.live="number_of_questions_to_select"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 text-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('number_of_questions_to_select') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Total to Display (optional)</label>
                    <input type="number" min="1"
                           wire:model.defer="total_questions_to_display"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 text-sm focus:ring-blue-500 focus:border-blue-500">
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
                    class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 dark:bg-indigo-500 text-white hover:bg-indigo-700 dark:hover:bg-indigo-600 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg wire:loading class="w-4 h-4 mr-2 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/>
                        <path d="M4 12a8 8 0 0 1 8-8" stroke="currentColor" stroke-width="3" class="opacity-75"/>
                    </svg>
                    <span wire:loading.remove>Add Rule</span>
                    <span wire:loading>Saving…</span>
                </button>
            </div>
        </div>

    </div>
</div>
