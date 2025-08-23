<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                {{ __('Manage Questions') }}
            </h2>

            <a href="{{ route('admin.questions.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm
                      hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                <!-- Plus icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/>
                </svg>
                Add New Question
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-b from-white to-slate-50 dark:from-gray-900 dark:to-gray-950 border border-gray-200 dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-xl">
                <div class="p-6 sm:p-8">

                    {{-- Flash --}}
                    @if (session('success'))
                        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800 dark:border-green-900/40 dark:bg-green-900/20 dark:text-green-200">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-200">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Filters / Search --}}
                    <form method="GET" action="{{ route('admin.questions.index') }}" class="mb-6">
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-6">
                            {{-- Search --}}
                            <div class="md:col-span-2">
                                <label class="sr-only" for="q">Search</label>
                                <div class="relative">
                                    <input id="q" name="q" type="text" value="{{ request('q') }}" placeholder="Search question text…"
                                           class="w-full rounded-lg border border-gray-300 bg-white/80 px-3 py-2 pl-9 text-sm text-gray-900 shadow-sm
                                                  placeholder:text-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                                                  dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100 dark:placeholder:text-gray-500">
                                    <span class="pointer-events-none absolute left-3 top-2.5 text-gray-400 dark:text-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"/></svg>
                                    </span>
                                </div>
                            </div>

                            {{-- Board --}}
                            <div>
                                <label for="filter_board_id" class="sr-only">Board</label>
                                <select id="filter_board_id" name="board_id" class="w-full rounded-lg border border-gray-300 bg-white/80 px-2 py-2 text-sm
                                        dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
                                    <option value="">All Boards</option>
                                    @foreach($boards as $b)
                                        <option value="{{ $b->id }}" @selected(request('board_id') == $b->id)>{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Class --}}
                            <div>
                                <label for="filter_class_id" class="sr-only">Class</label>
                                <select id="filter_class_id" name="class_id" class="w-full rounded-lg border border-gray-300 bg-white/80 px-2 py-2 text-sm
                                        dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
                                    <option value="">All Classes</option>
                                    @foreach($classes as $c)
                                        <option value="{{ $c->id }}" @selected(request('class_id') == $c->id)>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Subject (dependent) --}}
                            <div>
                                <label for="filter_subject_id" class="sr-only">Subject</label>
                                <select id="filter_subject_id" name="subject_id" class="w-full rounded-lg border border-gray-300 bg-white/80 px-2 py-2 text-sm
                                        dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100" {{ request('class_id') ? '' : 'disabled' }}>
                                    <option value="">
                                        @if(!request('class_id')) Select class first @else All Subjects @endif
                                    </option>
                                </select>
                            </div>

                            {{-- Type --}}
                            <div>
                                <label for="filter_type" class="sr-only">Type</label>
                                <select id="filter_type" name="question_type" class="w-full rounded-lg border border-gray-300 bg-white/80 px-2 py-2 text-sm
                                        dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
                                    <option value="">All Types</option>
                                    <option value="mcq" @selected(request('question_type')==='mcq')>MCQ</option>
                                    <option value="short" @selected(request('question_type')==='short')>Short</option>
                                    <option value="long" @selected(request('question_type')==='long')>Long</option>
                                    <option value="true_false" @selected(request('question_type')==='true_false')>True/False</option>
                                </select>
                            </div>

                            {{-- Difficulty --}}
                            <div>
                                <label for="filter_difficulty" class="sr-only">Difficulty</label>
                                <select id="filter_difficulty" name="difficulty" class="w-full rounded-lg border border-gray-300 bg-white/80 px-2 py-2 text-sm
                                        dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100">
                                    <option value="">All Difficulties</option>
                                    <option value="easy" @selected(request('difficulty')==='easy')>Easy</option>
                                    <option value="medium" @selected(request('difficulty')==='medium')>Medium</option>
                                    <option value="hard" @selected(request('difficulty')==='hard')>Hard</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end">
                            <a href="{{ route('admin.questions.index') }}"
                               class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm
                                      hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600
                                      dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                                Reset
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm
                                           hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                Apply Filters
                            </button>
                        </div>
                    </form>

                    {{-- Table (md+) --}}
                    <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                            <thead class="bg-gray-50 text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Question</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Board / Class / Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Difficulty</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-800 dark:bg-gray-900">
                                @forelse ($questions as $q)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60">
                                        <td class="px-6 py-3 text-sm text-gray-900 dark:text-gray-100">{{ \Illuminate\Support\Str::limit($q->question_text, 100) }}</td>
                                        <td class="px-6 py-3 text-sm text-gray-700 dark:text-gray-300">
                                            <div class="flex flex-col">
                                                <span>{{ $q->board->name ?? '—' }}</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $q->first_class?->name ?? '—' }} • {{ $q->subject?->name ?? '—' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-3">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                {{ match($q->question_type){
                                                    'mcq' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                                    'short' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                                                    'long' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                                                    'true_false' => 'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
                                                    default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
                                                } }}">
                                                {{ strtoupper(str_replace('_','/',$q->question_type)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                {{ match($q->difficulty){
                                                    'easy' => 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                                                    'medium' => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                                                    'hard' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                                    default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
                                                } }}">
                                                {{ ucfirst($q->difficulty ?? '—') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-sm">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <a href="{{ route('admin.questions.edit', $q) }}"
                                                   class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-gray-700 shadow-sm
                                                          hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600
                                                          dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.1 2.1 0 0 1 2.97 2.97L7.5 18.79l-4 1 1-4 12.362-12.303z"/></svg>
                                                    <span>Edit</span>
                                                </a>
                                                <form action="{{ route('admin.questions.destroy', $q) }}" method="POST" onsubmit="return confirm('Delete this question?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1 rounded-lg bg-red-600 px-3 py-1.5 font-medium text-white shadow-sm
                                                                   hover:bg-red-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12m-9 0V5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2m-7 0h10m-9 0v12a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2V7"/></svg>
                                                        <span>Delete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">No questions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Cards (mobile) --}}
                    <div class="md:hidden space-y-3">
                        @forelse ($questions as $q)
                            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ \Illuminate\Support\Str::limit($q->question_text, 140) }}</div>
                                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $q->board->name ?? '—' }} • {{ $q->first_class?->name ?? '—' }} • {{ $q->subject?->name ?? '—' }}
                                </div>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium border border-gray-200 text-gray-700 dark:border-gray-700 dark:text-gray-300">
                                        {{ strtoupper(str_replace('_','/',$q->question_type)) }}
                                    </span>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium border border-gray-200 text-gray-700 dark:border-gray-700 dark:text-gray-300">
                                        {{ ucfirst($q->difficulty ?? '—') }}
                                    </span>
                                </div>
                                <div class="mt-3 flex gap-2">
                                    <a href="{{ route('admin.questions.edit', $q) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-700 shadow-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.questions.destroy', $q) }}" method="POST" onsubmit="return confirm('Delete this question?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-sm text-gray-500 dark:text-gray-400">No questions found.</div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $questions->onEachSide(1)->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Dependent Subject filter --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const classSelect = document.getElementById('filter_class_id');
            const subjectSelect = document.getElementById('filter_subject_id');
            const initialClassId = @json(request('class_id'));
            const initialSubjectId = @json(request('subject_id'));

            async function loadSubjects(classId, selectedId = null) {
                subjectSelect.innerHTML = '<option value="">Loading…</option>';
                subjectSelect.disabled = true;
                try {
                    const res = await fetch(`/api/subjects-by-class?class_id=${classId}`);
                    const data = await res.json();
                    subjectSelect.innerHTML = '<option value="">All Subjects</option>';
                    data.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.id;
                        opt.textContent = s.name;
                        if (selectedId && String(selectedId) === String(s.id)) opt.selected = true;
                        subjectSelect.appendChild(opt);
                    });
                    subjectSelect.disabled = false;
                } catch (e) {
                    subjectSelect.innerHTML = '<option value="">All Subjects</option>';
                    subjectSelect.disabled = false;
                }
            }

            classSelect?.addEventListener('change', () => {
                const cid = classSelect.value;
                if (!cid) {
                    subjectSelect.innerHTML = '<option value="">Select class first</option>';
                    subjectSelect.disabled = true;
                    return;
                }
                loadSubjects(cid);
            });

            if (initialClassId) {
                loadSubjects(initialClassId, initialSubjectId);
            }
        });
    </script>
</x-app-layout>