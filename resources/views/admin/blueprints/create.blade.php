<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Paper Blueprint') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.blueprints.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Blueprint Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="e.g., HSC Science - Physics Pattern" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        {{-- ✅ START: ADDED TOTAL MARKS FIELD --}}
                        <div>
                            <label for="total_marks" class="block text-sm font-medium text-gray-700">Total Marks</label>
                            <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks') }}" placeholder="e.g., 80" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        {{-- ✅ END: ADDED TOTAL MARKS FIELD --}}
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="board_id" class="block text-sm font-medium text-gray-700">Board</label>
                                <select name="board_id" id="board_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">-- Select a Board --</option>
                                    @foreach($boards as $board)
                                        <option value="{{ $board->id }}">{{ $board->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="class_id" class="block text-sm font-medium text-gray-700">Class</label>
                                <select name="class_id" id="class_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">-- Select a Class --</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Dynamic Subject Dropdown --}}
                        <div>
                            <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject</label>
                            <select name="subject_id" id="subject_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">-- Select a Class First --</option>
                            </select>
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('admin.blueprints.index') }}" class="mr-4 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save and Add Sections &rarr;</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const classSelect = document.getElementById('class_id');
            const subjectSelect = document.getElementById('subject_id');

            classSelect.addEventListener('change', function () {
                const classId = this.value;
                subjectSelect.innerHTML = '<option value="">Loading...</option>';

                if (classId) {
                    // Use the existing API route to fetch subjects
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
                    subjectSelect.innerHTML = '<option value="">-- Select a Class First --</option>';
                }
            });
        });
    </script>
</x-app-layout>
