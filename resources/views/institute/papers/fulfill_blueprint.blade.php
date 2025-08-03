<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Building Paper: <span class="italic">{{ $paper->title }}</span>
        </h2>
    </x-slot>

    {{-- MathJax Configuration and Library --}}
    <script>
    window.MathJax = {
      tex: {
        inlineMath: [['$', '$'], ['\\(', '\\)']]
      }
    };
    </script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Blueprint: {{ $blueprint->name }}</h3>
                    <p class="text-sm text-gray-600">Fill each section below to complete your paper.</p>
                </div>
                <div>
                    <form action="{{ route('institute.papers.auto_fill', $paper) }}" method="POST" class="inline" onsubmit="return confirm('This will replace all currently selected questions. Are you sure?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 mr-2">Auto-fill Paper</button>
                    </form>
                    <a href="{{ route('institute.dashboard') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Finish & Go to Dashboard</a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-8">
                    @foreach($blueprint->sections as $section)
                        <section class="p-6 border rounded-lg">
                            <header class="border-b pb-4 mb-4">
                                <h3 class="text-xl font-semibold">{{ $section->name }}</h3>
                                @if($section->instructions)
                                    <p class="text-sm text-gray-600 italic mt-1">"{{ $section->instructions }}"</p>
                                @endif
                            </header>
                            <div class="space-y-4">
                                @foreach($section->rules as $rule)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-md">
                                        <div>
                                            <p class="font-medium">
                                                Select <strong>{{ $rule->number_of_questions_to_select }}</strong> 
                                                <strong>{{ strtoupper($rule->question_type) }}</strong> questions, each worth <strong>{{ $rule->marks_per_question }}</strong> marks.
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-bold text-gray-800" id="rule-count-{{$rule->id}}">0 / {{ $rule->number_of_questions_to_select }}</p>
                                            <button type="button" class="mt-1 text-sm text-indigo-600 hover:underline open-modal-btn" data-rule-id="{{ $rule->id }}">
                                                Add / View Questions
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- The Modal for Selecting Questions --}}
    <div id="question-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-11/12 md:w-3/4 lg:w-2/3 max-h-[90vh] flex flex-col">
            <header class="p-4 border-b flex justify-between items-center flex-shrink-0">
                <h3 class="text-lg font-medium">Select Questions</h3>
                <button id="close-modal-btn" class="text-2xl">&times;</button>
            </header>
            
            <main class="flex-grow flex overflow-hidden">
                {{-- Left Column: Chapter Filter --}}
                <div class="w-1/3 border-r overflow-y-auto p-4">
                    <h4 class="font-semibold mb-2">Filter by Chapter</h4>
                    <div class="space-y-2">
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="chapter-filter-checkbox rounded" value="all" checked>
                                <span class="ml-2 font-medium">All Chapters</span>
                            </label>
                        </div>
                        @foreach($chapters as $chapter)
                            <div>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="chapter-filter-checkbox rounded" value="{{ $chapter->id }}">
                                    <span class="ml-2">{{ $chapter->name }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Right Column: Question List --}}
                <div class="w-2/3 overflow-y-auto p-6 space-y-4" id="modal-question-list">
                    {{-- Questions will be loaded here by JavaScript --}}
                </div>
            </main>

            <footer class="p-4 border-t text-right flex-shrink-0">
                <button id="modal-done-btn" class="px-4 py-2 bg-blue-600 text-white rounded-md">Done</button>
            </footer>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('question-modal');
            const openModalButtons = document.querySelectorAll('.open-modal-btn');
            const closeModalBtn = document.getElementById('close-modal-btn');
            const modalDoneBtn = document.getElementById('modal-done-btn');
            const modalQuestionList = document.getElementById('modal-question-list');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            let currentRuleId = null;

            function updateAllRuleCounts() {
                fetch(`/api/papers/{{ $paper->id }}/stats`)
                    .then(response => response.json())
                    .then(stats => {
                        for (const ruleId in stats) {
                            const countElement = document.getElementById(`rule-count-${ruleId}`);
                            if (countElement) {
                                const requiredCount = countElement.textContent.split('/')[1].trim();
                                countElement.textContent = `${stats[ruleId]} / ${requiredCount}`;
                            }
                        }
                    });
            }

            function fetchAndDisplayQuestions() {
                if (!currentRuleId) return;

                modalQuestionList.innerHTML = '<p>Loading questions...</p>';

                const chapterCheckboxes = document.querySelectorAll('.chapter-filter-checkbox:checked');
                let chapterIds = Array.from(chapterCheckboxes).map(cb => cb.value);

                if (chapterIds.includes('all')) {
                    chapterIds = [];
                }
                
                const chapterQuery = chapterIds.map(id => `chapters[]=${id}`).join('&');
                const apiUrl = `/api/papers/{{ $paper->id }}/questions-for-rule/${currentRuleId}?${chapterQuery}`;

                fetch(apiUrl, {
                    headers: { 'Accept': 'application/json' },
                })
                .then(response => response.json())
                .then(data => {
                    modalQuestionList.innerHTML = '';
                    if (data.available_questions.length === 0) {
                        modalQuestionList.innerHTML = '<p>No questions found for the selected chapters and rule.</p>';
                        return;
                    }

                    data.available_questions.forEach(question => {
                        const isChecked = data.selected_ids.includes(question.id);
                        const div = document.createElement('div');
                        div.className = 'p-3 border rounded-md';
                        div.innerHTML = `
                            <label class="flex items-start space-x-3">
                                <input type="checkbox" class="modal-checkbox mt-1" value="${question.id}" ${isChecked ? 'checked' : ''}>
                                <div class="flex-1">
                                    <div class="font-semibold">${question.question_text}</div>
                                </div>
                            </label>
                        `;
                        modalQuestionList.appendChild(div);
                    });
                    if (window.MathJax) {
                        window.MathJax.typesetPromise();
                    }
                });
            }

            function openModal(ruleId) {
                currentRuleId = ruleId;
                modal.classList.remove('hidden');
                document.querySelectorAll('.chapter-filter-checkbox').forEach(cb => {
                    cb.checked = cb.value === 'all';
                });
                fetchAndDisplayQuestions();
            }

            function closeModal() {
                modal.classList.add('hidden');
                updateAllRuleCounts();
            }
            
            document.querySelector('.chapter-filter-checkbox[value="all"]').addEventListener('change', function() {
                if (this.checked) {
                    document.querySelectorAll('.chapter-filter-checkbox').forEach(cb => {
                        cb.checked = cb.value === 'all';
                    });
                }
                fetchAndDisplayQuestions();
            });

            document.querySelectorAll('.chapter-filter-checkbox:not([value="all"])').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        document.querySelector('.chapter-filter-checkbox[value="all"]').checked = false;
                    }
                    fetchAndDisplayQuestions();
                });
            });

            openModalButtons.forEach(btn => btn.addEventListener('click', () => openModal(btn.dataset.ruleId)));
            closeModalBtn.addEventListener('click', closeModal);
            modalDoneBtn.addEventListener('click', closeModal);
            modalQuestionList.addEventListener('change', function(e) {
                if (e.target.classList.contains('modal-checkbox')) {
                    const questionId = e.target.value;
                    const isChecked = e.target.checked;
                    const attachUrl = "{{ route('institute.papers.questions.attach', [$paper, ':questionId']) }}".replace(':questionId', questionId);
                    const detachUrl = "{{ route('institute.papers.questions.detach', [$paper, ':questionId']) }}".replace(':questionId', questionId);
                    const url = isChecked ? attachUrl : detachUrl;
                    fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }});
                }
            });

            updateAllRuleCounts();
        });
    </script>
</x-app-layout>
