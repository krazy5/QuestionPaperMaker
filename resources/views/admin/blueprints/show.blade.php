<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                Manage Blueprint: {{ $blueprint->name }}
            </h2>
            <a href="{{ route('admin.blueprints.index') }}"
               class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                &larr; Back to Blueprints
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Summary -->
            <div class="bg-white dark:bg-gray-900/60 backdrop-blur overflow-hidden shadow-sm sm:rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Blueprint Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div><span class="font-semibold">Name:</span> {{ $blueprint->name }}</div>
                        <div><span class="font-semibold">Total Marks:</span> {{ $blueprint->total_marks }}</div>
                        <div><span class="font-semibold">Subject:</span> {{ $blueprint->subject->name ?? '—' }}</div>
                        <div><span class="font-semibold">Class:</span> {{ $blueprint->academicClass->name ?? '—' }}</div>
                        <div><span class="font-semibold">Board:</span> {{ $blueprint->board->name ?? '—' }}</div>

                        @if(!empty($blueprint->selected_chapters))
                            <div class="md:col-span-3">
                                <span class="font-semibold">Selected Chapters:</span>
                                <span class="text-gray-700 dark:text-gray-300">
                                    {{ $blueprint->selected_chapter_names ?: '—' }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Livewire sections/rules manager -->
            <livewire:admin.blueprint-manager :blueprint="$blueprint" />

            <!-- Toast (Alpine) -->
            <div
                x-data="{ show: false, message: '' }"
                x-on:toast.window="message = $event.detail.message; show = true; setTimeout(()=> show=false, 2000)"
                x-show="show"
                x-transition
                class="fixed bottom-6 right-6 bg-gray-900 text-white text-sm px-4 py-2 rounded-md shadow-lg"
                style="display:none">
                <span x-text="message"></span>
            </div>

        </div>
    </div>
</x-app-layout>
