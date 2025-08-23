<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Edit Chapter') }}: <span class="font-normal">{{ $chapter->name }}</span>
        </h2>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900/60 backdrop-blur shadow-sm sm:rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="p-6 sm:p-8">

                    <form action="{{ route('admin.chapters.update', $chapter) }}" method="POST" class="space-y-6">
                        @csrf @method('PUT')

                        <!-- Class (drives Subject list) -->
                        <div>
                            <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Class
                            </label>
                            <select id="class_id" required
                                    class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                                @php $currentClassId = $chapter->subject->class_id ?? $chapter->subject->academicClass->id ?? null; @endphp
                                <option value="" disabled {{ $currentClassId ? '' : 'selected' }}>-- Select a Class --</option>
                                @foreach ($classes as $c)
                                    <option value="{{ $c->id }}" @selected($currentClassId == $c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Subject (depends on Class) -->
                        <div>
                            <label for="subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Subject
                            </label>
                            <select id="subject_id" name="subject_id" required
                                    class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                                @foreach ($subjects as $s)
                                    <option value="{{ $s->id }}" @selected(old('subject_id', $chapter->subject_id) == $s->id)>{{ $s->name }}</option>
                                @endforeach
                            </select>
                            @error('subject_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Chapter Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Chapter Name
                            </label>
                            <input  id="name" name="name" type="text" required
                                    value="{{ old('name', $chapter->name) }}"
                                    class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500">
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.chapters.index') }}"
                               class="inline-flex items-center px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:underline">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-5 py-2.5 rounded-lg bg-blue-600 dark:bg-blue-500 text-white hover:bg-blue-700 dark:hover:bg-blue-600 shadow-sm">
                                Update Chapter
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Dependent select logic -->
    <script>
        const classSelect   = document.getElementById('class_id');
        const subjectSelect = document.getElementById('subject_id');

        async function loadSubjects(classId, selectedId = null) {
            subjectSelect.innerHTML = '<option value="">Loading...</option>';
            if (!classId) { subjectSelect.innerHTML = '<option value="">-- Select a Class Above --</option>'; return; }
            try {
                const res  = await fetch(`/api/subjects-by-class?class_id=${classId}`);
                const data = await res.json();
                subjectSelect.innerHTML = '';
                data.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.id;
                    opt.textContent = s.name;
                    if (selectedId && String(selectedId) === String(s.id)) opt.selected = true;
                    subjectSelect.appendChild(opt);
                });
            } catch(e) {
                subjectSelect.innerHTML = '<option value="">Failed to load subjects</option>';
            }
        }

        classSelect?.addEventListener('change', function () {
            loadSubjects(this.value, null);
        });

        // If the class changes from original or if the initial subjects don't match, ensure consistency.
        // (No-op if you already rendered the correct list server-side.)
    </script>
</x-app-layout>
