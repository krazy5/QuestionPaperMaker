<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Edit Paper Blueprint') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900/60 backdrop-blur overflow-hidden shadow-sm sm:rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="p-6 md:p-8">
                    @include('admin.blueprints._form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
