<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Board</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
    <header>
        <h1>Edit Board: {{ $board->name }}</h1>
    </header>

    <main>
        <form action="{{ route('boards.update', $board) }}" method="POST">
            @csrf @method('PUT') <div>
                <label for="name">Board Name</label>
                <input type="text" name="name" id="name" value="{{ $board->name }}" required>
            </div>

            <button type="submit">Update Board</button>
            <a href="{{ route('boards.index') }}">Cancel</a>
        </form>
    </main>
</body>
</html>