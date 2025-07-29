<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Boards</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
<style>
    .link-button {
        background: none;
        border: none;
        color: blue;
        text-decoration: underline;
        cursor: pointer;
        padding: 0;
        font-size: inherit;
        font-family: inherit;
    }
</style>

</head>
<body>
    <header>
        <h1>Manage Boards</h1>
        <p>Here you can view, add, edit, or delete academic boards.</p>
    </header>

    <main>

         @if (session('success'))
        <div class="notice">
            <p>{{ session('success') }}</p>
        </div>
        @endif

     
        <a href="{{ route('boards.create') }}">
            <button>+ Add New Board</button>
        </a>

        <hr>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($boards as $board)
                    <tr>
                        <td>{{ $board->id }}</td>
                        <td>{{ $board->name }}</td>
                        <td>
                            <a href="{{ route('boards.edit', $board) }}">Edit</a> |
                           <form action="{{ route('boards.destroy', $board) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="link-button" onclick="return confirm('Are you sure you want to delete this board?')">
                                Delete
                            </button>
                        </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No boards found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>

</body>
</html>