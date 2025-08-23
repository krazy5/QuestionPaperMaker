<div class="space-y-4">
    {{-- Filters --}}
    <div class="bg-white p-4 rounded-md shadow-sm border">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div class="md:col-span-2">
                <input type="text"
                       placeholder="Search blueprint name..."
                       class="w-full rounded-md border-gray-300"
                       wire:model.live.debounce.300ms="q">
            </div>

            <div>
                <select class="w-full rounded-md border-gray-300"
                        wire:model.live="board_id">
                    <option value="">All Boards</option>
                    @foreach($boards as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select class="w-full rounded-md border-gray-300"
                        wire:model.live="class_id">
                    <option value="">All Classes</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select class="w-full rounded-md border-gray-300"
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

        <div class="mt-3 text-right">
            <button wire:click="clearFilters" class="text-sm text-gray-600 hover:underline">
                Clear filters
            </button>
        </div>
    </div>

    {{-- Results --}}
    <div class="overflow-x-auto bg-white rounded-md shadow-sm border">
        <table class="min-w-full">
            <thead class="bg-gray-50 text-xs text-gray-600 uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">Name</th>
                    <th class="px-6 py-3 text-left">Board / Class / Subject</th>
                    <th class="px-6 py-3 text-left">Total Marks</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($blueprints as $bp)
                    <tr>
                        <td class="px-6 py-3">{{ $bp->name }}</td>
                        <td class="px-6 py-3">
                            {{ $bp->board->name ?? '—' }} /
                            {{ $bp->academicClass->name ?? '—' }} /
                            {{ $bp->subject->name ?? '—' }}
                        </td>
                        <td class="px-6 py-3">{{ $bp->total_marks }}</td>
                        <td class="px-6 py-3 space-x-3">
                            <a href="{{ route('admin.blueprints.show', $bp) }}" class="text-indigo-600 hover:text-indigo-900">View</a>

                            @if(Route::has('admin.blueprints.edit'))
                                <a href="{{ route('admin.blueprints.edit', $bp) }}" class="text-green-600 hover:text-green-900">Edit</a>
                            @endif

                            <button
                                wire:click="deleteBlueprint({{ $bp->id }})"
                                onclick="return confirm('Delete this blueprint?')"
                                class="text-red-600 hover:text-red-900">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-6 py-6 text-center text-gray-500" colspan="4">
                            No blueprints found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-3">
            {{ $blueprints->onEachSide(1)->links() }}
        </div>
    </div>
</div>
