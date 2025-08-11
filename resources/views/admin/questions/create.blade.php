<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Question') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('admin.questions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        {{-- Academic Details Section --}}
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Academic Details</h3>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="board_id" class="block text-sm font-medium text-gray-700">Board</label>
                                    <select name="board_id" id="board_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        @foreach($boards as $board)<option value="{{ $board->id }}">{{ $board->name }}</option>@endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="class_id" class="block text-sm font-medium text-gray-700">Class</label>
                                    <select name="class_id" id="class_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="">-- Select a Class --</option>
                                        @foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject</label>
                                    <select name="subject_id" id="subject_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"><option value="">-- Select a Class First --</option></select>
                                </div>
                                <div>
                                    <label for="chapter_id" class="block text-sm font-medium text-gray-700">Chapter</label>
                                    <select name="chapter_id" id="chapter_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"><option value="">-- Select a Subject First --</option></select>
                                </div>
                            </div>
                        </div>

                        {{-- Question Details Section --}}
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Question Details</h3>
                            <div class="mt-4">
                                <label for="question_text" class="block text-sm font-medium text-gray-700">Question Text</label>
                                <textarea name="question_text" id="question_text" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('question_text') }}</textarea>
                            </div>
                            
                            <div class="mt-4">
                                <label for="question_image" class="block text-sm font-medium text-gray-700">Question Diagram (Optional)</label>
                                <input type="file" name="question_image" id="question_image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>

                            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="question_type" class="block text-sm font-medium text-gray-700">Question Type</label>
                                    <select name="question_type" id="question_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="mcq">Multiple Choice (MCQ)</option>
                                        <option value="short">Short Answer</option>
                                        <option value="long">Long Answer</option>
                                        <option value="true_false">True/False</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="marks" class="block text-sm font-medium text-gray-700">Marks</label>
                                    <input type="number" name="marks" id="marks" value="{{ old('marks', 1) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label for="difficulty" class="block text-sm font-medium text-gray-700">Difficulty</label>
                                    <select name="difficulty" id="difficulty" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="easy">Easy</option>
                                        <option value="medium" selected>Medium</option>
                                        <option value="hard">Hard</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- MCQ Options Container --}}
                        <div id="mcq_options_container" style="display: none;" class="p-4 border rounded-md">
                            <h3 class="text-md font-medium text-gray-900">MCQ Options</h3>
                            <div id="options-wrapper" class="mt-2 space-y-2">
                                <div><label class="text-sm">Option A</label><input type="text" name="options[]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></div>
                                <div><label class="text-sm">Option B</label><input type="text" name="options[]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></div>
                            </div>
                            <button type="button" id="add-option-btn" class="mt-2 text-sm text-blue-600 hover:underline">+ Add Another Option</button>
                            <hr class="my-4">
                            <label for="correct_answer" class="block text-sm font-medium text-gray-700">Correct Answer</label>
                            <select name="correct_answer" id="correct_answer" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></select>
                        </div>

                        {{-- Answer & Solution Section --}}
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Answer & Solution</h3>
                            <div class="mt-4">
                                <label for="answer_text" class="block text-sm font-medium text-gray-700">Answer Text</label>
                                <textarea name="answer_text" id="answer_text" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('answer_text') }}</textarea>
                            </div>

                            <div class="mt-4">
                                <label for="answer_image" class="block text-sm font-medium text-gray-700">Answer Diagram (Optional)</label>
                                <input type="file" name="answer_image" id="answer_image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>

                            <div class="mt-4">
                                <label for="solution_text" class="block text-sm font-medium text-gray-700">Detailed Solution (Optional)</label>
                                <textarea name="solution_text" id="solution_text" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('solution_text') }}</textarea>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('admin.questions.index') }}" class="mr-4 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save Question</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const classSelect = document.getElementById('class_id');
            const subjectSelect = document.getElementById('subject_id');
            const chapterSelect = document.getElementById('chapter_id');

            classSelect.addEventListener('change', function () {
                const classId = this.value;
                subjectSelect.innerHTML = '<option value="">Loading...</option>';
                chapterSelect.innerHTML = '<option value="">-- Select a Subject First --</option>';

                if (classId) {
                    fetch(`/api/subjects-by-class?class_id=${classId}`)
                        .then(response => response.json())
                        .then(data => {
                            subjectSelect.innerHTML = '<option value="">-- Select a Subject --</option>';
                            data.forEach(subject => {
                                subjectSelect.innerHTML += `<option value="${subject.id}">${subject.name}</option>`;
                            });
                        });
                } else {
                    subjectSelect.innerHTML = '<option value="">-- Select a Class First --</option>';
                }
            });

            subjectSelect.addEventListener('change', function () {
                const subjectId = this.value;
                chapterSelect.innerHTML = '<option value="">Loading...</option>';

                if (subjectId) {
                    fetch(`/api/chapters-by-subject?subject_id=${subjectId}`)
                        .then(response => response.json())
                        .then(data => {
                            chapterSelect.innerHTML = '<option value="">-- Select a Chapter --</option>';
                            data.forEach(chapter => {
                                chapterSelect.innerHTML += `<option value="${chapter.id}">${chapter.name}</option>`;
                            });
                        });
                } else {
                    chapterSelect.innerHTML = '<option value="">-- Select a Subject First --</option>';
                }
            });
            
            const questionTypeSelect = document.getElementById('question_type');
            const mcqOptionsContainer = document.getElementById('mcq_options_container');
            const addOptionBtn = document.getElementById('add-option-btn');
            const optionsWrapper = document.getElementById('options-wrapper');
            const correctAnswerSelect = document.getElementById('correct_answer');

            function toggleMcqOptions() {
                const mcqInputs = mcqOptionsContainer.querySelectorAll('input, select');
                if (questionTypeSelect.value === 'mcq') {
                    mcqOptionsContainer.style.display = 'block';
                    mcqInputs.forEach(input => input.disabled = false);
                } else {
                    mcqOptionsContainer.style.display = 'none';
                    mcqInputs.forEach(input => input.disabled = true);
                }
            }
            function updateCorrectAnswerOptions() {
                const optionInputs = optionsWrapper.querySelectorAll('input');
                correctAnswerSelect.innerHTML = '';
                optionInputs.forEach((input, index) => {
                    const letter = String.fromCharCode(65 + index);
                    const option = document.createElement('option');
                    option.value = letter;
                    option.textContent = `Option ${letter}`;
                    correctAnswerSelect.appendChild(option);
                });
            }
            addOptionBtn.addEventListener('click', function () {
                const optionCount = optionsWrapper.querySelectorAll('input').length;
                const letter = String.fromCharCode(65 + optionCount);
                const newOptionDiv = document.createElement('div');
                newOptionDiv.innerHTML = `<label class="text-sm">Option ${letter}</label><input type="text" name="options[]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">`;
                optionsWrapper.appendChild(newOptionDiv);
                updateCorrectAnswerOptions();
            });
            questionTypeSelect.addEventListener('change', toggleMcqOptions);
            toggleMcqOptions();
            updateCorrectAnswerOptions();
        });
    </script>
</x-app-layout>
