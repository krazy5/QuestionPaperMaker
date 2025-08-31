<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Add Institute Account') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900/60 backdrop-blur overflow-hidden shadow-sm sm:rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="p-6 sm:p-8 text-gray-900 dark:text-gray-100">

                    @if ($errors->any())
                        <div class="mb-4 p-4 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.institutes.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div>
                            <label for="institute_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Institute Name (optional)</label>
                            <input type="text" id="institute_name" name="institute_name"
                                   value="{{ old('institute_name') }}"
                                   class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="e.g., Sunrise Public School">
                            @error('institute_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contact Person Name</label>
                            <input type="text" id="name" name="name" required
                                   value="{{ old('name') }}"
                                   class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="e.g., Priya Sharma">
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input type="email" id="email" name="email" required
                                   value="{{ old('email') }}"
                                   class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="name@example.com">
                            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- === ADDED SECTION: SUBSCRIPTION DETAILS === --}}
                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100">
                                Initial Subscription (Optional)
                            </h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                You can assign a manual subscription plan to this institute upon creation. Leave the plan name blank to skip.
                            </p>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="plan_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Plan Name</label>
                                    <select id="plan_name" name="plan_name" class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">-- No Subscription --</option>
                                        <option value="Basic" @if(old('plan_name') == 'Basic') selected @endif>Basic</option>
                                        <option value="Professional" @if(old('plan_name') == 'Professional') selected @endif>Professional</option>
                                        <option value="Enterprise" @if(old('plan_name') == 'Enterprise') selected @endif>Enterprise</option>
                                    </select>
                                    @error('plan_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    {{-- Empty div for grid alignment --}}
                                </div>
                                <div>
                                    <label for="starts_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Starts At</label>
                                    <input type="datetime-local" id="starts_at" name="starts_at" value="{{ old('starts_at') }}" class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                                    @error('starts_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="ends_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ends At</label>
                                    <input type="datetime-local" id="ends_at" name="ends_at" value="{{ old('ends_at') }}" class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                                    @error('ends_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                        {{-- === END OF ADDED SECTION === --}}

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                                <input type="password" id="password" name="password" required
                                       class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" required
                                       class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('admin.institutes.index') }}" class="px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:underline">Cancel</a>
                            <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 dark:bg-blue-500 text-white hover:bg-blue-700 dark:hover:bg-blue-600 shadow-sm">Create Account</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>