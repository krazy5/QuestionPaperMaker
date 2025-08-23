<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                {{ __('Add New Question') }}
            </h2>
            <a href="{{ route('admin.questions.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm
                      hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600
                      dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-b from-white to-slate-50 dark:from-gray-900 dark:to-gray-950 border border-gray-200 dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-xl">
                <div class="p-6 sm:p-8">

                    @if ($errors->any())
                        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-200">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.questions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf

                        {{-- Academic Details --}}
                        <section>
                            <header class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Academic Details</h3>
                            </header>

                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="board_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Board</label>
                                    <select name="board_id" id="board_id" required
                                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white/90 px-3 py-2 text-gray-900 shadow-sm
                                                   focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                                                   dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
                                        @foreach($boards as $board)
                                            <option value="{{ $board->id }}" @selected(old('board_id')==$board->id)>{{ $board->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Class</label>
                                    <select name="class_id" id="class_id" required
                                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white/90 px-3 py-2 text-gray-900 shadow-sm
                                                   focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                                                   dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
                                        <option value="">-- Select a Class --</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" @selected(old('class_id')==$class->id)>{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subject</label>
                                    <select name="subject_id" id="subject_id" required
                                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white/90 px-3 py-2 text-gray-900 shadow-sm
                                                   focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                                                   dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
                                        <option value="">-- Select a Class First --</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="chapter_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Chapter</label>
                                    <select name="chapter_id" id="chapter_id" required
                                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white/90 px-3 py-2 text-gray-900 shadow-sm
                                                   focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                                                   dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
                                        <option value="">-- Select a Subject First --</option>
                                    </select>
                                </div>
                            </div>
                        </section>

                        {{-- Question Details --}}
                        <section>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Question Details</h3>

                            <div class="mt-4">
                                <label for="question_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Question Text</label>
                                <textarea name="question_text" id="question_text" rows="4" required
                                          class="mt-1 block w-full rounded-lg border border-gray-300 bg-white/90 px-3 py-2 text-gray-900 shadow-sm
                                                 focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                                                 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">{{ old('question_text') }}</textarea>
                            </div>

                            <div class="mt-4">
                                <label for="question_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Question Diagram (Optional)</label>
                                <input type="file" name="question_image" id="question_image"
                                       class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold
                                              file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:text-gray-300" accept="image/*">
                                <img id="question_image_preview" src="#" alt="" class="mt-2 rounded-md max-h-48 border p-1 hidden dark:border-gray-700"/>
                            </div>

                            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="question_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Question Type</label>
                                    <select name="question_type" id="question_type" required
                                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white/90 px-3 py-2 text-gray-900 shadow-sm
                                                   focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                                                   dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
                                        <option value="mcq" @selected(old('question_type')==='mcq')>Multiple Choice (MCQ)</option>
                                        <option value="short" @selected(old('question_type')==='short')>Short Answer</option>
                                        <option value="long" @selected(old('question_type')==='long')>Long Answer</option>
                                        <option value="true_false" @selected(old('question_type')==='true_false')>True/False</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="marks" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Marks</label>
                                    <input type="number" min="1" name="marks" id="marks" value="{{ old('marks', 1) }}" required
                                           class="mt-1 block w-full rounded-lg border border-gray-300 bg-white/90 px-3 py-2 text-gray-900 shadow-sm
                                                  focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                                                  dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
                                </div>
                                <div>
                                    <label for="difficulty" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Difficulty</label>
                                    <select name="difficulty" id="difficulty" required
                                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white/90 px-3 py-2 text-gray-900 shadow-sm
                                                   focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                                                   dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
                                        <option value="easy" @selected(old('difficulty')==='easy')>Easy</option>
                                        <option value="medium" @selected(old('difficulty','medium')==='medium')>Medium</option>
                                        <option value="hard" @selected(old('difficulty')==='hard')>Hard</option>
                                    </select>
                                </div>
                            </div>
                        </section>

                        {{-- MCQ Options (conditional) --}}
                        <section id="mcq_options_container" class="p-4 rounded-lg border border-gray-200 dark:border-gray-800 bg-white/80 dark:bg-gray-900/70 hidden">
                            <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100">MCQ Options</h3>

                            <div id="options-wrapper" class="mt-3 space-y-2">
                                <div>
                                    <label class="text-sm text-gray-700 dark:text-gray-300">Option A</label>
                                    <input type="text" name="options[]" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white/90 px-3 py-2
                                           dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
                                </div>
                                <div>
                                    <label class="text-sm text-gray-700 dark:text-gray-300">Option B</label>
                                    <input type="text" name="options[]" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white/90 px-3 py-2
                                           dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
                                </div>
                            </div>

                            <button type="button" id="add-option-btn"
                                    class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:underline">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/></svg>
                                Add Option
                            </button>

                            <div class="mt-4">
                                <label for="correct_answer" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correct Answer</label>
                                <select name="correct_answer" id="correct_answer"
                                        class="mt-1 block w-full rounded-lg border border-gray-300 bg-white/90 px-3 py-2 text-gray-900 shadow-sm
                                               dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100"></select>
                            </div>
                        </section>

                        {{-- Answer & Solution --}}
                        <section>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Answer & Solution</h3>

                            <div class="mt-4">
                                <label for="answer_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Answer Text</label>
                                <textarea name="answer_text" id="answer_text" rows="3"
                                          class="mt-1 block w-full rounded-lg border border-gray-300 bg-white/90 px-3 py-2 text-gray-900 shadow-sm
                                                 focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                                                 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">{{ old('answer_text') }}</textarea>
                            </div>

                            <div class="mt-4">
                                <label for="answer_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Answer Diagram (Optional)</label>
                                <input type="file" name="answer_image" id="answer_image"
                                       class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold
                                              file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:text-gray-300" accept="image/*">
                                <img id="answer_image_preview" src="#" alt="" class="mt-2 rounded-md max-h-48 border p-1 hidden dark:border-gray-700"/>
                            </div>

                            <div class="mt-4">
                                <label for="solution_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Detailed Solution (Optional)</label>
                                <textarea name="solution_text" id="solution_text" rows="5"
                                          class="mt-1 block w-full rounded-lg border border-gray-300 bg-white/90 px-3 py-2 text-gray-900 shadow-sm
                                                 focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                                                 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">{{ old('solution_text') }}</textarea>
                            </div>
                        </section>

                        <div class="flex justify-end">
                            <a href="{{ route('admin.questions.index') }}"
                               class="mr-3 inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm
                                      hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600
                                      dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Cancel</a>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm
                                           hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25L15 10.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Save Question
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Dependent selects + MCQ UI + previews --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const classSelect   = document.getElementById('class_id');
            const subjectSelect = document.getElementById('subject_id');
            const chapterSelect = document.getElementById('chapter_id');

            async function loadSubjects(classId) {
                subjectSelect.innerHTML = '<option value="">Loading…</option>';
                chapterSelect.innerHTML = '<option value="">-- Select a Subject First --</option>';
                if (!classId) {
                    subjectSelect.innerHTML = '<option value="">-- Select a Class First --</option>';
                    return;
                }
                const res = await fetch(`/api/subjects-by-class?class_id=${classId}`);
                const data = await res.json();
                subjectSelect.innerHTML = '<option value="">-- Select a Subject --</option>';
                data.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.id; opt.textContent = s.name;
                    subjectSelect.appendChild(opt);
                });
            }

            async function loadChapters(subjectId) {
                chapterSelect.innerHTML = '<option value="">Loading…</option>';
                if (!subjectId) {
                    chapterSelect.innerHTML = '<option value="">-- Select a Subject First --</option>';
                    return;
                }
                const res = await fetch(`/api/chapters-by-subject?subject_id=${subjectId}`);
                const data = await res.json();
                chapterSelect.innerHTML = '<option value="">-- Select a Chapter --</option>';
                data.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id; opt.textContent = c.name;
                    chapterSelect.appendChild(opt);
                });
            }

            classSelect.addEventListener('change', () => loadSubjects(classSelect.value));
            subjectSelect.addEventListener('change', () => loadChapters(subjectSelect.value));

            // MCQ options + correct answer
            const questionTypeSelect    = document.getElementById('question_type');
            const mcqOptionsContainer   = document.getElementById('mcq_options_container');
            const addOptionBtn          = document.getElementById('add-option-btn');
            const optionsWrapper        = document.getElementById('options-wrapper');
            const correctAnswerSelect   = document.getElementById('correct_answer');

            function toggleMcq() {
                const mcqInputs = mcqOptionsContainer.querySelectorAll('input, select, button');
                const isMcq = questionTypeSelect.value === 'mcq';
                mcqOptionsContainer.classList.toggle('hidden', !isMcq);
                mcqInputs.forEach(el => el.disabled = !isMcq);
                if (isMcq) updateCorrectAnswerOptions();
            }

            function updateCorrectAnswerOptions() {
                const inputs = optionsWrapper.querySelectorAll('input[type="text"]');
                correctAnswerSelect.innerHTML = '';
                inputs.forEach((input, idx) => {
                    const letter = String.fromCharCode(65 + idx);
                    const opt = document.createElement('option');
                    opt.value = letter; opt.textContent = `Option ${letter}`;
                    correctAnswerSelect.appendChild(opt);
                });
            }

            addOptionBtn.addEventListener('click', () => {
                const count = optionsWrapper.querySelectorAll('input[type="text"]').length;
                const letter = String.fromCharCode(65 + count);
                const div = document.createElement('div');
                div.innerHTML = `
                    <label class="text-sm text-gray-700 dark:text-gray-300">Option ${letter}</label>
                    <input type="text" name="options[]" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white/90 px-3 py-2
                           dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">`;
                optionsWrapper.appendChild(div);
                updateCorrectAnswerOptions();
            });

            questionTypeSelect.addEventListener('change', toggleMcq);
            toggleMcq();

            // Image previews
            const preview = (input, img) => {
                input.addEventListener('change', e => {
                    if (e.target.files && e.target.files[0]) {
                        const reader = new FileReader();
                        reader.onload = ev => {
                            img.src = ev.target.result;
                            img.classList.remove('hidden');
                        };
                        reader.readAsDataURL(e.target.files[0]);
                    }
                });
            };
            preview(document.getElementById('question_image'), document.getElementById('question_image_preview'));
            preview(document.getElementById('answer_image'), document.getElementById('answer_image_preview'));
        });
    </script>
</x-app-layout>
