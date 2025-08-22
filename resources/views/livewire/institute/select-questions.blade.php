<div class="flex flex-col md:flex-row md:space-x-6 w-full">
    {{-- Filters --}}
    <div class="w-full md:w-1/4 border-b md:border-b-0 md:border-r pb-6 md:pb-0 md:pr-6 mb-6 md:mb-0">
        <h3 class="text-lg font-medium mb-4">Filters</h3>

        <div class="mb-6">
            <h4 class="font-medium mb-2 text-sm text-gray-600">Chapter</h4>
            {{-- For Livewire 3 use wire:model.live, for Livewire 2 use wire:model.lazy --}}
            <select wire:model.live="selectedChapter" class="w-full rounded-md border-gray-300 shadow-sm">
                <option value="all">All Chapters</option>
                @foreach($chapters as $chapter)
                    <option value="{{ $chapter->id }}">{{ $chapter->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <h4 class="font-medium mb-2 text-sm text-gray-600">Question Type</h4>
            <div class="space-y-2">
                <label class="inline-flex items-center">
                    <input type="checkbox" class="rounded" value="mcq" wire:model.live="types">
                    <span class="ml-2">MCQ</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="rounded" value="long" wire:model.live="types">
                    <span class="ml-2">Long Answer</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="rounded" value="short" wire:model.live="types">
                    <span class="ml-2">Short Answer</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="rounded" value="true_false" wire:model.live="types">
                    <span class="ml-2">True/False</span>
                </label>
            </div>
        </div>
    </div>

    {{-- Question List --}}
    <div class="w-full md:w-3/4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
            <h3 class="text-lg font-medium">Available Questions</h3>
            <a href="{{ route('institute.dashboard') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Finish & Go to Dashboard
            </a>
        </div>

        {{-- Loading indicator --}}
        <div wire:loading wire:target="selectedChapter, types" class="text-center py-4">
            <div class="inline-flex items-center">
                <svg class="animate-spin h-5 w-5 mr-3 text-gray-600" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span>Loading questions...</span>
            </div>
        </div>

        <div class="space-y-4 min-h-[60vh]" wire:loading.remove wire:target="selectedChapter, types">
            @forelse ($questions as $question)
                <div class="question-item p-4 border rounded-lg">
                    <div class="flex items-start gap-3">
                        {{-- Checkbox handled by Alpine in the page --}}
                        <div class="mt-1">
                            <label class="inline-flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    class="rounded"
                                    value="{{ $question->id }}"
                                    @checked($existingQuestionIds->contains($question->id))
                                    :disabled="inFlight[{{ $question->id }}] === true"
                                    @change="toggleQuestion({{ $question->id }}, {{ (int) $question->marks }}, $event)"
                                >
                                <svg x-show="inFlight[{{ $question->id }}] === true" class="w-4 h-4 animate-spin text-gray-500" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                            </label>
                        </div>

                        <div class="flex-1">
                            <div class="font-semibold">{!! $question->question_text !!}</div>

                            @if($question->question_type === 'mcq')
                                @php $optionsArray = json_decode($question->options, true); @endphp
                                @if(is_array($optionsArray))
                                    <div class="mt-2 pl-4 text-sm text-gray-700 space-y-1">
                                        @foreach($optionsArray as $index => $option)
                                            <div>
                                                <span class="font-semibold">{{ chr(65 + $index) }})</span>
                                                {!! $option !!}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endif

                            <small class="text-gray-500 mt-2 block">
                                Type: {{ strtoupper($question->question_type) }} | Marks: {{ $question->marks }} | Difficulty: {{ ucfirst($question->difficulty) }}
                            </small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-500">
                    <p>No questions found matching your current filters.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $questions->links() }}
        </div>
    </div>
</div>