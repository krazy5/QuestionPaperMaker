<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">

        <div class="min-h-screen flex flex-col sm:flex-row">

            <div class="hidden sm:flex sm:w-1/2 bg-gray-800 text-white flex-col items-center justify-center p-12 text-center">
                <a href="/">
                    <img src="{{ asset('images/logo-white.png') }}" alt="Company Logo" class="w-48 h-auto mb-8">
                </a>
                <h1 class="text-3xl font-semibold mb-2">Welcome to Our Platform</h1>
                <p class="text-gray-300 max-w-sm">
                    Manage your projects with ease and efficiency. Sign in to continue your journey.
                </p>
            </div>

            <div class="flex flex-col items-center justify-center w-full sm:w-1/2 bg-gray-100 p-6 sm:p-12">
                <div class="sm:hidden mb-6">
                    <a href="/">
                        <img src="{{ asset('images/logo.png') }}" alt="Company Logo" class="w-32 h-auto">
                    </a>
                </div>

                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </div>

        </div>
    </body>
</html>