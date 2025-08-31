<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            Contact Requests
        </h2>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900/60 backdrop-blur overflow-hidden shadow-sm sm:rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">
                    @if (session('success'))
                        <div class="mb-4 p-3 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Top bar --}}
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4">
                        <div>
                            <h3 class="text-lg font-semibold">All Contact Requests</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @if($requests->total() > 0)
                                    Showing <span class="font-medium">{{ $requests->firstItem() }}</span>–<span class="font-medium">{{ $requests->lastItem() }}</span>
                                    of <span class="font-medium">{{ $requests->total() }}</span>
                                @else
                                    No results
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Filters --}}
                    <form method="GET" action="{{ route('admin.contact_requests.index') }}" class="mb-5">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
                            <div class="lg:col-span-3">
                                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, email, phone, message…"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-500">
                            </div>
                            <div>
                                <select name="plan" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                                    <option value="">All Plans</option>
                                    <option value="Basic" @selected(request('plan')==='Basic')>Basic</option>
                                    <option value="Professional" @selected(request('plan')==='Professional')>Professional</option>
                                </select>
                            </div>
                            <div>
                                <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                                    <option value="">All Statuses</option>
                                    @foreach(['new','contacted','scheduled','closed'] as $s)
                                        <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <input type="date" name="from" value="{{ request('from') }}"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                            </div>
                            <div>
                                <input type="date" name="to" value="{{ request('to') }}"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                            </div>
                            <div>
                                <select name="sort" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                                    <option value="newest" @selected(request('sort','newest')==='newest')>Newest first</option>
                                    <option value="oldest" @selected(request('sort')==='oldest')>Oldest first</option>
                                </select>
                            </div>
                            <div>
                                <select name="per_page" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                                    @foreach([10,15,25,50,100] as $n)
                                        <option value="{{ $n }}" @selected((int)request('per_page',15)===$n)>{{ $n }} / page</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex gap-2 lg:col-span-3">
                                <button class="flex-1 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Apply
                                </button>
                                <a href="{{ route('admin.contact_requests.index') }}"
                                   class="px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:underline">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    {{-- Mobile cards --}}
                    <ul class="sm:hidden space-y-3">
                        @forelse($requests as $r)
                            <li class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="font-semibold">{{ $r->name }} <span class="text-xs text-gray-500">({{ $r->plan_name }})</span></div>
                                        <div class="text-xs text-gray-500">{{ $r->email }} @if($r->phone) · {{ $r->phone }} @endif</div>
                                        <div class="text-xs text-gray-500 mt-1">{{ $r->created_at->format('d M, Y H:i') }}</div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-xs
                                        @class([
                                            'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' => $r->status==='new',
                                            'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200' => $r->status==='contacted',
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200' => $r->status==='scheduled',
                                            'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200' => $r->status==='closed',
                                        ])">
                                        {{ ucfirst($r->status) }}
                                    </span>
                                </div>
                                @if($r->message)
                                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-200">{{ \Illuminate\Support\Str::limit($r->message, 160) }}</p>
                                @endif
                                <div class="mt-3 flex items-center justify-between">
                                    <a href="{{ route('admin.contact_requests.show', $r) }}" class="text-blue-600 hover:underline">Open</a>
                                    <form method="POST" action="{{ route('admin.contact_requests.update', $r) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <select name="status" class="text-xs rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" onchange="this.form.submit()">
                                            @foreach(['new','contacted','scheduled','closed'] as $s)
                                                <option value="{{ $s }}" @selected($r->status===$s)>{{ ucfirst($s) }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                            </li>
                        @empty
                            <li class="text-center text-gray-500 dark:text-gray-400">No contact requests yet.</li>
                        @endforelse
                    </ul>

                    {{-- Desktop table --}}
                    <div class="hidden sm:block overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                        <table class="min-w-full bg-white dark:bg-gray-900">
                            <thead class="bg-gray-50 dark:bg-gray-800/60">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Created</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Plan</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Contact</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Preferred</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Message</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                @forelse($requests as $r)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 align-top">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $r->created_at->format('d M, Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $r->plan_name }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <div class="font-medium">{{ $r->name }}</div>
                                            <div class="text-gray-500">{{ $r->email }} @if($r->phone) · {{ $r->phone }} @endif</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($r->preferred_date)
                                                {{ $r->preferred_date?->format('d M, Y') }}
                                                @if($r->preferred_time)
                                                    · {{ \Carbon\Carbon::parse($r->preferred_time)->format('H:i') }}
                                                @endif
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            @if($r->message)
                                                {{ \Illuminate\Support\Str::limit($r->message, 120) }}
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <form method="POST" action="{{ route('admin.contact_requests.update', $r) }}" class="inline">
                                                @csrf @method('PATCH')
                                                <select name="status" class="text-xs rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" onchange="this.form.submit()">
                                                    @foreach(['new','contacted','scheduled','closed'] as $s)
                                                        <option value="{{ $s }}" @selected($r->status===$s)>{{ ucfirst($s) }}</option>
                                                    @endforeach
                                                </select>
                                            </form>
                                            <a href="{{ route('admin.contact_requests.show', $r) }}" class="ml-3 text-sm text-blue-600 hover:underline">Open</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No contact requests yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-5">
                        {{ $requests->onEachSide(1)->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
