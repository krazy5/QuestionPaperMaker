<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    {{ __('Manage Classes') }}
                </h2>
                @if(method_exists($classes, 'total'))
                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/10
                                 dark:bg-blue-900/30 dark:text-blue-200 dark:ring-blue-400/20">
                        {{ number_format($classes->total()) }} total
                    </span>
                @endif
            </div>

            <a href="{{ route('admin.classes.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm
                      hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                {{-- Plus icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/>
                </svg>
                Add New Class
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-gradient-to-b from-white to-slate-50 dark:from-gray-900 dark:to-gray-950
                        border border-gray-200 dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-xl">
                <div class="p-6 sm:p-8">

                    {{-- Flash --}}
                    @if (session('success'))
                        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800
                                    dark:border-green-900/40 dark:bg-green-900/20 dark:text-green-200">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800
                                    dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-200">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Toolbar --}}
                    <form method="GET" action="{{ route('admin.classes.index') }}" class="mb-5">
                        <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                            <div class="w-full md:max-w-md relative">
                                <input type="text" name="q" placeholder="Search classes…" value="{{ request('q') }}"
                                    class="w-full rounded-lg border border-gray-300 bg-white/80 px-3 py-2 pl-9 text-sm text-gray-900 shadow-sm
                                           placeholder:text-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                                           dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100 dark:placeholder:text-gray-500">
                                <span class="pointer-events-none absolute left-3 top-2.5 text-gray-400 dark:text-gray-500" aria-hidden="true">
                                    {{-- Magnifying glass --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"/>
                                    </svg>
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                {{-- Sort (controller can read ?sort=name_asc|name_desc) --}}
                                <label class="sr-only" for="sort">Sort</label>
                                <select id="sort" name="sort"
                                        class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm
                                               focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                                               dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                                    <option value="">Sort: Default</option>
                                    <option value="name_asc"  @selected(request('sort')==='name_asc')>Name A → Z</option>
                                    <option value="name_desc" @selected(request('sort')==='name_desc')>Name Z → A</option>
                                </select>

                                {{-- Per-page (controller can read ?per=10|25|50) --}}
                                <label class="sr-only" for="per">Per page</label>
                                <select id="per" name="per"
                                        class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm
                                               focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                                               dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                                    @php $per = (int) request('per', 10); @endphp
                                    <option value="10"  @selected($per===10)>10</option>
                                    <option value="25"  @selected($per===25)>25</option>
                                    <option value="50"  @selected($per===50)>50</option>
                                    <option value="100" @selected($per===100)>100</option>
                                </select>

                                <a href="{{ route('admin.classes.index') }}"
                                   class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm
                                          hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600
                                          dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                                    Reset
                                </a>
                                <button type="submit"
                                        class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm
                                               hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                    Apply
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Desktop table --}}
                    <div class="hidden sm:block overflow-x-auto rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                            <thead class="bg-gray-50 text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-800 dark:bg-gray-900">
                                @forelse ($classes as $class)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60">
                                        <td class="whitespace-nowrap px-6 py-3 text-sm text-gray-600 dark:text-gray-300">#{{ $class->id }}</td>
                                        <td class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $class->name }}
                                        </td>
                                        <td class="px-6 py-3 text-sm">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <a href="{{ route('admin.classes.edit', $class) }}"
                                                   class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-gray-700 shadow-sm
                                                          hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600
                                                          dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                                                    {{-- Pencil icon --}}
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.1 2.1 0 0 1 2.97 2.97L7.5 18.79l-4 1 1-4 12.362-12.303z"/>
                                                    </svg>
                                                    <span>Edit</span>
                                                </a>

                                                <form action="{{ route('admin.classes.destroy', $class) }}" method="POST"
                                                      onsubmit="return confirm('Delete this class?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1 rounded-lg bg-red-600 px-3 py-1.5 font-medium text-white shadow-sm
                                                                   hover:bg-red-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                                                        {{-- Trash icon --}}
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12m-9 0V5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2m-7 0h10m-9 0v12a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2V7"/>
                                                        </svg>
                                                        <span>Delete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No classes found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile cards --}}
                    <div class="sm:hidden space-y-3">
                        @forelse($classes as $class)
                            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm
                                        dark:border-gray-800 dark:bg-gray-900">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">ID: #{{ $class->id }}</div>
                                        <div class="mt-0.5 text-base font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $class->name }}
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.classes.edit', $class) }}"
                                           class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-gray-700 shadow-sm
                                                  hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600
                                                  dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                                            {{-- Pencil --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.1 2.1 0 0 1 2.97 2.97L7.5 18.79l-4 1 1-4 12.362-12.303z"/>
                                            </svg>
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.classes.destroy', $class) }}" method="POST"
                                              onsubmit="return confirm('Delete this class?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 rounded-lg bg-red-600 px-3 py-1.5 font-medium text-white shadow-sm
                                                           hover:bg-red-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                                                {{-- Trash --}}
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12m-9 0V5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2m-7 0h10m-9 0v12a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2V7"/>
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center
                                        dark:border-gray-700 dark:bg-gray-900">
                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full
                                            bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300">
                                    {{-- Inbox icon --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-7h14l2 7m-2 7H5l-2-7h18l-2 7z"/>
                                    </svg>
                                </div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">No classes</h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Get started by creating your first class.</p>
                                <div class="mt-4">
                                    <a href="{{ route('admin.classes.create') }}"
                                       class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm
                                              hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                        {{-- Plus --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/>
                                        </svg>
                                        Add Class
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    @if(method_exists($classes, 'links'))
                        <div class="mt-6">
                            {{ $classes->appends(request()->query())->onEachSide(1)->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
