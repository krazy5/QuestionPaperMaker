{{-- resources/views/institute/blueprints/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manage Blueprint: {{ $blueprint->name }}
            </h2>
            <a href="{{ route('institute.blueprints.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                &larr; Back to Blueprints
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="p-4 bg-green-100 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-100 text-red-700 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Blueprint Summary --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 border-b">
                    <h3 class="text-lg font-medium mb-4">Blueprint Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div><span class="font-semibold">Name:</span> {{ $blueprint->name }}</div>
                        <div><span class="font-semibold">Total Marks:</span> {{ $blueprint->total_marks }}</div>
                        <div><span class="font-semibold">Subject:</span> {{ $blueprint->subject->name ?? '—' }}</div>
                        <div><span class="font-semibold">Class:</span> {{ $blueprint->academicClass->name ?? '—' }}</div>
                        <div><span class="font-semibold">Board:</span> {{ $blueprint->board->name ?? '—' }}</div>
                        @if(!empty($blueprint->selected_chapters))
                            <div class="md:col-span-3">
                                <span class="font-semibold">Selected Chapters:</span>
                                <span class="text-gray-700">
                                    {{ $blueprint->selected_chapter_names ?: '—' }}
                                </span>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

            {{-- Livewire: Sections + Rules manager --}}
            <livewire:institute.blueprint-manager :blueprint="$blueprint" />

            {{-- Optional: simple toast UI for Livewire events --}}
            <div
                x-data="{ show: false, message: '' }"
                x-on:toast.window="message = $event.detail.message; show = true; setTimeout(()=> show=false, 2000)"
                x-show="show"
                x-transition
                class="fixed bottom-6 right-6 bg-gray-900 text-white text-sm px-4 py-2 rounded-md shadow-lg"
                style="display:none"
            >
                <span x-text="message"></span>
            </div>

        </div>
    </div>
</x-app-layout>
