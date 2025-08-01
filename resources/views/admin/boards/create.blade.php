<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Board</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
    <header>
        <h1>Add New Board</h1>
    </header>

    <main>
        <form action="{{ route('boards.store') }}" method="POST">
            @csrf

            <div>
                <label for="name">Board Name</label>
                <input type="text" name="name" id="name" required>
            </div>

            <button type="submit">Save Board</button>
            <a href="{{ route('boards.index') }}">Cancel</a>
        </form>
    </main>

</body>
</html>