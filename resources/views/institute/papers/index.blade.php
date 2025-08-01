<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Institute Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Success Message from other actions --}}
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- NEW: Subscription Status Display --}}
                    @if($activeSubscription)
                        {{-- User IS subscribed --}}
                        <div class="mb-6 p-4 bg-blue-100 text-blue-800 border-l-4 border-blue-500 rounded">
                            <p><strong>Current Plan:</strong> {{ $activeSubscription->plan_name }}</p>
                            <p><strong>Status:</strong> Active until {{ \Carbon\Carbon::parse($activeSubscription->ends_at)->format('F j, Y') }}</p>
                        </div>
                    @else
                        {{-- User IS NOT subscribed --}}
                        <div class="mb-6 p-4 bg-yellow-100 text-yellow-800 border-l-4 border-yellow-500 rounded">
                            <h4 class="font-bold">No Active Subscription</h4>
                            <p>Please choose a plan to unlock all features, including creating new papers.</p>
                            <a href="{{ route('subscription.pricing') }}" class="mt-2 inline-block font-semibold text-yellow-900 hover:underline">
                                View Pricing Plans &rarr;
                            </a>
                        </div>
                    @endif


                    {{-- Header with Create Button --}}
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">My Papers</h3>
                        <div>
                            <a href="{{ route('institute.questions.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 mr-2">My Questions</a>
                            
                            {{-- Conditionally enable/disable the button --}}
                            @if($activeSubscription)
                                <a href="{{ route('institute.papers.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">+ Create New Paper</a>
                            @else
                                <button class="px-4 py-2 bg-blue-300 text-white rounded cursor-not-allowed" disabled title="Subscription required">+ Create New Paper</button>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Papers Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            {{-- ... table head ... --}}
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class & Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($papers as $paper)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $paper->title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $paper->subject->academicClass->name ?? '' }} - {{ $paper->subject->name ?? '' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($paper->status) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('institute.papers.preview', $paper) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Preview</a>
                                            <a href="{{ route('institute.papers.answers', $paper) }}" class="text-green-600 hover:text-green-900 mr-4">Answer Key</a>
                                            <a href="{{ route('institute.papers.edit', $paper) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                                            <form action="{{ route('institute.papers.destroy', $paper) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">You have not created any papers yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Links --}}
                    <div class="mt-4">
                        {{ $papers->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
