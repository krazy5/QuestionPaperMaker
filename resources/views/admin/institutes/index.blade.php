<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Manage Institutes') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900/60 backdrop-blur overflow-hidden shadow-sm sm:rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <div>
                            <h3 class="text-lg font-semibold">All Institutes</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @if($institutes->total() > 0)
                                    Showing <span class="font-medium">{{ $institutes->firstItem() }}</span>–<span class="font-medium">{{ $institutes->lastItem() }}</span>
                                    of <span class="font-medium">{{ $institutes->total() }}</span>
                                @else
                                    No results
                                @endif
                            </p>
                        </div>

                        <a href="{{ route('admin.institutes.create') }}"
                           class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 shadow-sm transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Add Institute
                        </a>
                    </div>

                    <form method="GET" action="{{ route('admin.institutes.index') }}" class="mb-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-3">
                            <div class="lg:col-span-3">
                                <label for="search" class="sr-only">Search</label>
                                <div class="relative">
                                    <input id="search" name="search" type="text" value="{{ request('search') }}"
                                           placeholder="Search institute, contact, or email…"
                                           class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500">
                                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="active_filter" class="sr-only">Active Subscription</label>
                                @php $af = request('active_filter'); @endphp
                                <select id="active_filter" name="active_filter"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500"
                                        onchange="this.form.submit()">
                                    <option value="" @selected($af===null || $af==='')>All</option>
                                    <option value="with" @selected($af==='with')>Has active subscription</option>
                                    <option value="without" @selected($af==='without')>No active subscription</option>
                                </select>
                            </div>

                            <div>
                                @php $sort = request('sort','newest'); @endphp
                                <label for="sort" class="sr-only">Sort</label>
                                <select id="sort" name="sort"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500"
                                        onchange="this.form.submit()">
                                    <option value="newest" @selected($sort==='newest')>Newest first</option>
                                    <option value="oldest" @selected($sort==='oldest')>Oldest first</option>
                                    <option value="name_asc" @selected($sort==='name_asc')>Name A→Z</option>
                                    <option value="name_desc" @selected($sort==='name_desc')>Name Z→A</option>
                                </select>
                            </div>

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

                            <div class="flex gap-2">
                                <button type="submit"
                                        class="flex-1 inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    Apply
                                </button>
                                <a href="{{ route('admin.institutes.index') }}"
                                   class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-transparent text-gray-600 dark:text-gray-300 hover:underline">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <ul class="sm:hidden space-y-3">
                        @forelse ($institutes as $institute)
                            <li class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                                <div class="min-w-0">
                                    <div class="flex items-center justify-between gap-3">
                                        <h4 class="font-semibold truncate">{{ $institute->institute_name ?? $institute->name }}</h4>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $institute->created_at->format('d M, Y') }}</span>
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 truncate">{{ $institute->email }}</div>
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Contact: {{ $institute->name }}</div>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.institutes.show', $institute) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Manage Subscriptions</a>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="text-center text-gray-500 dark:text-gray-400">No institutes have registered yet.</li>
                        @endforelse
                    </ul>

                    <div class="hidden sm:block overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                        <table class="min-w-full bg-white dark:bg-gray-900">
                            <thead class="bg-gray-50 dark:bg-gray-800/60">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Institute</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Contact</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Registered</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                @forelse ($institutes as $institute)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $institute->institute_name ?? $institute->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $institute->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $institute->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $institute->created_at->format('d M, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.institutes.show', $institute) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Manage Subscriptions</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No institutes have registered yet.
                                            <a href="{{ route('admin.institutes.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline ml-1">Reset filters</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5">
                        {{ $institutes->onEachSide(1)->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
