<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Edit Subject') }}: <span class="font-normal">{{ $subject->name }}</span>
        </h2>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900/60 backdrop-blur shadow-sm sm:rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="p-6 sm:p-8">

                    <form action="{{ route('admin.subjects.update', $subject) }}" method="POST" class="space-y-6">
                        @csrf @method('PUT')

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Subject Name
                            </label>
                            <input  id="name" name="name" type="text" value="{{ old('name', $subject->name) }}" required
                                    class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500">
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Class -->
                        <div>
                            <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Class
                            </label>
                            <select id="class_id" name="class_id" required
                                    class="mt-2 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Select a Class --</option>
                                @foreach ($classes as $c)
                                    <option value="{{ $c->id }}" @selected(old('class_id', $subject->class_id) == $c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                            @error('class_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.subjects.index') }}"
                               class="inline-flex items-center px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:underline">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-5 py-2.5 rounded-lg bg-blue-600 dark:bg-blue-500 text-white hover:bg-blue-700 dark:hover:bg-blue-600 shadow-sm">
                                Update Subject
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
