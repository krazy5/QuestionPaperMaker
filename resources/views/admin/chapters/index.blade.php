<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Manage Chapters') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900/60 backdrop-blur overflow-hidden shadow-sm sm:rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                        <div class="mb-4 p-4 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Top bar -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <div>
                            <h3 class="text-lg font-semibold">All Chapters</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @if($chapters->total() > 0)
                                    Showing <span class="font-medium">{{ $chapters->firstItem() }}</span>–<span class="font-medium">{{ $chapters->lastItem() }}</span>
                                    of <span class="font-medium">{{ $chapters->total() }}</span>
                                @else
                                    No results
                                @endif
                            </p>
                        </div>

                        <a href="{{ route('admin.chapters.create') }}"
                           class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 shadow-sm transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Add Chapter
                        </a>
                    </div>

                    <!-- Filters -->
                    <form method="GET" action="{{ route('admin.chapters.index') }}" class="mb-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                            <!-- Search -->
                            <div class="lg:col-span-2">
                                <label for="search" class="sr-only">Search</label>
                                <div class="relative">
                                    <input id="search" name="search" type="text" value="{{ request('search') }}"
                                           placeholder="Search chapters, subject, or class…"
                                           class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500">
                                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Class filter -->
                            <div>
                                <label for="class_id" class="sr-only">Class</label>
                                <select id="class_id" name="class_id"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500"
                                        onchange="this.form.submit()">
                                    <option value="">All Classes</option>
                                    @foreach($classes as $c)
                                        <option value="{{ $c->id }}" @selected((string)$c->id === request('class_id'))>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Subject filter (dependent on Class) -->
                            <div>
                                <label for="subject_id" class="sr-only">Subject</label>
                                <select id="subject_id" name="subject_id"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500"
                                        onchange="this.form.submit()">
                                    <option value="">All Subjects</option>
                                    @foreach($subjectsForFilter as $s)
                                        <option value="{{ $s->id }}" @selected((string)$s->id === request('subject_id'))>
                                            {{ $s->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Sort -->
                            <div>
                                @php $sort = request('sort','newest'); @endphp
                                <label for="sort" class="sr-only">Sort</label>
                                <select id="sort" name="sort"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500"
                                        onchange="this.form.submit()">
                                    <option value="newest" @selected($sort==='newest')>Newest first</option>
                                    <option value="name_asc" @selected($sort==='name_asc')>Name A→Z</option>
                                    <option value="name_desc" @selected($sort==='name_desc')>Name Z→A</option>
                                </select>
                            </div>

                            <!-- Per page -->
                            <div>
                                <label for="per_page" class="sr-only">Per page</label>
                                <select id="per_page" name="per_page"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500"
                                        onchange="this.form.submit()">
                                    @foreach([10,15,25,50,100] as $n)
                                        <option value="{{ $n }}" @selected((int)request('per_page',15)===$n)>{{ $n }} / page</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="flex gap-2">
                                <button type="submit"
                                        class="flex-1 inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    Apply
                                </button>
                                <a href="{{ route('admin.chapters.index') }}"
                                   class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-transparent text-gray-600 dark:text-gray-300 hover:underline">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Mobile cards -->
                    <ul class="sm:hidden space-y-3">
                        @forelse($chapters as $chapter)
                            <li class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                                <div class="min-w-0">
                                    <div class="flex items-center justify-between gap-3">
                                        <h4 class="font-semibold truncate">{{ $chapter->name }}</h4>
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                            {{ $chapter->subject->name }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Class: {{ $chapter->subject->academicClass->name }}
                                    </p>
                                    <div class="mt-3 flex items-center gap-4">
                                        <a href="{{ route('admin.chapters.edit', $chapter) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Edit</a>
                                        <form action="{{ route('admin.chapters.destroy', $chapter) }}" method="POST" onsubmit="return confirm('Delete this chapter?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="text-center text-gray-500 dark:text-gray-400">No chapters found.</li>
                        @endforelse
                    </ul>

                    <!-- Desktop table -->
                    <div class="hidden sm:block overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                        <table class="min-w-full bg-white dark:bg-gray-900">
                            <thead class="bg-gray-50 dark:bg-gray-800/60">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Chapter</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Class</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            @forelse ($chapters as $chapter)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $chapter->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $chapter->subject->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $chapter->subject->academicClass->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.chapters.edit', $chapter) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline mr-4">Edit</a>
                                        <form action="{{ route('admin.chapters.destroy', $chapter) }}" method="POST" class="inline" onsubmit="return confirm('Delete this chapter?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No chapters found.
                                        <a href="{{ route('admin.chapters.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline ml-1">Reset filters</a>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-5">
                        {{ $chapters->onEachSide(1)->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
