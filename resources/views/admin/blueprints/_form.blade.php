@if ($errors->any())
    <div class="mb-4 p-4 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ isset($blueprint) ? route('admin.blueprints.update', $blueprint) : route('admin.blueprints.store') }}"
      method="POST" class="space-y-6 text-gray-900 dark:text-gray-100">
    @csrf
    @if (isset($blueprint))
        @method('PUT')
    @endif

    <!-- Name & Total Marks -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Blueprint Name</label>
            <input type="text" name="name" id="name" required
                   value="{{ old('name', isset($blueprint) ? $blueprint->name : '') }}"
                   placeholder="e.g., HSC Science – Physics Pattern"
                   class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500">
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="total_marks" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total Marks</label>
            <input type="number" name="total_marks" id="total_marks" required min="1"
                   value="{{ old('total_marks', isset($blueprint) ? $blueprint->total_marks : '') }}"
                   placeholder="e.g., 80"
                   class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500">
            @error('total_marks') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <!-- Board, Class, Subject -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label for="board_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Board</label>
            <select name="board_id" id="board_id" required
                    class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                <option value="">-- Select Board --</option>
                @foreach ($boards as $board)
                    <option value="{{ $board->id }}"
                        @selected(old('board_id', isset($blueprint) ? $blueprint->board_id : '') == $board->id)>
                        {{ $board->name }}
                    </option>
                @endforeach
            </select>
            @error('board_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Class</label>
            <select name="class_id" id="class_id" required
                    class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                <option value="">-- Select Class --</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}"
                        @selected(old('class_id', isset($blueprint) ? $blueprint->class_id : '') == $class->id)>
                        {{ $class->name }}
                    </option>
                @endforeach
            </select>
            @error('class_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subject</label>
            <select name="subject_id" id="subject_id" required
                    class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                <option value="">-- Select Class First --</option>
            </select>
            @error('subject_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <!-- Chapters (optional) -->
    <div id="chapters-container" class="hidden">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Select Chapters <span class="text-xs text-gray-500 dark:text-gray-400">(Leave blank for full syllabus)</span>
        </label>
        <div id="chapters-list"
             class="mt-2 p-4 rounded-lg border border-gray-200 dark:border-gray-700 max-h-60 overflow-y-auto space-y-2 bg-white dark:bg-gray-900">
            <span class="text-gray-500 dark:text-gray-400">Select a subject to see chapters.</span>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-end pt-4 gap-3">
        <a href="{{ route('admin.blueprints.index') }}"
           class="px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:underline">
            Cancel
        </a>
        <button type="submit"
                class="px-5 py-2.5 rounded-lg bg-blue-600 dark:bg-blue-500 text-white hover:bg-blue-700 dark:hover:bg-blue-600 shadow-sm">
            {{ isset($blueprint) ? 'Update Blueprint' : 'Save and Add Sections →' }}
        </button>
    </div>
</form>

<!-- Dependent selects & chapters -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const classSelect        = document.getElementById('class_id');
    const subjectSelect      = document.getElementById('subject_id');
    const chaptersContainer  = document.getElementById('chapters-container');
    const chaptersList       = document.getElementById('chapters-list');

    const initialSubjectId   = @json(old('subject_id', isset($blueprint) ? $blueprint->subject_id : null));
    const selectedChapterIds = @json(old('selected_chapters', isset($blueprint) ? ($blueprint->selected_chapters ?? []) : []));

    async function fetchSubjects(classId, selectedSubjectId = null) {
        subjectSelect.innerHTML = '<option value="">Loading...</option>';
        chaptersContainer.classList.add('hidden');

        if (!classId) {
            subjectSelect.innerHTML = '<option value="">-- Select Class First --</option>';
            return;
        }

        try {
            const res  = await fetch(`/api/subjects-by-class?class_id=${classId}`);
            const data = await res.json();
            subjectSelect.innerHTML = '<option value="">-- Select Subject --</option>';
            data.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.name;
                subjectSelect.appendChild(opt);
            });
            if (selectedSubjectId) {
                subjectSelect.value = selectedSubjectId;
                fetchChapters(selectedSubjectId, selectedChapterIds);
            }
        } catch (e) {
            subjectSelect.innerHTML = '<option value="">Failed to load subjects</option>';
        }
    }

    async function fetchChapters(subjectId, preSelected = []) {
        chaptersList.innerHTML = '<span class="text-gray-500 dark:text-gray-400">Loading chapters...</span>';
        chaptersContainer.classList.remove('hidden');

        if (!subjectId) {
            chaptersContainer.classList.add('hidden');
            return;
        }

        try {
            const res  = await fetch(`/api/chapters-by-subject?subject_id=${subjectId}`);
            const data = await res.json();
            chaptersList.innerHTML = '';
            if (!data.length) {
                chaptersList.innerHTML = '<span class="text-gray-500 dark:text-gray-400">No chapters found for this subject.</span>';
                return;
            }
            data.forEach(ch => {
                const row = document.createElement('label');
                row.className = 'flex items-center gap-2 text-sm';

                const cb = document.createElement('input');
                cb.type  = 'checkbox';
                cb.name  = 'selected_chapters[]';
                cb.value = ch.id;
                cb.id    = `chapter-${ch.id}`;
                cb.className = 'h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500';

                if (Array.isArray(preSelected) && preSelected.includes(ch.id)) cb.checked = true;

                const span = document.createElement('span');
                span.textContent = ch.name;

                row.appendChild(cb);
                row.appendChild(span);
                chaptersList.appendChild(row);
            });
        } catch (e) {
            chaptersList.innerHTML = '<span class="text-red-600">Failed to load chapters.</span>';
        }
    }

    classSelect.addEventListener('change', () => fetchSubjects(classSelect.value));
    subjectSelect.addEventListener('change', () => fetchChapters(subjectSelect.value, []));

    // Hydrate on load (edit/validation error)
    const initialClassId = classSelect.value;
    if (initialClassId) fetchSubjects(initialClassId, initialSubjectId);
});
</script>
