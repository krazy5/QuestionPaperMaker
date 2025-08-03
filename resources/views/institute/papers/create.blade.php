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

            <label for="class_id">Class</label>
            <select name="class_id" id="class_id" required>
                 @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
            
            <label for="subject_id">Subject</label>
            <select name="subject_id" id="subject_id" required>
                 @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->academicClass->name }})</option>
                @endforeach
            </select>
            <div>
                      <label for="time_allowed" class="block text-sm font-medium text-gray-700">Time Allowed (e.g., "3 Hrs", "90 Mins")</label>
                      <input type="text" name="time_allowed" id="time_allowed" value="{{ old('time_allowed', '3 Hrs') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <label for="total_marks">Total Marks</label>
            <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks', 100) }}" required>

            <label for="instructions">Instructions (Optional)</label>
            <textarea name="instructions" id="instructions" rows="5">{{ old('instructions') }}</textarea>

            <button type="submit">Save and Select Questions &rarr;</button>
        </form>
    </main>
</body>
</html>