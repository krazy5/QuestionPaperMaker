{{-- resources/views/livewire/admin/blueprint-manager.blade.php --}}
<div class="text-gray-900 dark:text-gray-100 space-y-6"> {{-- Single root wrapper --}}

    @php
        $total      = max(1, (int) $blueprint->total_marks);
        $allocatedI = (int) $allocated;
        $remaining  = max(0, (int) $blueprint->total_marks - $allocatedI);
        $exceeded   = $allocatedI > (int) $blueprint->total_marks;
        $pct        = min(100, round(($allocatedI / $total) * 100));
    @endphp

    {{-- Sticky tracker --}}
    <div class="sticky top-16 z-30">
        <div class="rounded-xl ring-1 ring-gray-200 dark:ring-gray-700 bg-white/90 dark:bg-gray-900/70 backdrop-blur p-4 sm:p-5 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                <div class="flex items-center gap-3">
                    <div class="shrink-0 w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-200 flex items-center justify-center">
                        {{-- icon --}}
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Total</div>
                        <div class="font-semibold">{{ (int) $blueprint->total_marks }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="shrink-0 w-9 h-9 rounded-full {{ $exceeded ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-200' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-200' }} flex items-center justify-center">
                        {{-- icon --}}
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Allocated</div>
                        <div class="font-semibold {{ $exceeded ? 'text-red-600' : '' }}">{{ $allocatedI }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="shrink-0 w-9 h-9 rounded-full {{ $remaining === 0 && !$exceeded ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-200' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200' }} flex items-center justify-center">
                        {{-- icon --}}
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m-4-4h8"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Remaining</div>
                        <div class="font-semibold {{ $exceeded ? 'text-red-600' : '' }}">{{ $remaining }}</div>
                        @if($exceeded)
                            <div class="text-xs text-red-600 mt-0.5">Exceeded by {{ $allocatedI - (int) $blueprint->total_marks }}</div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Progress bar (accessible) --}}
            <div class="mt-4">
                <div class="relative h-2 rounded-full bg-gray-200 dark:bg-gray-800 overflow-hidden" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100" aria-label="Marks allocation progress">
                    <div class="h-2 transition-all duration-300
                                {{ $exceeded ? 'bg-red-500' : 'bg-blue-600' }}"
                         style="width: {{ $pct }}%"></div>
                </div>
                <div class="mt-1 flex justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>{{ $pct }}%</span>
                    <span>{{ $allocatedI }}/{{ (int) $blueprint->total_marks }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Sections list --}}
    <div class="space-y-4">
        @forelse ($sections as $section)
            {{-- Keep the child component markup; style it inside its own view.
                 If you want a shell card here, wrap with a card container. --}}
            <livewire:admin.section-card
                :section="$section"
                :canAddMore="$allocatedI < (int) $blueprint->total_marks"
                :remaining="$remaining"
                :key="'section-'.$section->id"
            />
        @empty
            <div class="rounded-xl ring-1 ring-gray-200 dark:ring-gray-700 bg-white dark:bg-gray-900 p-6 text-gray-500 dark:text-gray-400">
                No sections added yet — create your first section below.
            </div>
        @endforelse
    </div>

    {{-- Add Section --}}
    <div class="rounded-xl ring-1 ring-gray-200 dark:ring-gray-700 bg-white dark:bg-gray-900 p-6 sm:p-7 space-y-5">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold">Add New Section</h3>
            <div class="text-sm {{ $exceeded ? 'text-red-600' : 'text-gray-600 dark:text-gray-400' }}">
                Remaining: {{ $remaining }}
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="sr-only" for="section_name">Section name</label>
                <input
                    id="section_name"
                    type="text"
                    wire:model.defer="section_name"
                    placeholder="Section A: Multiple Choice"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500"
                >
                @error('section_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="sr-only" for="section_instructions">Instructions</label>
                <textarea
                    id="section_instructions"
                    rows="2"
                    wire:model.defer="section_instructions"
                    placeholder="Instructions (optional)"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500"
                ></textarea>
                @error('section_instructions') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="text-right">
            <button
                wire:click="addSection"
                wire:loading.attr="disabled"
                @disabled($allocatedI >= (int) $blueprint->total_marks)
                class="inline-flex items-center px-5 py-2.5 rounded-lg bg-blue-600 dark:bg-blue-500 text-white hover:bg-blue-700 dark:hover:bg-blue-600 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed transition"
            >
                <svg wire:loading class="w-4 h-4 mr-2 animate-spin" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
                    <path d="M4 12a8 8 0 0 1 8-8" stroke="currentColor" stroke-width="4" class="opacity-75"/>
                </svg>
                <span wire:loading.remove>Add Section</span>
                <span wire:loading>Saving…</span>
            </button>
        </div>
    </div>

</div>
