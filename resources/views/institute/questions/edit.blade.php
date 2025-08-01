<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Question') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">

                    <form action="{{ route('institute.questions.update', $question) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            {{-- Academic Details --}}
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Academic Details</h3>
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="board_id" class="block text-sm font-medium text-gray-700">Board</label>
                                        <select name="board_id" id="board_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                            @foreach($boards as $board)<option value="{{ $board->id }}" @selected($question->board_id == $board->id)>{{ $board->name }}</option>@endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="class_id" class="block text-sm font-medium text-gray-700">Class</label>
                                        <select name="class_id" id="class_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                            @foreach($classes as $class)<option value="{{ $class->id }}" @selected($question->class_id == $class->id)>{{ $class->name }}</option>@endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject</label>
                                        <select name="subject_id" id="subject_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                            @foreach($subjects as $subject)<option value="{{ $subject->id }}" @selected($question->subject_id == $subject->id)>{{ $subject->name }} ({{ $subject->academicClass->name }})</option>@endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="chapter_id" class="block text-sm font-medium text-gray-700">Chapter</label>
                                        <select name="chapter_id" id="chapter_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                            @foreach($chapters as $chapter)<option value="{{ $chapter->id }}" @selected($question->chapter_id == $chapter->id)>{{ $chapter->name }} ({{ $chapter->subject->name }})</option>@endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Question Details --}}
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Question Details</h3>
                                <div class="mt-4">
                                    <label for="question_text" class="block text-sm font-medium text-gray-700">Question Text</label>
                                    <textarea name="question_text" id="question_text" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ $question->question_text }}</textarea>
                                </div>
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label for="question_type" class="block text-sm font-medium text-gray-700">Question Type</label>
                                        <select name="question_type" id="question_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                            <option value="mcq" @selected($question->question_type == 'mcq')>MCQ</option>
                                            <option value="short" @selected($question->question_type == 'short')>Short Answer</option>
                                            <option value="long" @selected($question->question_type == 'long')>Long Answer</option>
                                            <option value="true_false" @selected($question->question_type == 'true_false')>True/False</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="marks" class="block text-sm font-medium text-gray-700">Marks</label>
                                        <input type="number" name="marks" id="marks" value="{{ $question->marks }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>
                                    <div>
                                        <label for="difficulty" class="block text-sm font-medium text-gray-700">Difficulty</label>
                                        <select name="difficulty" id="difficulty" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                            <option value="easy" @selected($question->difficulty == 'easy')>Easy</option>
                                            <option value="medium" @selected($question->difficulty == 'medium')>Medium</option>
                                            <option value="hard" @selected($question->difficulty == 'hard')>Hard</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- MCQ Options Container --}}
                            <div id="mcq_options_container" style="display: none;" class="p-4 border rounded-md">
                                <h3 class="text-md font-medium text-gray-900">MCQ Options</h3>
                                <div id="options-wrapper" class="mt-2 space-y-2">
                                    @if($question->question_type == 'mcq' && is_array($question->options))
                                        @foreach($question->options as $option)
                                            <div><label class="text-sm">Option</label><input type="text" name="options[]" value="{{ $option }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></div>
                                        @endforeach
                                    @else
                                        <div><label class="text-sm">Option A</label><input type="text" name="options[]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></div>
                                        <div><label class="text-sm">Option B</label><input type="text" name="options[]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></div>
                                    @endif
                                </div>
                                <button type="button" id="add-option-btn" class="mt-2 text-sm text-blue-600 hover:underline">+ Add Another Option</button>
                                <hr class="my-4">
                                <label for="correct_answer" class="block text-sm font-medium text-gray-700">Correct Answer</label>
                                <select name="correct_answer" id="correct_answer" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></select>
                            </div>

                            {{-- Answer & Solution --}}
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Answer & Solution</h3>
                                <div class="mt-4">
                                    <label for="answer_text" class="block text-sm font-medium text-gray-700">Answer Text</label>
                                    <textarea name="answer_text" id="answer_text" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ $question->answer_text }}</textarea>
                                </div>
                                <div class="mt-4">
                                    <label for="solution_text" class="block text-sm font-medium text-gray-700">Detailed Solution (Optional)</label>
                                    <textarea name="solution_text" id="solution_text" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ $question->solution_text }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <a href="{{ route('institute.questions.index') }}" class="mr-4 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update Question</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // This JavaScript is for the dynamic MCQ fields
        document.addEventListener('DOMContentLoaded', function () {
            const questionTypeSelect = document.getElementById('question_type');
            const mcqOptionsContainer = document.getElementById('mcq_options_container');
            const addOptionBtn = document.getElementById('add-option-btn');
            const optionsWrapper = document.getElementById('options-wrapper');
            const correctAnswerSelect = document.getElementById('correct_answer');
            const existingCorrectAnswer = "{{ $question->correct_answer }}";

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
                    if(letter === existingCorrectAnswer) {
                        option.selected = true;
                    }
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
