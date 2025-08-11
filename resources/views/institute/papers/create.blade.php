<!DOCTYPE html>
<html>
<head>
    <title>Create New Paper</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
    <header><h1>Create New Question Paper</h1>
        <p>Step 1: Define the paper's details.</p>
    </header>
    <main>
        @if ($errors->any())
            <div class="alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('institute.papers.store') }}" method="POST">
            @csrf
            
            <label for="title">Paper Title</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required>

            <label for="board_id">Board</label>
            <select name="board_id" id="board_id" required>
                @foreach($boards as $board)
                    <option value="{{ $board->id }}">{{ $board->name }}</option>
                @endforeach
            </select>

            {{-- The "Class" dropdown now has an ID for JavaScript to target --}}
            <label for="class_id">Class</label>
            <select name="class_id" id="class_id" required>
                 <option value="">-- Select a Class --</option> {{-- Added a default option --}}
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
            
            {{-- The "Subject" dropdown is now initially empty --}}
            <label for="subject_id">Subject</label>
            <select name="subject_id" id="subject_id" required>
                {{-- Options will be loaded dynamically by JavaScript --}}
            </select>

            <div>
                <label for="time_allowed" class="block text-sm font-medium text-gray-700">Time Allowed (e.g., "3 Hrs", "90 Mins")</label>
                <input type="text" name="time_allowed" id="time_allowed" value="{{ old('time_allowed', '3 Hrs') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <label for="exam_date" class="block text-sm font-medium text-gray-700">Exam Date</label>
                <input type="date" name="exam_date" id="exam_date" value="{{ old('exam_date', now()->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <label for="total_marks">Total Marks</label>
            <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks', 100) }}" required>

            <label for="instructions">Instructions (Optional)</label>
            <textarea name="instructions" id="instructions" rows="5">{{ old('instructions') }}</textarea>

            <button type="submit">Save and Select Questions &rarr;</button>
        </form>
    </main>

    {{-- NEW JAVASCRIPT SECTION --}}
    <script>
        document.getElementById('class_id').addEventListener('change', function() {
            const classId = this.value;
            const subjectSelect = document.getElementById('subject_id');
            
            // Clear existing options
            subjectSelect.innerHTML = '<option value="">-- Loading subjects... --</option>';

            if (!classId) {
                subjectSelect.innerHTML = '<option value="">-- Select a class first --</option>';
                return;
            }

            // Fetch subjects for the selected class using AJAX
            // The URL points to a route we will create in the next step
            fetch(`/institute/get-subjects-for-class/${classId}`)
                .then(response => response.json())
                .then(data => {
                    subjectSelect.innerHTML = ''; // Clear loading message
                    if (data.length === 0) {
                        subjectSelect.innerHTML = '<option value="">-- No subjects found --</option>';
                    } else {
                        data.forEach(subject => {
                            const option = document.createElement('option');
                            option.value = subject.id;
                            option.textContent = subject.name;
                            subjectSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching subjects:', error);
                    subjectSelect.innerHTML = '<option value="">-- Error loading subjects --</option>';
                });
        });
    </script>
</body>
</html>
