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
                            
                            <div id="question-list" class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                                @forelse ($questions as $question)
                                    <div class="question-item p-4 border rounded-lg" data-chapter-id="{{ $question->chapter_id }}">
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
                                                <small class="text-gray-500">
                                                    Marks: {{ $question->marks }} | Difficulty: {{ ucfirst($question->difficulty) }}
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chapterLinks = document.querySelectorAll('.chapter-link');
            const questionItems = document.querySelectorAll('.question-item');

            chapterLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Update active styles
                    chapterLinks.forEach(l => l.classList.remove('bg-blue-500', 'text-white'));
                    this.classList.add('bg-blue-500', 'text-white');

                    const selectedChapterId = this.getAttribute('data-chapter-id');

                    questionItems.forEach(item => {
                        if (selectedChapterId === 'all' || item.getAttribute('data-chapter-id') === selectedChapterId) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    // ADD THIS LINE: Tell MathJax to re-render the math on the page
                    if (window.MathJax) {
                        window.MathJax.typesetPromise();
                    }
                });
            });
        });
    </script>
</x-app-layout>
