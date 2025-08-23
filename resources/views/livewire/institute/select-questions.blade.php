<div class="flex flex-col lg:flex-row lg:space-x-6 w-full">

    {{-- Filters (sticky on desktop, collapsible feel on mobile via simple layout) --}}
    <aside class="w-full lg:w-1/4 lg:sticky lg:top-16 lg:self-start">
        <div class="rounded-xl ring-1 ring-gray-200 dark:ring-gray-700 bg-white dark:bg-gray-900/60 shadow-sm p-4 lg:p-5 mb-6">
            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">Filters</h3>

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">Chapter</label>
                    <select wire:model.live="selectedChapter"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500">
                        <option value="all">All Chapters</option>
                        @foreach($chapters as $chapter)
                            <option value="{{ $chapter->id }}">{{ $chapter->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">Question Type</label>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" class="rounded border-gray-300 dark:border-gray-700 text-blue-600 focus:ring-blue-500"
                                   value="mcq" wire:model.live="types">
                            <span>MCQ</span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" class="rounded border-gray-300 dark:border-gray-700 text-blue-600 focus:ring-blue-500"
                                   value="long" wire:model.live="types">
                            <span>Long Answer</span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" class="rounded border-gray-300 dark:border-gray-700 text-blue-600 focus:ring-blue-500"
                                   value="short" wire:model.live="types">
                            <span>Short Answer</span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" class="rounded border-gray-300 dark:border-gray-700 text-blue-600 focus:ring-blue-500"
                                   value="true_false" wire:model.live="types">
                            <span>True/False</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    {{-- Questions --}}
    <section class="w-full lg:w-3/4">
        <div class="rounded-xl ring-1 ring-gray-200 dark:ring-gray-700 bg-white dark:bg-gray-900/60 shadow-sm">
            <div class="p-4 sm:p-5 border-b border-gray-200 dark:border-gray-800">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Available Questions</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            @if($questions->total() > 0)
                                Showing <span class="font-medium">{{ $questions->firstItem() }}</span>–<span class="font-medium">{{ $questions->lastItem() }}</span>
                                of <span class="font-medium">{{ $questions->total() }}</span>
                                · Selected: <span class="font-medium">{{ $existingQuestionIds->count() }}</span>
                            @else
                                No results
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('institute.dashboard') }}"
                       class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 shadow-sm">
                        Finish & Go to Dashboard
                    </a>
                </div>
            </div>

            {{-- Loading indicator --}}
            <div wire:loading wire:target="selectedChapter, types"
                 class="p-4 text-center" aria-live="polite">
                <div class="inline-flex items-center text-gray-700 dark:text-gray-300">
                    <svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span>Loading questions…</span>
                </div>
            </div>

            {{-- List --}}
            <div class="p-4 sm:p-5 space-y-4 min-h-[60vh]" wire:loading.remove wire:target="selectedChapter, types">
                @forelse ($questions as $question)
                    <div class="question-item p-4 sm:p-5 rounded-xl ring-1 ring-gray-200 dark:ring-gray-700 bg-white dark:bg-gray-900/60 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                        <div class="flex items-start gap-3">
                            {{-- Checkbox (Alpine handles attach/detach + inFlight state) --}}
                            <div class="mt-1">
                                <label class="inline-flex items-center gap-2">
                                    <input
                                        type="checkbox"
                                        class="rounded border-gray-300 dark:border-gray-700 text-blue-600 focus:ring-blue-500"
                                        value="{{ $question->id }}"
                                        @checked($existingQuestionIds->contains($question->id))
                                        :disabled="inFlight[{{ $question->id }}] === true"
                                        @change="toggleQuestion({{ $question->id }}, {{ (int) $question->marks }}, $event)"
                                    >
                                    <svg x-show="inFlight[{{ $question->id }}] === true"
                                         class="w-4 h-4 animate-spin text-gray-500" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                </label>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-900 dark:text-gray-100 prose prose-sm max-w-none dark:prose-invert">
                                    {!! $question->question_text !!}
                                </div>

                                @if($question->question_type === 'mcq')
                                    @php $optionsArray = json_decode($question->options, true); @endphp
                                    @if(is_array($optionsArray))
                                        <div class="mt-3 pl-4 text-sm text-gray-700 dark:text-gray-300 space-y-1">
                                            @foreach($optionsArray as $index => $option)
                                                <div class="flex">
                                                    <span class="font-semibold mr-2">{{ chr(65 + $index) }})</span>
                                                    <span class="prose prose-sm max-w-none dark:prose-invert">{!! $option !!}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif

                                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                                    <span class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                        {{ strtoupper($question->question_type) }}
                                    </span>
                                    <span>Marks: <span class="font-medium">{{ $question->marks }}</span></span>
                                    <span>Difficulty: <span class="font-medium">{{ ucfirst($question->difficulty) }}</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                        No questions found matching your current filters.
                    </div>
                @endforelse
            </div>

            <div class="p-3 border-t border-gray-200 dark:border-gray-800">
                {{ $questions->links() }}
            </div>
        </div>
    </section>
</div>
