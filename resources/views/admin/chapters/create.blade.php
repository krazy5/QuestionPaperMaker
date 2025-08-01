<!DOCTYPE html>
<html>
<head>
    <title>Add New Chapter</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
<header><h1>Add New Chapter</h1></header>
<main>
    <form action="{{ route('admin.chapters.store') }}" method="POST">
        @csrf

        <div>
            <label for="class_id_filter">Filter by Class</label>
            <select id="class_id_filter">
                <option value="">-- Select a Class First --</option>
                @foreach (\App\Models\AcademicClassModel::all() as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="subject_id">Subject</label>
            <select name="subject_id" id="subject_id" required>
                <option value="">-- Select a Class Above --</option>
            </select>
        </div>

        <div>
            <label for="name">Chapter Name</label>
            <input type="text" name="name" id="name" required>
        </div>
        
        <button type="submit">Save Chapter</button>
        <a href="{{ route('admin.chapters.index') }}">Cancel</a>
    </form>
</main>

<script>
    document.getElementById('class_id_filter').addEventListener('change', function () {
        const classId = this.value;
        const subjectSelect = document.getElementById('subject_id');
        
        // Clear previous options
        subjectSelect.innerHTML = '<option value="">Loading...</option>';

        if (classId) {
            // Fetch subjects from our new API route
            fetch(`/api/subjects-by-class?class_id=${classId}`)
                .then(response => response.json())
                .then(data => {
                    subjectSelect.innerHTML = '<option value="">-- Select a Subject --</option>';
                    data.forEach(subject => {
                        const option = document.createElement('option');
                        option.value = subject.id;
                        option.textContent = subject.name;
                        subjectSelect.appendChild(option);
                    });
                });
        } else {
            subjectSelect.innerHTML = '<option value="">-- Select a Class Above --</option>';
        }
    });
</script>
</body>
</html>