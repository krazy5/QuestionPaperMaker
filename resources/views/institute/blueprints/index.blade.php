    <x-app-layout>
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('My Blueprints') }}
                </h2>
                <a href="{{ route('institute.blueprints.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    + Create New Blueprint
                </a>
            </div>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                {{-- Success Message --}}
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Marks</th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($blueprints as $blueprint)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $blueprint->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $blueprint->subject->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $blueprint->academicClass->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $blueprint->total_marks }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('institute.blueprints.show', $blueprint) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                                <a href="{{ route('institute.blueprints.edit', $blueprint) }}" class="text-green-600 hover:text-green-900 ml-4">Edit</a>
                                            {{-- ✅ ADD THIS FORM --}}
                                            {{-- ✅ REPLACE the old "Create Paper" form with this --}}
                                                <div x-data="{ open: false }" class="inline-block ml-4">
                                                    <button @click="open = true" type="button" class="px-3 py-1 text-xs font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                                        Create Paper
                                                    </button>

                                                    <div x-show="open" class="fixed inset-0 z-10 bg-gray-500 bg-opacity-75" @click="open = false"></div>

                                                    <div x-show="open" class="fixed inset-0 z-20 flex items-center justify-center">
                                                        <div @click.away="open = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                                                            <h3 class="text-lg font-medium text-gray-900 mb-4">Create Paper from Blueprint</h3>
                                                            <p class="text-sm text-gray-600 mb-4">Please select an exam date for the new paper.</p>
                                                            
                                                            <form action="{{ route('institute.papers.createFromBlueprint', $blueprint) }}" method="POST">
                                                                @csrf
                                                                <div>
                                                                    <label for="exam_date_{{ $blueprint->id }}" class="block text-sm font-medium text-gray-700">Exam Date</label>
                                                                    <input type="date" name="exam_date" id="exam_date_{{ $blueprint->id }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                                                </div>

                                                                <div class="mt-6 flex justify-end">
                                                                    <button type="button" @click="open = false" class="mr-4 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                                                        Cancel
                                                                    </button>
                                                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                                                        Confirm & Create Paper
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                No blueprints found. <a href="{{ route('institute.blueprints.create') }}" class="text-blue-600 hover:underline">Create one now</a>.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $blueprints->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>