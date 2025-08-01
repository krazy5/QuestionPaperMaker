<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Question</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <style>
        .alert-danger { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; padding: .75rem 1.25rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: .25rem; }
    </style>
</head>
<body>
    <header><h1>Add New Question to the Bank</h1></header>

    <main>
        @if ($errors->any())
            <div class="alert-danger">
                <strong>Whoops! Something went wrong.</strong>
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('admin.questions.store') }}" method="POST">
            @csrf
            
            <p><strong>Academic Details</strong></p>
            <label for="board_id">Board</label>
            <select name="board_id" id="board_id" required>
                @foreach($boards as $board)<option value="{{ $board->id }}">{{ $board->name }}</option>@endforeach
            </select>

            <label for="class_id">Class</label>
            <select name="class_id" id="class_id" required>
                <option value="">-- Select a Class --</option>
                 @foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach
            </select>

            <label for="subject_id">Subject</label>
            <select name="subject_id" id="subject_id" required><option value="">-- Select a Class First --</option></select>

            <label for="chapter_id">Chapter</label>
            <select name="chapter_id" id="chapter_id" required><option value="">-- Select a Subject First --</option></select>
            <hr>
            
            <p><strong>Question Details</strong></p>
            <label for="question_text">Question Text</label>
            <textarea name="question_text" id="question_text" rows="4" required></textarea>

            <label for="question_type">Question Type</label>
            <select name="question_type" id="question_type" required>
                <option value="mcq">Multiple Choice (MCQ)</option>
                <option value="short">Short Answer</option>
                <option value="long">Long Answer</option>
                <option value="true_false">True/False</option>
            </select>

            <div id="mcq_options_container" style="display: none; border: 1px solid #ccc; padding: 1rem; margin-top: 1rem;">
                <p><strong>MCQ Options</strong></p>
                <div id="options-wrapper">
                    <label>Option A</label><input type="text" name="options[]">
                    <label>Option B</label><input type="text" name="options[]">
                </div>
                <button type="button" id="add-option-btn">+ Add Another Option</button><hr>
                <label for="correct_answer">Correct Answer</label>
                <select name="correct_answer" id="correct_answer"></select>
            </div>

            <label for="marks">Marks</label>
            <input type="number" name="marks" id="marks" value="1" required>

            <label for="difficulty">Difficulty</label>
            <select name="difficulty" id="difficulty" required>
                <option value="easy">Easy</option><option value="medium" selected>Medium</option><option value="hard">Hard</option>
            </select>
            <hr>
            
            <p><strong>Answer & Solution</strong></p>
            <label for="answer_text">Answer Text (for short/long questions)</label>
            <textarea name="answer_text" id="answer_text" rows="3"></textarea>

            <label for="solution_text">Detailed Solution (Optional)</label>
            <textarea name="solution_text" id="solution_text" rows="5"></textarea>

            <button type="submit">Save Question</button>
            <a href="{{ route('admin.questions.index') }}">Cancel</a>
        </form>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const classSelect = document.getElementById('class_id');
            const subjectSelect = document.getElementById('subject_id');
            const chapterSelect = document.getElementById('chapter_id');

            // --- Chain: Class -> Subject ---
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

            // --- Chain: Subject -> Chapter ---
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
            
            // --- Logic for MCQ fields from previous step ---
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
                const optionCount = optionsWrapper.children.length / 2;
                correctAnswerSelect.innerHTML = '';
                for (let i = 0; i < optionCount; i++) {
                    const letter = String.fromCharCode(65 + i);
                    const option = document.createElement('option');
                    option.value = letter;
                    option.textContent = letter;
                    correctAnswerSelect.appendChild(option);
                }
            }
            addOptionBtn.addEventListener('click', function () {
                const optionCount = (optionsWrapper.children.length / 2) + 1;
                const letter = String.fromCharCode(64 + optionCount);
                optionsWrapper.innerHTML += `<label>Option ${letter}</label><input type="text" name="options[]">`;
                updateCorrectAnswerOptions();
            });
            questionTypeSelect.addEventListener('change', toggleMcqOptions);
            toggleMcqOptions();
            updateCorrectAnswerOptions();
        });
    </script>
</body>
</html>