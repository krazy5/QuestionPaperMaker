<!DOCTYPE html>
<html><head><title>Manage Subjects</title><link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css"><style>.link-button{background:none;border:none;color:blue;text-decoration:underline;cursor:pointer;padding:0;font-size:inherit;font-family:inherit;}</style></head>
<body><header><h1>Manage Subjects</h1></header><main>
@if (session('success'))<div class="notice"><p>{{ session('success') }}</p></div>@endif
<a href="{{ route('subjects.create') }}"><button>+ Add New Subject</button></a><hr>
<table><thead><tr><th>ID</th><th>Subject Name</th><th>Class</th><th>Actions</th></tr></thead><tbody>
@forelse ($subjects as $subject)
    <tr><td>{{ $subject->id }}</td><td>{{ $subject->name }}</td><td>{{ $subject->academicClass->name }}</td>
        <td><a href="{{ route('subjects.edit', $subject) }}">Edit</a> |
            <form action="{{ route('subjects.destroy', $subject) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="link-button" onclick="return confirm('Are you sure?')">Delete</button>
            </form>
        </td>
    </tr>
@empty
    <tr><td colspan="4">No subjects found.</td></tr>
@endforelse
</tbody></table></main></body></html>