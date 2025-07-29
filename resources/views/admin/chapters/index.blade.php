<!DOCTYPE html>
<html><head><title>Manage Chapters</title><link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css"><style>.link-button{background:none;border:none;color:blue;text-decoration:underline;cursor:pointer;padding:0;font-size:inherit;font-family:inherit;}</style></head>
<body><header><h1>Manage Chapters</h1></header><main>
@if (session('success'))<div class="notice"><p>{{ session('success') }}</p></div>@endif
<a href="{{ route('chapters.create') }}"><button>+ Add New Chapter</button></a><hr>
<table><thead><tr><th>ID</th><th>Chapter Name</th><th>Subject (Class)</th><th>Actions</th></tr></thead><tbody>
@forelse ($chapters as $chapter)
    <tr><td>{{ $chapter->id }}</td><td>{{ $chapter->name }}</td><td>{{ $chapter->subject->name }} ({{ $chapter->subject->academicClass->name }})</td>
        <td><a href="{{ route('chapters.edit', $chapter) }}">Edit</a> |
            <form action="{{ route('chapters.destroy', $chapter) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="link-button" onclick="return confirm('Are you sure?')">Delete</button>
            </form>
        </td>
    </tr>
@empty
    <tr><td colspan="4">No chapters found.</td></tr>
@endforelse
</tbody></table></main></body></html>