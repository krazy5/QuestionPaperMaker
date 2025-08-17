@if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form action="{{ isset($blueprint) ? route('institute.blueprints.update', $blueprint) : route('institute.blueprints.store') }}" method="POST" class="space-y-6">
    @csrf
    @if(isset($blueprint))
        @method('PUT')
    @endif

    {{-- Blueprint Name & Total Marks --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
         <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Blueprint Name</label>
            {{-- ✅ CORRECTED VALUE --}}
            <input type="text" name="name" id="name" value="{{ old('name', isset($blueprint) ? $blueprint->name : '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
        <div>
            <label for="total_marks" class="block text-sm font-medium text-gray-700">Total Marks</label>
            {{-- ✅ CORRECTED VALUE --}}
            <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks', isset($blueprint) ? $blueprint->total_marks : '') }}" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
    </div>

    {{-- Board, Class, Subject --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label for="board_id" class="block text-sm font-medium text-gray-700">Board</label>
            <select name="board_id" id="board_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <option value="">-- Select Board --</option>
                @foreach($boards as $board)
                    {{-- ✅ CORRECTED SELECTED LOGIC --}}
                    <option value="{{ $board->id }}" {{ old('board_id', isset($blueprint) ? $blueprint->board_id : '') == $board->id ? 'selected' : '' }}>{{ $board->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="class_id" class="block text-sm font-medium text-gray-700">Class</label>
            <select name="class_id" id="class_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <option value="">-- Select Class --</option>
                 @foreach($classes as $class)
                    {{-- ✅ CORRECTED SELECTED LOGIC --}}
                    <option value="{{ $class->id }}" {{ old('class_id', isset($blueprint) ? $blueprint->class_id : '') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
         <div>
            <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject</label>
            <select name="subject_id" id="subject_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <option value="">-- Select Class First --</option>
            </select>
        </div>
    </div>

    {{-- Chapters Section --}}
    <div id="chapters-container" class="hidden">
        <label class="block text-sm font-medium text-gray-700">Select Chapters (Leave blank for full syllabus)</label>
         <div id="chapters-list" class="mt-2 p-4 border border-gray-200 rounded-md max-h-60 overflow-y-auto space-y-2">
            <span class="text-gray-500">Select a subject to see chapters.</span>
        </div>
    </div>

    <div class="flex justify-end pt-4">
        <a href="{{ route('institute.blueprints.index') }}" class="mr-4 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            {{ isset($blueprint) ? 'Update Blueprint' : 'Save Blueprint' }} &rarr;
        </button>
    </div>
</form>

{{-- The JavaScript remains the same, as it already handles the edit state --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const classSelect = document.getElementById('class_id');
        const subjectSelect = document.getElementById('subject_id');
        const chaptersContainer = document.getElementById('chapters-container');
        const chaptersList = document.getElementById('chapters-list');

        const initialSubjectId = @json(old('subject_id', isset($blueprint) ? $blueprint->subject_id : null));
        const selectedChapterIds = @json(old('selected_chapters', isset($blueprint) ? $blueprint->selected_chapters : []));

        function fetchSubjects(classId, selectedSubjectId = null) {
            subjectSelect.innerHTML = '<option value="">Loading...</option>';
            chaptersContainer.classList.add('hidden');
            if (!classId) {
                subjectSelect.innerHTML = '<option value="">-- Select Class First --</option>';
                return;
            }
            fetch(`/api/subjects-by-class?class_id=${classId}`)
                .then(response => response.json())
                .then(data => {
                    subjectSelect.innerHTML = '<option value="">-- Select Subject --</option>';
                    data.forEach(subject => {
                        const option = document.createElement('option');
                        option.value = subject.id;
                        option.textContent = subject.name;
                        subjectSelect.appendChild(option);
                    });
                    if (selectedSubjectId) {
                        subjectSelect.value = selectedSubjectId;
                        fetchChapters(selectedSubjectId, selectedChapterIds);
                    }
                });
        }
        
        function fetchChapters(subjectId, preSelectedChapters = []) {
            chaptersList.innerHTML = '<span class="text-gray-500">Loading chapters...</span>';
            chaptersContainer.classList.remove('hidden');
            if (!subjectId) {
                chaptersContainer.classList.add('hidden');
                return;
            }
            fetch(`/api/chapters-by-subject?subject_id=${subjectId}`)
                .then(response => response.json())
                .then(data => {
                    chaptersList.innerHTML = '';
                    if (data.length === 0) {
                         chaptersList.innerHTML = '<span class="text-gray-500">No chapters found for this subject.</span>';
                    } else {
                        data.forEach(chapter => {
                            const div = document.createElement('div');
                            div.classList.add('flex', 'items-center');
                            
                            const checkbox = document.createElement('input');
                            checkbox.type = 'checkbox';
                            checkbox.name = 'selected_chapters[]';
                            checkbox.value = chapter.id;
                            checkbox.id = `chapter-${chapter.id}`;
                            checkbox.classList.add('h-4', 'w-4', 'text-blue-600', 'border-gray-300', 'rounded');

                            if (Array.isArray(preSelectedChapters) && preSelectedChapters.includes(chapter.id)) {
                                checkbox.checked = true;
                            }

                            const label = document.createElement('label');
                            label.htmlFor = `chapter-${chapter.id}`;
                            label.textContent = chapter.name;
                            label.classList.add('ml-2', 'block', 'text-sm', 'text-gray-900');

                            div.appendChild(checkbox);
                            div.appendChild(label);
                            chaptersList.appendChild(div);
                        });
                    }
                });
        }

        classSelect.addEventListener('change', () => fetchSubjects(classSelect.value));
        subjectSelect.addEventListener('change', () => fetchChapters(subjectSelect.value, []));

        const initialClassId = classSelect.value;
        if (initialClassId) {
            fetchSubjects(initialClassId, initialSubjectId);
        }
    });
</script>