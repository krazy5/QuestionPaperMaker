<!DOCTYPE html>
<html><head><title>Edit Chapter</title><link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css"></head>
<body><header><h1>Edit Chapter: {{ $chapter->name }}</h1></header><main>
<form action="{{ route('chapters.update', $chapter) }}" method="POST">
    @csrf @method('PUT')
    <div><label for="name">Chapter Name</label><input type="text" name="name" id="name" value="{{ $chapter->name }}" required></div>
    <div><label for="subject_id">Subject</label>
        <select name="subject_id" id="subject_id" required>
            <option value="">-- Select a Subject --</option>
            @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}" @selected($chapter->subject_id == $subject->id)>{{ $subject->name }} ({{$subject->academicClass->name}})</option>
            @endforeach
        </select>
    </div>
    <button type="submit">Update Chapter</button><a href="{{ route('chapters.index') }}">Cancel</a>
</form>
</main></body></html>