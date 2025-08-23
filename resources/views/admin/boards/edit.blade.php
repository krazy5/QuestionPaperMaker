{{-- resources/views/admin/boards/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Edit Board') }}: <span class="font-normal">{{ $board->name }}</span></h2>
            <a href="{{ route('admin.boards.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-b from-white to-slate-50 dark:from-gray-900 dark:to-gray-950 border border-gray-200 dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-xl">
                <div class="p-6 sm:p-8">

                    @if ($errors->any())
                        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-200">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.boards.update', $board) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Board Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $board->name) }}" required
                                   class="mt-1 block w-full rounded-lg border border-gray-300 bg-white/90 px-3 py-2 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100 dark:placeholder:text-gray-500">
                            @error('name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.boards.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15l-6-6m0 0l6-6m-6 6h15"/></svg>
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25L15 10.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Update Board
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
