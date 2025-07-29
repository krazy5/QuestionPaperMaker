<!DOCTYPE html>
<html><head><title>Add New Subject</title><link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css"></head>
<body><header><h1>Add New Subject</h1></header><main>
<form action="{{ route('subjects.store') }}" method="POST">
    @csrf
    <div><label for="name">Subject Name</label><input type="text" name="name" id="name" required></div>
    <div><label for="class_id">Class</label>
        <select name="class_id" id="class_id" required>
            <option value="">-- Select a Class --</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}">{{ $class->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit">Save Subject</button><a href="{{ route('subjects.index') }}">Cancel</a>
</form>
</main></body></html>