<!DOCTYPE html>
<html>
<head>
    <title>Edit Paper Details</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
    <header><h1>Edit Paper Details</h1></header>
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

        <a href="{{ route('institute.papers.questions.select', $paper) }}">
            <button>Edit Selected Questions</button>
        </a>
        <hr>

        <form action="{{ route('institute.papers.update', $paper) }}" method="POST">
            @csrf
            @method('PUT') <label for="title">Paper Title</label>
            <input type="text" name="title" id="title" value="{{ $paper->title }}" required>

            <label for="board_id">Board</label>
            <select name="board_id" id="board_id" required>
                @foreach($boards as $board)
                    <option value="{{ $board->id }}" @selected($paper->board_id == $board->id)>{{ $board->name }}</option>
                @endforeach
            </select>

            <label for="class_id">Class</label>
            <select name="class_id" id="class_id" required>
                 @foreach($classes as $class)
                    <option value="{{ $class->id }}" @selected($paper->class_id == $class->id)>{{ $class->name }}</option>
                @endforeach
            </select>
            
            <label for="subject_id">Subject</label>
            <select name="subject_id" id="subject_id" required>
                 @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" @selected($paper->subject_id == $subject->id)>{{ $subject->name }} ({{ $subject->academicClass->name }})</option>
                @endforeach
            </select>

            <label for="total_marks">Total Marks</label>
            <input type="number" name="total_marks" id="total_marks" value="{{ $paper->total_marks }}" required>

            <div>
                <label for="time_allowed" class="block text-sm font-medium text-gray-700">Time Allowed (e.g., "3 Hrs", "90 Mins")</label>
                <input type="text" name="time_allowed" id="time_allowed" value="{{ old('time_allowed', $paper->time_allowed) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <label for="instructions">Instructions (Optional)</label>
            <textarea name="instructions" id="instructions" rows="5">{{ $paper->instructions }}</textarea>

            <button type="submit">Update Paper Details</button>
        </form>
    </main>
</body>
</html>