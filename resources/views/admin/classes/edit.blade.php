<!DOCTYPE html>
<html>
<head>
    <title>Edit Class</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
    <header><h1>Edit Class: {{ $class->name }}</h1></header>
    <main>
        <form action="{{ route('admin.classes.update', $class) }}" method="POST">
            @csrf
            @method('PUT')
            <div>
                <label for="name">Class Name</label>
                <input type="text" name="name" id="name" value="{{ $class->name }}" required>
            </div>
            <button type="submit">Update Class</button>
            <a href="{{ route('admin.classes.index') }}">Cancel</a>
        </form>
    </main>
</body>
</html>