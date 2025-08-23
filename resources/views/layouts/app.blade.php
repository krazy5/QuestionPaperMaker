<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Load your CSS/JS via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])


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


    {{-- Livewire CSS must be in the HEAD --}}
    @livewireStyles
  </head>

  <body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
      @include('layouts.navigation')

      @isset($header)
        <header class="bg-white dark:bg-gray-800 shadow">
          <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            {{ $header }}
          </div>
        </header>
      @endisset

      <main>
        {{ $slot }}
      </main>
    </div>

    {{-- Livewire JS must be before </body>, AFTER your app.js/Alpine --}}
    @livewireScripts


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var btn  = document.getElementById('themeToggle');
            var sun  = document.getElementById('iconSun');
            var moon = document.getElementById('iconMoon');

            function syncIcons () {
                var isDark = document.documentElement.classList.contains('dark');
                if (isDark) { sun && sun.classList.add('hidden'); moon && moon.classList.remove('hidden'); }
                else { moon && moon.classList.add('hidden'); sun && sun.classList.remove('hidden'); }
                btn && btn.setAttribute('aria-pressed', String(isDark));
            }

            syncIcons();

            if (btn) {
                btn.addEventListener('click', function () {
                    var isDark = document.documentElement.classList.toggle('dark');
                    try { localStorage.setItem('theme', isDark ? 'dark' : 'light'); } catch (e) {}
                    syncIcons();
                });
            }
        });
    </script>
  </body>
</html>
