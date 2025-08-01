<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Select Questions for: <span class="italic">{{ $paper->title }}</span>
        </h2>
    </x-slot>

    {{-- MathJax Configuration --}}
    <script>
    window.MathJax = {
      tex: {
        inlineMath: [['$', '$'], ['\\(', '\\)']],
        displayMath: [['$$', '$$'], ['\\[', '\\]']]
      }
    };
    </script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('institute.papers.questions.save', $paper) }}" method="POST">
                @csrf
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 flex space-x-6">

                         {{-- Left Column: Chapter List --}}
                        <div class="w-1/4 border-r pr-6">
                            <h3 class="text-lg font-medium mb-4">Chapters</h3>
                            <ul class="space-y-2" id="chapter-list">
                                <li>
                                    <a href="#" class="chapter-link block p-2 rounded bg-blue-500 text-white" data-chapter-id="all">
                                        All Chapters
                                    </a>
                                </li>
                                @foreach($chapters as $chapter)
                                    <li>
                                        <a href="#" class="chapter-link block p-2 rounded hover:bg-gray-100" data-chapter-id="{{ $chapter->id }}">
                                            {{ $chapter->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- Right Column: Question List --}}
                        <div class="w-3/4">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium">Available Questions</h3>
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Save Selections & Finish</button>
                            </div>
                            
                            {{-- Filter by Question Type --}}
                            <div class="mb-4 p-4 border rounded-lg bg-gray-50">
                                <h4 class="font-medium mb-2 text-sm text-gray-600">Filter by Type</h4>
                                <div class="flex flex-wrap gap-x-6 gap-y-2">
                                    <div><label class="inline-flex items-center"><input type="checkbox" class="type-filter-checkbox rounded" value="mcq" checked> <span class="ml-2">MCQ</span></label></div>
                                    <div><label class="inline-flex items-center"><input type="checkbox" class="type-filter-checkbox rounded" value="long" checked> <span class="ml-2">Long Answer</span></label></div>
                                    <div><label class="inline-flex items-center"><input type="checkbox" class="type-filter-checkbox rounded" value="short" checked> <span class="ml-2">Short Answer</span></label></div>
                                    <div><label class="inline-flex items-center"><input type="checkbox" class="type-filter-checkbox rounded" value="true_false" checked> <span class="ml-2">True/False</span></label></div>
                                </div>
                            </div>

                            {{-- Question List --}}
                            <div id="question-list" class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                                @forelse ($questions as $question)
                                    <div class="question-item p-4 border rounded-lg" data-chapter-id="{{ $question->chapter_id }}" data-question-type="{{ $question->question_type }}">
                                        <label class="flex items-start space-x-3">
                                            <input 
                                                type="checkbox" 
                                                name="questions[]" 
                                                value="{{ $question->id }}"
                                                class="mt-1"
                                                @checked(in_array($question->id, $existingQuestionIds->toArray()))
                                            >
                                            <div class="flex-1">
                                                <div class="font-semibold">{!! $question->question_text !!}</div>
                                                
                                                @if($question->question_type === 'mcq')
                                                    @php
                                                        $optionsArray = json_decode($question->options, true);
                                                    @endphp
                                                    @if(is_array($optionsArray))
                                                        <div class="mt-2 pl-4 text-sm text-gray-700 space-y-1">
                                                            @foreach($optionsArray as $index => $option)
                                                                <div>
                                                                    <span class="font-semibold">{{ chr(65 + $index) }})</span> {!! $option !!}
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                @endif

                                                <small class="text-gray-500 mt-2 block">
                                                    Type: {{ strtoupper($question->question_type) }} | Marks: {{ $question->marks }} | Difficulty: {{ ucfirst($question->difficulty) }}
                                                </small>
                                            </div>
                                        </label>
                                    </div>
                                @empty
                                    <p>No questions found for this subject.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- JavaScript Filtering --}}
    {{-- JavaScript Filtering --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chapterLinks = document.querySelectorAll('.chapter-link');
            const typeCheckboxes = document.querySelectorAll('.type-filter-checkbox');
            const questionItems = document.querySelectorAll('.question-item');
            
            function filterQuestions() {
                const selectedTypes = Array.from(typeCheckboxes)
                                        .filter(cb => cb.checked)
                                        .map(cb => cb.value);

                // --- CHANGE #1: Look for bg-blue-500, not bg-blue-600 ---
                const activeLink = document.querySelector('.chapter-link.bg-blue-500');
                const currentChapterId = activeLink ? activeLink.getAttribute('data-chapter-id') : 'all';

                questionItems.forEach(item => {
                    const chapterMatch = currentChapterId === 'all' || item.dataset.chapterId === currentChapterId;
                    const typeMatch = selectedTypes.includes(item.dataset.questionType);
                    
                    item.style.display = (chapterMatch && typeMatch) ? '' : 'none';
                });

                if (window.MathJax) {
                    MathJax.typesetPromise();
                }
            }

            // --- CHANGE #2: The click handler now uses the correct classes ---
            chapterLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Reset all links to their default, inactive state
                    chapterLinks.forEach(l => {
                        l.classList.remove('bg-blue-500', 'text-white');
                        l.classList.add('hover:bg-gray-100');
                    });

                    // Set the clicked link to the active state
                    this.classList.remove('hover:bg-gray-100');
                    this.classList.add('bg-blue-500', 'text-white');

                    // Now, run the filter function
                    filterQuestions();
                });
            });

            typeCheckboxes.forEach(cb => cb.addEventListener('change', filterQuestions));

            // Initial run
            filterQuestions();
        });
    </script>
</x-app-layout>
