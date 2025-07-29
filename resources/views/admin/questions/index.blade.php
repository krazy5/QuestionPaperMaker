<!DOCTYPE html>
<html>
<head>
    <title>Manage Questions</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <style>.link-button{background:none;border:none;color:blue;text-decoration:underline;cursor:pointer;padding:0;font-size:inherit;font-family:inherit;}</style>
</head>
<body>
    <header><h1>Question Bank</h1></header>
    <main>
        @if (session('success'))
            <div class="notice"><p>{{ session('success') }}</p></div>
        @endif
        <a href="{{ route('questions.create') }}"><button>+ Add New Question</button></a>
        <hr>
        <table>
            <thead>
                <tr>
                    <th>Question</th>
                    <th>Class</th>
                    <th>Subject</th>
                    <th>Chapter</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($questions as $question)
                    <tr>
                        <td>{{ \Illuminate\Support\Str::limit($question->question_text, 80) }}</td>
                        <td>{{ $question->subject->academicClass->name ?? 'N/A' }}</td>
                        <td>{{ $question->subject->name ?? 'N/A' }}</td>
                        <td>{{ $question->chapter->name ?? 'N/A' }}</td>
                        <td>{{ strtoupper($question->question_type) }}</td>
                        <td>
                            <a href="{{ route('questions.edit', $question) }}">Edit</a> |
                            <form action="{{ route('questions.destroy', $question) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="link-button" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No questions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>
</body>
</html>