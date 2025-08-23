{{-- resources/views/institute/papers/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                Create New Paper
            </h2>
            <a href="{{ route('institute.papers.index') }}"
               class="text-sm px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                &larr; Back to Papers
            </a>
        </div>
    </x-slot>

    <div class="relative min-h-screen overflow-visible"
         x-data="paperForm({
            initialClass: '{{ old('class_id') }}',
            initialSubject: '{{ old('subject_id') }}',
            endpoint: '{{ url('/institute/get-subjects-for-class') }}'
         })"
         x-init="init()">

        <div class="py-6 sm:py-10 pb-48">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                @if ($errors->any())
                    <div class="mb-6 rounded-lg border border-red-200 dark:border-red-900/50 bg-red-50 dark:bg-red-900/30 p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-red-500 mt-0.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16Zm-.75-11.5a.75.75 0 011.5 0v4a.75.75 0 01-1.5 0v-4Zm.75 8a1 1 0 100-2 1 1 0 000 2Z" clip-rule="evenodd"/>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-red-800 dark:text-red-200">There were errors with your submission:</h3>
                                <ul class="mt-2 text-sm text-red-700 dark:text-red-200/90 list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="bg-white dark:bg-gray-900/60 backdrop-blur overflow-visible shadow-sm sm:rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Paper Details</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Fill in the basic information for this paper.</p>
                    </div>

                    <form id="create-paper-form" action="{{ route('institute.papers.store') }}" method="POST" class="px-6 py-6 space-y-8">
                        @csrf

                        {{-- Title --}}
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Paper Title</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder='e.g. "Midterm Examination – Physics (Sem II)"'>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Example: “Midterm Examination – Physics (Sem II)”</p>
                        </div>

                        {{-- Board / Class / Subject --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="board_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Board</label>
                                <select name="board_id" id="board_id" required
                                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500">
                                    @foreach($boards as $board)
                                        <option value="{{ $board->id }}" @selected(old('board_id') == $board->id)>{{ $board->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Class</label>
                                <select name="class_id" id="class_id" required
                                        x-model="selectedClass" @change="fetchSubjects()"
                                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Select a Class --</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subject</label>
                                <div class="relative">
                                    <select name="subject_id" id="subject_id" required
                                            x-model="selectedSubject" :disabled="!selectedClass || loading"
                                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 disabled:bg-gray-100 dark:disabled:bg-gray-800/60 disabled:text-gray-500">
                                        <template x-if="loading"><option value="">-- Loading subjects... --</option></template>
                                        <template x-if="error && !loading"><option value="">-- Error loading subjects --</option></template>
                                        <template x-if="!loading && !error && subjects.length === 0 && selectedClass"><option value="">-- No subjects found --</option></template>
                                        <template x-if="!loading && !error && (!selectedClass || subjects.length === 0)"><option value="">-- Select a class first --</option></template>
                                        <template x-for="s in subjects" :key="s.id">
                                            <option :value="s.id" x-text="s.name"></option>
                                        </template>
                                    </select>
                                    <div x-show="loading" x-transition
                                         class="absolute left-0 right-0 -bottom-1 h-0.5 bg-indigo-100 dark:bg-indigo-900/30 overflow-hidden rounded">
                                        <div class="h-full w-1/3 bg-indigo-500 dark:bg-indigo-400 animate-pulse"></div>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Choose class first to load available subjects.</p>
                            </div>
                        </div>

                        {{-- Time / Date / Marks --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="time_allowed" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Time Allowed</label>
                                <input type="text" name="time_allowed" id="time_allowed" value="{{ old('time_allowed', '3 Hrs') }}" required
                                       class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="e.g. 3 Hrs, 90 Mins">
                            </div>

                            <div>
                                <label for="exam_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Exam Date</label>
                                <input type="date" name="exam_date" id="exam_date" value="{{ old('exam_date', now()->format('Y-m-d')) }}"
                                       class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label for="total_marks" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total Marks</label>
                                <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks', 100) }}" required min="1"
                                       class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        {{-- Instructions --}}
                        <div>
                            <label for="instructions" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Instructions (Optional)</label>
                            <textarea name="instructions" id="instructions" rows="5"
                                      class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Shown on the paper header (e.g., ‘Attempt all questions’, ‘Use a non-programmable calculator’).">{{ old('instructions') }}</textarea>
                        </div>

                        {{-- Desktop/Tablet Actions --}}
                        <div class="hidden sm:flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                            <a href="{{ route('institute.papers.index') }}"
                               class="inline-flex justify-center px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="inline-flex justify-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Save &amp; Select Questions &rarr;
                            </button>
                        </div>

                        {{-- Mobile-only fallback submit inside the form --}}
                        <div class="sm:hidden">
                            <button type="submit"
                                    class="w-full mt-2 inline-flex justify-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Save &amp; Select Questions &rarr;
                            </button>
                        </div>
                    </form>

                    <div class="sm:hidden h-24"></div>
                </div>
            </div>
        </div>

        {{-- Sticky mobile action bar (safe-area + high z-index) --}}
        <div
            class="sm:hidden fixed inset-x-0 bottom-0 z-[1000] bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 px-4 py-3 shadow-2xl"
            style="padding-bottom: calc(env(safe-area-inset-bottom) + 12px);">
            <div class="max-w-4xl mx-auto flex items-center justify-between gap-3">
                <a href="{{ route('institute.papers.index') }}"
                   class="flex-1 inline-flex justify-center px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                    Cancel
                </a>
                <button
                    form="create-paper-form"
                    type="submit"
                    class="flex-1 inline-flex justify-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Save &amp; Select Questions &rarr;
                </button>
            </div>
        </div>
    </div>

    <script>
        function paperForm({ initialClass, initialSubject, endpoint }) {
            return {
                endpoint,
                subjects: [],
                selectedClass: initialClass || '',
                selectedSubject: initialSubject || '',
                loading: false,
                error: false,
                async init() {
                    if (this.selectedClass) {
                        await this.fetchSubjects();
                        if (this.selectedSubject && !this.subjects.find(s => String(s.id) === String(this.selectedSubject))) {
                            this.selectedSubject = '';
                        }
                    }
                },
                async fetchSubjects() {
                    this.loading = true; this.error = false;
                    this.subjects = []; this.selectedSubject = '';
                    if (!this.selectedClass) { this.loading = false; return; }
                    try {
                        const res = await fetch(`${this.endpoint}/${this.selectedClass}`, { headers: { 'Accept': 'application/json' } });
                        if (!res.ok) throw new Error('Network response was not ok');
                        const data = await res.json();
                        this.subjects = Array.isArray(data) ? data : [];
                    } catch (e) { this.error = true; } finally { this.loading = false; }
                }
            }
        }
    </script>
</x-app-layout>
