<x-app-layout>
    {{-- Instant theme apply (no flicker) --}}
    <script>
        (function () {
            try {
                var saved = localStorage.getItem('theme');
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                var enableDark = saved ? (saved === 'dark') : prefersDark;
                if (enableDark) document.documentElement.classList.add('dark');
            } catch (e) {}
        })();
    </script>

    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                {{ __('Edit Class') }}: <span class="font-normal">{{ $class->name }}</span>
            </h2>

            {{-- Dark mode toggle (persists via localStorage) --}}
            <button id="themeToggle"
                    type="button"
                    class="inline-flex items-center rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                    aria-pressed="false" aria-label="Toggle dark mode">
                <svg id="iconSun" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364-1.414 1.414M6.05 17.95l-1.414 1.414m12.728 0-1.414-1.414M6.05 6.05 4.636 4.636M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8z"/></svg>
                <svg id="iconMoon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                <span class="ml-2 hidden sm:inline">Theme</span>
            </button>
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

                    <form action="{{ route('admin.classes.update', $class) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Class Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $class->name) }}" required
                                   class="mt-1 block w-full rounded-lg border border-gray-300 bg-white/90 px-3 py-2 text-gray-900 shadow-sm placeholder:text-gray-400
                                          focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                                          dark:border-gray-700 dark:bg-gray-900/70 dark:text-gray-100 dark:placeholder:text-gray-500">
                            @error('name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.classes.index') }}"
                               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm
                                      hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600
                                      dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15l-6-6m0 0l6-6m-6 6h15"/></svg>
                                Cancel
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm
                                           hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25L15 10.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Update Class
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Toggle logic --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var btn = document.getElementById('themeToggle');
            var sun = document.getElementById('iconSun');
            var moon = document.getElementById('iconMoon');
            function syncIcons () {
                var isDark = document.documentElement.classList.contains('dark');
                if (isDark) { sun.classList.add('hidden'); moon.classList.remove('hidden'); }
                else { moon.classList.add('hidden'); sun.classList.remove('hidden'); }
                btn.setAttribute('aria-pressed', String(isDark));
            }
            syncIcons();
            btn.addEventListener('click', function () {
                var isDark = document.documentElement.classList.toggle('dark');
                try { localStorage.setItem('theme', isDark ? 'dark' : 'light'); } catch (e) {}
                syncIcons();
            });
        });
    </script>
</x-app-layout>
