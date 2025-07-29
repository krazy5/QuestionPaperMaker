<!DOCTYPE html>
<html>
<head>
    <title>Manage Classes</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <style>.link-button{background:none;border:none;color:blue;text-decoration:underline;cursor:pointer;padding:0;font-size:inherit;font-family:inherit;}</style>
</head>
<body>
    <header><h1>Manage Classes</h1></header>
    <main>
        @if (session('success'))
            <div class="notice"><p>{{ session('success') }}</p></div>
        @endif
        <a href="{{ route('classes.create') }}"><button>+ Add New Class</button></a>
        <hr>
        <table>
            <thead><tr><th>ID</th><th>Name</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse ($classes as $class)
                    <tr>
                        <td>{{ $class->id }}</td>
                        <td>{{ $class->name }}</td>
                        <td>
                            <a href="{{ route('classes.edit', $class) }}">Edit</a> |
                            <form action="{{ route('classes.destroy', $class) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="link-button" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3">No classes found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </main>
</body>
</html>