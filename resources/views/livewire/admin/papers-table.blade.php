<div class="space-y-4">
    {{-- Filters --}}
    <div class="bg-white p-4 rounded-md shadow-sm border">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div class="md:col-span-2">
                <input type="text"
                       placeholder="Search paper title..."
                       class="w-full rounded-md border-gray-300"
                       wire:model.live.debounce.300ms="q">
            </div>

            <div>
                <select class="w-full rounded-md border-gray-300" wire:model.live="board_id">
                    <option value="">All Boards</option>
                    @foreach($boards as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select class="w-full rounded-md border-gray-300" wire:model.live="class_id">
                    <option value="">All Classes</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select class="w-full rounded-md border-gray-300" wire:model.live="subject_id" @disabled(!$class_id)>
                    <option value="">
                        @if(!$class_id) Select class first @else All Subjects @endif
                    </option>
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-3 text-right">
            <button wire:click="clearFilters" class="text-sm text-gray-600 hover:underline">
                Clear filters
            </button>
        </div>
    </div>

    {{-- Results --}}
    <div class="overflow-x-auto bg-white rounded-md shadow-sm border">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class & Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($papers as $paper)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $paper->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $paper->subject->academicClass->name ?? '' }} - {{ $paper->subject->name ?? '' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($paper->status) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">

                            @if(Route::has('admin.papers.preview'))
                                <a href="{{ route('admin.papers.preview', $paper) }}" class="text-indigo-600 hover:text-indigo-900">Preview</a>
                            @elseif(Route::has('institute.papers.preview'))
                                <a href="{{ route('institute.papers.preview', $paper) }}" class="text-indigo-600 hover:text-indigo-900">Preview</a>
                            @endif

                            @if(Route::has('admin.papers.answers'))
                                <a href="{{ route('admin.papers.answers', $paper) }}" class="text-green-600 hover:text-green-900">Answer Key</a>
                            @elseif(Route::has('institute.papers.answers'))
                                <a href="{{ route('institute.papers.answers', $paper) }}" class="text-green-600 hover:text-green-900">Answer Key</a>
                            @endif

                            @if(Route::has('admin.papers.edit'))
                                <a href="{{ route('admin.papers.edit', $paper) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            @elseif(Route::has('institute.papers.edit'))
                                <a href="{{ route('institute.papers.edit', $paper) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            @endif

                            <button
                                wire:click="deletePaper({{ $paper->id }})"
                                onclick="return confirm('Delete this paper?')"
                                class="text-red-600 hover:text-red-900">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            No papers found.
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
