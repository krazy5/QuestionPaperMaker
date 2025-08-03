<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Paper Blueprints') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">All Paper Blueprints</h3>
                        {{-- This link will work in the next step --}}
                        <a href="{{ route('admin.blueprints.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">+ Create New Blueprint</a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Blueprint Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Board</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($blueprints as $blueprint)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $blueprint->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $blueprint->board->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $blueprint->academicClass->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            {{-- This link will take us to the page to manage sections and rules --}}
                                            <a href="{{ route('admin.blueprints.show', $blueprint) }}" class="text-indigo-600 hover:text-indigo-900">Manage Sections & Rules</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No paper blueprints have been created yet.</td>
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
