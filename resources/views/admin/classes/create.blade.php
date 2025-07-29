<!DOCTYPE html>
<html>
<head>
    <title>Add New Class</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
    <header><h1>Add New Class</h1></header>
    <main>
        <form action="{{ route('classes.store') }}" method="POST">
            @csrf
            <div>
                <label for="name">Class Name</label>
                <input type="text" name="name" id="name" required>
            </div>
            <button type="submit">Save Class</button>
            <a href="{{ route('classes.index') }}">Cancel</a>
        </form>
    </main>
</body>
</html>