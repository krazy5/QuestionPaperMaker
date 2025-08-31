{{-- resources/views/subscription/contact.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                Book a Call / Request Activation
            </h2>
            <a href="{{ route('subscription.pricing') }}"
               class="text-sm px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                &larr; Back to Pricing
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900/60 backdrop-blur shadow-sm sm:rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                <form method="POST" action="{{ route('pricing.contact.store') }}" class="p-6 sm:p-8 space-y-6">
                    @csrf

                    @if ($errors->any())
                        <div class="rounded-lg border border-red-200 dark:border-red-900/50 bg-red-50 dark:bg-red-900/30 p-4">
                            <ul class="text-sm text-red-700 dark:text-red-200 list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Selected Plan</label>
                        <input type="text" name="plan_name" value="{{ $plan }}" readonly
                               class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">We’ll follow up with the best offer for this plan.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Your Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone (optional)</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preferred Date (optional)</label>
                            <input type="date" name="preferred_date" value="{{ old('preferred_date') }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preferred Time (optional)</label>
                            <input type="time" name="preferred_time" value="{{ old('preferred_time') }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Message (optional)</label>
                        <textarea name="message" rows="4"
                                  class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                                  placeholder="Tell us about your institute, preferred timing, or any questions…">{{ old('message') }}</textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('subscription.pricing') }}"
                           class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>

            <p class="mt-4 text-center text-xs text-gray-500 dark:text-gray-400">
                We’ll reach out within 1–2 business days. This is not a payment—just a contact request.
            </p>
        </div>
    </div>
</x-app-layout>
