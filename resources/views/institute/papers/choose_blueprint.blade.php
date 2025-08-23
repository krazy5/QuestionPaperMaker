<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            Blueprint Detected
        </h2>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900/60 backdrop-blur overflow-hidden shadow-sm sm:rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="p-6 sm:p-8 text-gray-900 dark:text-gray-100">

                    @if(session('error'))
                        <div class="mb-4 p-3 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold">We found a matching blueprint</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Your paper <span class="font-medium text-gray-900 dark:text-gray-200">{{ $paper->title }}</span>
                                matches <span class="font-medium text-gray-900 dark:text-gray-200">“{{ $blueprint->name }}”</span>
                                ({{ $blueprint->total_marks }} marks).
                            </p>
                        </div>
                        <a href="{{ route('admin.blueprints.show', $blueprint) }}" target="_blank"
                           class="hidden sm:inline-flex items-center px-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                            Preview structure
                        </a>
                    </div>

                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 rounded-lg ring-1 ring-gray-200 dark:ring-gray-700 bg-gray-50 dark:bg-gray-800/60">
                            <div class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-400">Board / Class / Subject</div>
                            <div class="mt-1 font-medium">
                                {{ $blueprint->board->name ?? '—' }} /
                                {{ $blueprint->academicClass->name ?? '—' }} /
                                {{ $blueprint->subject->name ?? '—' }}
                            </div>
                        </div>
                        <div class="p-4 rounded-lg ring-1 ring-gray-200 dark:ring-gray-700 bg-gray-50 dark:bg-gray-800/60">
                            <div class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-400">Total Marks</div>
                            <div class="mt-1 font-medium">{{ $blueprint->total_marks }}</div>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        {{-- Adopt blueprint --}}
                        <form method="POST" action="{{ route('institute.papers.adopt_blueprint', [$paper, $blueprint]) }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 shadow-sm">
                                Use this Blueprint
                            </button>
                        </form>

                        {{-- Skip and go free-form --}}
                        <a href="{{ route('institute.papers.questions.select', $paper) }}"
                           class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800">
                            Continue with Custom Selection
                        </a>

                        {{-- Mobile-only quick preview link --}}
                        <a href="{{ route('admin.blueprints.show', $blueprint) }}" target="_blank"
                           class="sm:hidden inline-flex items-center justify-center px-5 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800">
                            Preview structure
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
