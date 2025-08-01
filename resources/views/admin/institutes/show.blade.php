<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manage Institute: <span class="italic">{{ $institute->institute_name ?? $institute->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Success/Error Messages --}}
            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Section 1: Manually Add Subscription --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            Add New Subscription
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Manually activate a plan for this institute. Use this for offline payments or special offers.
                        </p>
                    </header>

                    {{-- We will create this route in the next step --}}
                    <form method="POST" action="{{ route('admin.institutes.subscriptions.store', $institute) }}" class="mt-6 space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="plan_name" class="block text-sm font-medium text-gray-700">Plan</label>
                                <select id="plan_name" name="plan_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option>Basic</option>
                                    <option>Professional</option>
                                </select>
                            </div>
                            <div>
                                <label for="starts_at" class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input type="date" id="starts_at" name="starts_at" value="{{ now()->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                             <div>
                                <label for="ends_at" class="block text-sm font-medium text-gray-700">End Date</label>
                                <input type="date" id="ends_at" name="ends_at" value="{{ now()->addMonth()->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700">Activate Plan</button>
                        </div>
                    </form>
                </section>
            </div>


            {{-- Section 2: Subscription History --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            Subscription History
                        </h2>
                    </header>
                    <div class="mt-6 overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($subscriptions as $subscription)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $subscription->plan_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($subscription->status == 'active' && \Carbon\Carbon::parse($subscription->ends_at)->isFuture()) bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($subscription->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($subscription->starts_at)->format('d M, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($subscription->ends_at)->format('d M, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            {{-- ADD THIS LOGIC FOR THE CANCEL BUTTON --}}
                                            @if($subscription->status == 'active' && \Carbon\Carbon::parse($subscription->ends_at)->isFuture())
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
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">This institute has no subscription history.</td>
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
