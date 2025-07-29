<!DOCTYPE html>
<html><head><title>Edit Subject</title><link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css"></head>
<body><header><h1>Edit Subject: {{ $subject->name }}</h1></header><main>
<form action="{{ route('subjects.update', $subject) }}" method="POST">
    @csrf @method('PUT')
    <div><label for="name">Subject Name</label><input type="text" name="name" id="name" value="{{ $subject->name }}" required></div>
    <div><label for="class_id">Class</label>
        <select name="class_id" id="class_id" required>
            <option value="">-- Select a Class --</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" @selected($subject->class_id == $class->id)>{{ $class->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit">Update Subject</button><a href="{{ route('subjects.index') }}">Cancel</a>
</form>
</main></body></html>