<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Blueprint Detected
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if(session('error'))
                        <div class="mb-4 p-3 rounded bg-red-100 text-red-700">
                            {{ session('error') }}
                        </div>
                    @endif

                    <h3 class="text-lg font-semibold mb-2">We found a matching blueprint</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Your paper <span class="font-semibold">{{ $paper->title }}</span> matches the blueprint
                        <span class="font-semibold">“{{ $blueprint->name }}”</span> ({{ $blueprint->total_marks }} marks).
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 border rounded-md bg-gray-50">
                            <div class="text-sm text-gray-600 mb-2">Board / Class / Subject</div>
                            <div class="font-medium">
                                {{ $blueprint->board->name ?? '—' }} /
                                {{ $blueprint->academicClass->name ?? '—' }} /
                                {{ $blueprint->subject->name ?? '—' }}
                            </div>
                        </div>
                        <div class="p-4 border rounded-md bg-gray-50">
                            <div class="text-sm text-gray-600 mb-2">Total Marks</div>
                            <div class="font-medium">{{ $blueprint->total_marks }}</div>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col md:flex-row gap-3">
                        {{-- Adopt blueprint --}}
                        <form method="POST" action="{{ route('institute.papers.adopt_blueprint', [$paper, $blueprint]) }}">
                            @csrf
                            <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Use this Blueprint
                            </button>
                        </form>

                        {{-- Skip and go free-form --}}
                        <a href="{{ route('institute.papers.questions.select', $paper) }}"
                           class="px-5 py-2 border rounded-md text-gray-700 hover:bg-gray-50">
                            Continue with Custom Selection
                        </a>
                    </div>

                    {{-- Optional: quick link to preview blueprint structure --}}
                    <div class="mt-4">
                        <a href="{{ route('admin.blueprints.show', $blueprint) }}" target="_blank"
                           class="text-sm text-indigo-600 hover:underline">
                            Preview blueprint structure (opens admin view)
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
