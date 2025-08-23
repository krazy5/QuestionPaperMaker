{{-- Frameless; assumes parent page provides the card wrapper --}}
<div>
    {{-- Sticky tracker --}}
    <div class="sticky top-16 z-20 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 p-3 rounded-lg ring-1 ring-gray-200 dark:ring-gray-700 bg-white dark:bg-gray-900/60 shadow-sm">
            <div>
                <span class="font-semibold">Total:</span>
                {{ (int) $blueprint->total_marks }}
            </div>
            <div>
                <span class="font-semibold">Allocated:</span>
                <span class="{{ $allocated > $blueprint->total_marks ? 'text-red-600' : '' }}">
                    {{ $allocated }}
                </span>
            </div>
            <div>
                <span class="font-semibold">Remaining:</span>
                <span class="{{ $allocated > $blueprint->total_marks ? 'text-red-600' : '' }}">
                    {{ max(0, (int) $blueprint->total_marks - (int) $allocated) }}
                </span>
                @if($allocated > $blueprint->total_marks)
                    <span class="ml-2 text-xs text-red-600 font-medium">
                        Exceeded by {{ $allocated - (int) $blueprint->total_marks }}
                    </span>
                @endif
            </div>
        </div>

        @php
            $total = max(1, (int) $blueprint->total_marks);
            $pct   = min(100, round(($allocated / $total) * 100));
        @endphp
        <div class="mt-2 h-2 bg-gray-200 dark:bg-gray-800 rounded-full overflow-hidden">
            <div class="h-2 {{ $allocated <= $blueprint->total_marks ? 'bg-blue-600 dark:bg-blue-500' : 'bg-red-600' }}"
                 style="width: {{ $pct }}%"></div>
        </div>
    </div>

    <div class="space-y-6">
        {{-- Sections --}}
        @forelse ($sections as $section)
            <livewire:institute.section-card
                :section="$section"
                :canAddMore="$allocated < (int) $blueprint->total_marks"
                :remaining="max(0, (int) $blueprint->total_marks - (int) $allocated)"
                :key="'section-'.$section->id"
            />
        @empty
            <div class="rounded-lg ring-1 ring-gray-200 dark:ring-gray-700 bg-white dark:bg-gray-900/60 shadow-sm">
                <div class="p-6 text-gray-600 dark:text-gray-300">
                    No sections added yet — create your first section below.
                </div>
            </div>
        @endforelse

        {{-- Add Section --}}
        <div class="rounded-lg ring-1 ring-gray-200 dark:ring-gray-700 bg-white dark:bg-gray-900/60 shadow-sm">
            <div class="p-6 text-gray-900 dark:text-gray-100 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium">Add New Section</h3>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Remaining: {{ max(0, (int) $blueprint->total_marks - (int) $allocated) }}
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    <input type="text"
                           wire:model.defer="section_name"
                           class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                           placeholder="Section A: Multiple Choice">
                    <textarea
                           wire:model.defer="section_instructions"
                           rows="2"
                           class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                           placeholder="Instructions (optional)"></textarea>
                </div>

                <div class="text-right">
                    <button
                        wire:click="addSection"
                        wire:loading.attr="disabled"
                        @disabled($allocated >= (int) $blueprint->total_marks)
                        class="px-4 py-2 rounded-lg bg-blue-600 dark:bg-blue-500 text-white
                               disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
                        <span wire:loading.remove>Add Section</span>
                        <span wire:loading>Saving…</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
