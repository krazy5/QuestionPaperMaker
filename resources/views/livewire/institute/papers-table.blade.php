<div class="space-y-4">
    {{-- Filters (frameless) --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
        <div class="md:col-span-2">
            <label for="p_q" class="sr-only">Search papers</label>
            <div class="relative">
                <input id="p_q" type="text" placeholder="Search paper title…"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100
                              placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500"
                       wire:model.live.debounce.300ms="q">
                <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
                </div>
            </div>
        </div>

        <div>
            <label for="p_board" class="sr-only">Board</label>
            <select id="p_board"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100
                           focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500"
                    wire:model.live="board_id">
                <option value="">All Boards</option>
                @foreach($boards as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="p_class" class="sr-only">Class</label>
            <select id="p_class"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100
                           focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500"
                    wire:model.live="class_id">
                <option value="">All Classes</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="p_subject" class="sr-only">Subject</label>
            <select id="p_subject"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100
                           focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500 disabled:opacity-60 disabled:cursor-not-allowed"
                    wire:model.live="subject_id" @disabled(!$class_id)>
                <option value="">
                    @if(!$class_id) Select class first @else All Subjects @endif
                </option>
                @foreach($subjects as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="text-right -mt-1">
        <button wire:click="clearFilters" class="text-sm text-gray-600 dark:text-gray-300 hover:underline">
            Clear filters
        </button>
    </div>

    {{-- Mobile cards --}}
    <ul class="sm:hidden space-y-3">
        @forelse ($papers as $paper)
            <li class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
                <div class="min-w-0">
                    <div class="flex items-start justify-between gap-3">
                        <div class="font-medium truncate">{{ $paper->title }}</div>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                            {{ ucfirst($paper->status) }}
                        </span>
                    </div>
                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ $paper->subject->academicClass->name ?? '' }} – {{ $paper->subject->name ?? '' }}
                    </div>
                </div>
                <div class="mt-3 flex flex-wrap gap-3">
                    <a href="{{ route('institute.papers.preview', $paper) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Preview</a>
                    <a href="{{ route('institute.papers.answers', $paper) }}" class="text-emerald-600 hover:underline">Answer Key</a>
                    <a href="{{ route('institute.papers.edit', $paper) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Edit</a>
                    <button wire:click="deletePaper({{ $paper->id }})"
                            onclick="return confirm('Delete this paper?')"
                            class="text-red-600 hover:underline">Delete</button>
                </div>
            </li>
        @empty
            <li class="text-center text-gray-500 dark:text-gray-400">You have not created any papers yet.</li>
        @endforelse
    </ul>

    {{-- Desktop table --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="min-w-full bg-white dark:bg-gray-900 rounded-xl overflow-hidden">
            <thead class="bg-gray-50 dark:bg-gray-800/60">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Class & Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($papers as $paper)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $paper->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $paper->subject->academicClass->name ?? '' }} – {{ $paper->subject->name ?? '' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                {{ ucfirst($paper->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex flex-wrap items-center gap-4">
                                <a href="{{ route('institute.papers.preview', $paper) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Preview</a>
                                <a href="{{ route('institute.papers.answers', $paper) }}" class="text-emerald-600 hover:underline">Answer Key</a>
                                <a href="{{ route('institute.papers.edit', $paper) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Edit</a>
                                <button wire:click="deletePaper({{ $paper->id }})"
                                        onclick="return confirm('Delete this paper?')"
                                        class="text-red-600 hover:underline">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            You have not created any papers yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-3">
            {{ $papers->onEachSide(1)->links() }}
        </div>
    </div>
</div>
