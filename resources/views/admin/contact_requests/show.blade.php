<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                Contact Request
            </h2>
            <a href="{{ route('admin.contact_requests.index') }}"
               class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                &larr; Back
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-900/60 backdrop-blur overflow-hidden shadow-sm sm:rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="p-6 sm:p-8 text-gray-900 dark:text-gray-100 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Plan</div>
                            <div class="font-medium">{{ $contactRequest->plan_name }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Status</div>
                            <form method="POST" action="{{ route('admin.contact_requests.update', $contactRequest) }}" class="mt-1">
                                @csrf @method('PATCH')
                                <select name="status" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" onchange="this.form.submit()">
                                    @foreach(['new','contacted','scheduled','closed'] as $s)
                                        <option value="{{ $s }}" @selected($contactRequest->status===$s)>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Name</div>
                            <div class="font-medium">{{ $contactRequest->name }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Email</div>
                            <div class="font-medium">{{ $contactRequest->email }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Phone</div>
                            <div class="font-medium">{{ $contactRequest->phone ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Submitted</div>
                            <div class="font-medium">{{ $contactRequest->created_at->format('d M, Y H:i') }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Preferred Date</div>
                            <div class="font-medium">{{ $contactRequest->preferred_date?->format('d M, Y') ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Preferred Time</div>
                            <div class="font-medium">{{ $contactRequest->preferred_time ? \Carbon\Carbon::parse($contactRequest->preferred_time)->format('H:i') : '—' }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Message</div>
                        <div class="whitespace-pre-wrap">{{ $contactRequest->message ?: '—' }}</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
