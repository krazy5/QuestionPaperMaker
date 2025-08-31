<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Institute Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash: success --}}
            @if (session('success'))
                <div class="p-4 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Subscription panel --}}
            @if($activeSubscription)

                @php
                    // Get the application's configured timezone for consistent calculations
                    $appTimezone = config('app.timezone');

                    // Parse start and end times *with* the correct timezone
                    $start = \Carbon\Carbon::parse($activeSubscription->starts_at, $appTimezone);
                    $end   = \Carbon\Carbon::parse($activeSubscription->ends_at, $appTimezone);
                    $now   = now($appTimezone);

                    // --- Time Remaining Calculation (Corrected Logic) ---
                    $remaining = max(0, $end->timestamp - $now->timestamp);

                    $daysLeft    = floor($remaining / 86400);
                    $hoursLeft   = floor(($remaining % 86400) / 3600);
                    $minutesLeft = floor(($remaining % 3600) / 60);

                    // --- Progress Bar Calculation ---
                    $totalSeconds = max(1, $end->timestamp - $start->timestamp);
                    $usedSeconds  = max(0, min($totalSeconds, $now->timestamp - $start->timestamp));
                    $pct          = round(($usedSeconds / $totalSeconds) * 100, 2);
                @endphp

                <div class="bg-white dark:bg-gray-900/60 backdrop-blur shadow-sm sm:rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold">Current Plan: {{ $activeSubscription->plan_name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Active until <span class="font-medium">{{ $end->format('F j, Y g:i A') }}</span>
                                    &middot;
                                    <span class="font-medium">
                                        {{ $daysLeft }} {{ Str::plural('day', $daysLeft) }},
                                        {{ $hoursLeft }} {{ Str::plural('hour', $hoursLeft) }},
                                        {{ $minutesLeft }} {{ Str::plural('minute', $minutesLeft) }}
                                    </span>
                                    remaining
                                </p>
                            </div>
                            <a href="{{ route('subscription.pricing') }}"
                               class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 shadow-sm">
                                Manage Subscription
                            </a>
                        </div>

                        {{-- Progress --}}
                        <div class="mt-4">
                            <div class="h-2 w-full bg-gray-200 dark:bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-2 bg-blue-600 dark:bg-blue-500" style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                                {{ $start->format('d M g:i A') }} – {{ $end->format('d M g:i A') }} ({{ $pct }}% elapsed)
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-900/60 backdrop-blur shadow-sm sm:rounded-xl ring-1 ring-yellow-300/50 dark:ring-yellow-700/50">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-200">No Active Subscription</h3>
                                <p class="text-sm text-yellow-800 dark:text-yellow-300/90">
                                    Choose a plan to unlock all features, including creating new papers.
                                </p>
                            </div>
                            <a href="{{ route('subscription.pricing') }}"
                               class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 shadow-sm">
                                View Pricing Plans →
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Toolbar: title + actions --}}
            <div class="bg-white dark:bg-gray-900/60 backdrop-blur shadow-sm sm:rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">My Papers</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Create, manage, and preview your exam papers.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('institute.questions.index') }}"
                               class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">
                                My Questions
                            </a>
                            @if($activeSubscription)
                                <a href="{{ route('institute.papers.create') }}"
                                   class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 shadow-sm">
                                    + Create New Paper
                                </a>
                            @else
                                <button class="px-4 py-2 rounded-lg bg-blue-300/70 text-white cursor-not-allowed" disabled title="Subscription required">
                                    + Create New Paper
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Papers table (Livewire) --}}
            <div class="bg-white dark:bg-gray-900/60 backdrop-blur shadow-sm sm:rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">
                    <livewire:institute.papers-table />
                </div>
            </div>

        </div>
    </div>
</x-app-layout>