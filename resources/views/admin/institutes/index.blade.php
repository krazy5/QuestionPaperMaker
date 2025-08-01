<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Institutes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Institute Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered On</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                {{-- The loop starts here, creating the $institute variable for each row --}}
                                @forelse ($institutes as $institute)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $institute->institute_name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $institute->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $institute->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $institute->created_at->format('d M, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            {{-- This link is now correctly inside the loop --}}
                                            <a href="{{ route('admin.institutes.show', $institute) }}" class="text-indigo-600 hover:text-indigo-900">
                                                Manage Subscriptions
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No institutes have registered yet.</td>
                                    </tr>
                                @endforelse
                                {{-- The loop ends here --}}
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $institutes->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
