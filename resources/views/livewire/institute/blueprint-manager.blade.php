{{-- resources/views/livewire/institute/blueprint-manager.blade.php --}}
<div>  {{-- ✅ Single root wrapper --}}

    {{-- ✅ Sticky tracker at the top --}}
    <div class="sticky top-16 z-20 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 p-3 rounded-md border bg-white shadow-sm">
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
        <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
            <div class="h-2 {{ $allocated <= $blueprint->total_marks ? 'bg-blue-600' : 'bg-red-600' }}"
                 style="width: {{ $pct }}%"></div>
        </div>
    </div>

    <div class="space-y-6">
        {{-- Sections --}}
        @forelse ($sections as $section)
            {{-- Pass flags down so child can disable "Add Rule" when no marks left --}}
            <livewire:institute.section-card
                :section="$section"
                :canAddMore="$allocated < (int) $blueprint->total_marks"
                :remaining="max(0, (int) $blueprint->total_marks - (int) $allocated)"
                :key="'section-'.$section->id"
            />
        @empty
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-500">
                    No sections added yet — create your first section below.
                </div>
            </div>
        @endforelse

        {{-- Add Section --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium">Add New Section</h3>
                    <div class="text-sm text-gray-600">
                        Remaining: {{ max(0, (int) $blueprint->total_marks - (int) $allocated) }}
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    <input type="text"
                           wire:model.defer="section_name"
                           class="mt-1 block w-full rounded-md border-gray-300"
                           placeholder="Section A: Multiple Choice">
                    <textarea
                           wire:model.defer="section_instructions"
                           rows="2"
                           class="mt-1 block w-full rounded-md border-gray-300"
                           placeholder="Instructions (optional)"></textarea>
                </div>

                <div class="text-right">
                    <button
                        wire:click="addSection"
                        wire:loading.attr="disabled"
                        @disabled($allocated >= (int) $blueprint->total_marks)
                        class="px-4 py-2 bg-blue-600 text-white rounded-md
                               disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove>Add Section</span>
                        <span wire:loading>Saving…</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div> {{-- ✅ end single root wrapper --}}
