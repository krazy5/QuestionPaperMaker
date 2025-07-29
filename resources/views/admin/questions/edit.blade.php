<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Question</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <style>
        .alert-danger { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; padding: .75rem 1.25rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: .25rem; }
        .alert-danger ul { margin-bottom: 0; }
    </style>
</head>
<body>
    <header>
        <h1>Edit Question</h1>
    </header>
    <main>
        @if ($errors->any())
            <div class="alert-danger">
                <strong>Whoops! Something went wrong.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('questions.update', $question) }}" method="POST">
            @csrf
            @method('PUT')

            <p><strong>Academic Details</strong></p>
            <label for="board_id">Board</label>
            <select name="board_id" id="board_id" required>
                @foreach($boards as $board)
                    <option value="{{ $board->id }}" @selected($question->board_id == $board->id)>{{ $board->name }}</option>
                @endforeach
            </select>

            <label for="class_id">Class</label>
            <select name="class_id" id="class_id" required>
                 @foreach($classes as $class)
                    <option value="{{ $class->id }}" @selected($question->class_id == $class->id)>{{ $class->name }}</option>
                @endforeach
            </select>
            
            <label for="subject_id">Subject</label>
            <select name="subject_id" id="subject_id" required>
                 @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" @selected($question->subject_id == $subject->id)>{{ $subject->name }} ({{ $subject->academicClass->name }})</option>
                @endforeach
            </select>

            <label for="chapter_id">Chapter</label>
            <select name="chapter_id" id="chapter_id" required>
                 @foreach($chapters as $chapter)
                    <option value="{{ $chapter->id }}" @selected($question->chapter_id == $chapter->id)>{{ $chapter->name }} ({{ $chapter->subject->name }})</option>
                @endforeach
            </select>
            <hr>

            <p><strong>Question Details</strong></p>
            <label for="question_text">Question Text</label>
            <textarea name="question_text" id="question_text" rows="4" required>{{ $question->question_text }}</textarea>

            <label for="question_type">Question Type</label>
            <select name="question_type" id="question_type" required>
                <option value="mcq" @selected($question->question_type == 'mcq')>Multiple Choice (MCQ)</option>
                <option value="short" @selected($question->question_type == 'short')>Short Answer</option>
                <option value="long" @selected($question->question_type == 'long')>Long Answer</option>
                <option value="true_false" @selected($question->question_type == 'true_false')>True/False</option>
            </select>

            <div id="mcq_options_container" style="display: none; border: 1px solid #ccc; padding: 1rem; margin-top: 1rem;">
                <p><strong>MCQ Options</strong></p>
                <div id="options-wrapper">
                    @if($question->question_type == 'mcq' && is_array($question->options))
                        @foreach($question->options as $index => $option)
                            <label for="option_{{ $loop->iteration }}">Option {{ chr(65 + $index) }}</label>
                            <input type="text" name="options[]" id="option_{{ $loop->iteration }}" class="mcq-option" value="{{ $option }}">
                        @endforeach
                    @else
                         <label for="option_1">Option A</label>
                         <input type="text" name="options[]" id="option_1" class="mcq-option">
                         <label for="option_2">Option B</label>
                         <input type="text" name="options[]" id="option_2" class="mcq-option">
                    @endif
                </div>
                <button type="button" id="add-option-btn">+ Add Another Option</button>
                <hr>
                <label for="correct_answer">Correct Answer</label>
                <select name="correct_answer" id="correct_answer"></select>
            </div>
            
            <label for="marks">Marks</label>
            <input type="number" name="marks" id="marks" value="{{ $question->marks }}" required>

            <label for="difficulty">Difficulty</label>
            <select name="difficulty" id="difficulty" required>
                <option value="easy" @selected($question->difficulty == 'easy')>Easy</option>
                <option value="medium" @selected($question->difficulty == 'medium')>Medium</option>
                <option value="hard" @selected($question->difficulty == 'hard')>Hard</option>
            </select>
            <hr>

            <p><strong>Answer & Solution</strong></p>
            <label for="answer_text">Answer Text (for short/long questions)</label>
            <textarea name="answer_text" id="answer_text" rows="3">{{ $question->answer_text }}</textarea>

            <label for="solution_text">Detailed Solution (Optional)</label>
            <textarea name="solution_text" id="solution_text" rows="5">{{ $question->solution_text }}</textarea>

            <button type="submit">Update Question</button>
            <a href="{{ route('questions.index') }}">Cancel</a>
        </form>
    </main>
    
    <script>
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
                const optionCount = optionsWrapper.querySelectorAll('input.mcq-option').length;
                correctAnswerSelect.innerHTML = '';
                for (let i = 0; i < optionCount; i++) {
                    const letter = String.fromCharCode(65 + i);
                    const option = document.createElement('option');
                    option.value = letter;
                    option.textContent = letter;
                    if(letter === existingCorrectAnswer) {
                        option.selected = true;
                    }
                    correctAnswerSelect.appendChild(option);
                }
            }

            addOptionBtn.addEventListener('click', function () {
                const optionCount = optionsWrapper.querySelectorAll('input.mcq-option').length + 1;
                const letter = String.fromCharCode(64 + optionCount);

                const newLabel = document.createElement('label');
                newLabel.textContent = `Option ${letter}`;
                const newInput = document.createElement('input');
                newInput.type = 'text';
                newInput.name = 'options[]';
                newInput.classList.add('mcq-option');

                optionsWrapper.appendChild(newLabel);
                optionsWrapper.appendChild(newInput);
                updateCorrectAnswerOptions();
            });

            questionTypeSelect.addEventListener('change', toggleMcqOptions);
            toggleMcqOptions();
            updateCorrectAnswerOptions();
        });
    </script>
</body>
</html>