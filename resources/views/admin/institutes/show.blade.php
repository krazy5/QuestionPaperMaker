<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                Manage Institute: <span class="italic">{{ $institute->institute_name ?? $institute->name }}</span>
            </h2>
            <a href="{{ route('admin.institutes.index') }}"
               class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                &larr; Back to Institutes
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Success/Error Messages --}}
            @if (session('success'))
                <div class="p-4 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Add Subscription --}}
            <div class="bg-white dark:bg-gray-900/60 backdrop-blur overflow-hidden shadow-sm sm:rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                <section class="p-6 sm:p-8">
                    <header class="mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Add New Subscription</h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Manually activate a plan for this institute. Use for offline payments or special offers.
                        </p>
                    </header>

                    @if ($errors->any())
                        <div class="mb-4 p-4 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.institutes.subscriptions.store', $institute) }}" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="plan_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Plan</label>
                                <select id="plan_name" name="plan_name" class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="Basic" @selected(old('plan_name')==='Basic')>Basic</option>
                                    <option value="Professional" @selected(old('plan_name')==='Professional')>Professional</option>
                                    <option value="Enterprise" @selected(old('plan_name')==='Enterprise')>Enterprise</option>
                                </select>
                            </div>
                            <div>
                                <label for="starts_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date &amp; Time</label>
                                <input type="datetime-local"
                                       id="starts_at"
                                       name="starts_at"
                                       value="{{ old('starts_at', now()->format('Y-m-d\TH:i')) }}"
                                       class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="ends_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date &amp; Time</label>
                                <input type="datetime-local"
                                       id="ends_at"
                                       name="ends_at"
                                       value="{{ old('ends_at', now()->addMonth()->format('Y-m-d\TH:i')) }}"
                                       class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
                                Activate Plan
                            </button>
                        </div>
                    </form>
                </section>
            </div>

            {{-- Subscription History --}}
            <div class="bg-white dark:bg-gray-900/60 backdrop-blur overflow-hidden shadow-sm sm:rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                <section class="p-6 sm:p-8">
                    <header class="mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Subscription History</h2>
                    </header>

                    <!-- Mobile cards -->
                    <ul class="sm:hidden space-y-3">
                        @forelse ($subscriptions as $subscription)
                            @php
                                $active = $subscription->status === 'active'
                                    && \Carbon\Carbon::parse($subscription->starts_at)->lte(now())
                                    && \Carbon\Carbon::parse($subscription->ends_at)->isFuture();
                            @endphp
                            <li class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="font-medium">{{ $subscription->plan_name }}</div>
                                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($subscription->starts_at)->format('d M, Y h:i A') }}
                                            &rarr;
                                            {{ \Carbon\Carbon::parse($subscription->ends_at)->format('d M, Y h:i A') }}
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs {{ $active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                </div>
                                @if($active)
                                    <div class="mt-3 text-right">
                                        <form method="POST" action="{{ route('admin.institutes.subscriptions.cancel', $subscription) }}">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Are you sure you want to cancel this subscription?')" class="text-sm text-red-600 hover:underline">
                                                Cancel
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </li>
                        @empty
                            <li class="text-center text-gray-500 dark:text-gray-400">This institute has no subscription history.</li>
                        @endforelse
                    </ul>

                    <!-- Desktop table -->
                    <div class="hidden sm:block overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                        <table class="min-w-full bg-white dark:bg-gray-900">
                            <thead class="bg-gray-50 dark:bg-gray-800/60">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Plan</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Start Date &amp; Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">End Date &amp; Time</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                @forelse ($subscriptions as $subscription)
                                    @php
                                        $active = $subscription->status === 'active'
                                            && \Carbon\Carbon::parse($subscription->starts_at)->lte(now())
                                            && \Carbon\Carbon::parse($subscription->ends_at)->isFuture();
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $subscription->plan_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                                {{ ucfirst($subscription->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($subscription->starts_at)->format('d M, Y h:i A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($subscription->ends_at)->format('d M, Y h:i A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if($active)
                                                <form method="POST" action="{{ route('admin.institutes.subscriptions.cancel', $subscription) }}">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to cancel this subscription?')">
                                                        Cancel
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">This institute has no subscription history.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>