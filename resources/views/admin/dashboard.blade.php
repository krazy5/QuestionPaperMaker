<x-app-layout>
    
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                {{ __('Admin Dashboard') }}
            </h2>

           
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Application Stats -->
            <div class="bg-gradient-to-b from-white to-slate-50 dark:from-gray-900 dark:to-gray-950 border border-gray-200 dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-xl">
                <div class="p-6 sm:p-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Application Stats</h3>

                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Card: Institutes Registered -->
                        <div class="group relative rounded-xl border border-gray-200 dark:border-gray-800 bg-gradient-to-br from-white to-slate-50 dark:from-gray-800 dark:to-gray-900 p-5 shadow-sm hover:shadow-md transition-shadow focus-within:ring-2 focus-within:ring-blue-500">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/30 group-hover:scale-105 transition-transform" aria-hidden="true">
                                    <!-- Building Library Icon (Heroicons outline) -->
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-blue-600 dark:text-blue-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 19.5V6.75a2.25 2.25 0 0 1 1.126-1.956l6.75-3.938a2.25 2.25 0 0 1 2.248 0l6.75 3.938A2.25 2.25 0 0 1 21 6.75V19.5M3 19.5h18M3 19.5h18M3 19.5v-7.5m18 7.5v-7.5M7.5 15h9" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['institutes'] }}</div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Institutes Registered</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card: Questions in Bank -->
                        <div class="group relative rounded-xl border border-gray-200 dark:border-gray-800 bg-gradient-to-br from-white to-slate-50 dark:from-gray-800 dark:to-gray-900 p-5 shadow-sm hover:shadow-md transition-shadow focus-within:ring-2 focus-within:ring-blue-500">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-900/30 group-hover:scale-105 transition-transform" aria-hidden="true">
                                    <!-- Rectangle Stack Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-emerald-600 dark:text-emerald-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5l7.5-3 7.5 3m-15 0l7.5 3 7.5-3m-15 0v9l7.5 3 7.5-3v-9" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['questions'] }}</div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Questions in Bank</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card: Papers Generated -->
                        <div class="group relative rounded-xl border border-gray-200 dark:border-gray-800 bg-gradient-to-br from-white to-slate-50 dark:from-gray-800 dark:to-gray-900 p-5 shadow-sm hover:shadow-md transition-shadow focus-within:ring-2 focus-within:ring-blue-500">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-50 dark:bg-purple-900/30 group-hover:scale-105 transition-transform" aria-hidden="true">
                                    <!-- Document Text Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-purple-600 dark:text-purple-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5A3.375 3.375 0 0 0 10.125 2.25H6.75m0 0L3 6m3.75-3.75V6m0 0h3.375A3.375 3.375 0 0 1 13.5 9.375v1.5A1.125 1.125 0 0 0 14.625 12h1.5A3.375 3.375 0 0 1 19.5 15.375V21A1.125 1.125 0 0 1 18.375 22.125H7.125A1.125 1.125 0 0 1 6 21V6z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['papers'] }}</div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Papers Generated</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="mt-10">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Quick Links</h3>
                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            <!-- Link cards -->
                            <a href="{{ route('admin.classes.index') }}" class="group relative rounded-xl border border-gray-200 dark:border-gray-800 bg-white/80 dark:bg-gray-900/80 backdrop-blur p-5 shadow-sm hover:shadow-md transition-all focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/30" aria-hidden="true">
                                        <!-- Academic Cap Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14.25l8.955-4.477a.75.75 0 000-1.346L12 3.95 3.045 8.427a.75.75 0 000 1.346L12 14.25z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 10.5v4.875c0 .621.356 1.188.92 1.462l3.33 1.596a3 3 0 002.999 0l3.33-1.596a1.64 1.64 0 00.92-1.462V10.5" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100">Manage Classes</div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Create, edit and organize</p>
                                    </div>
                                </div>
                            </a>

                            <a href="{{ route('admin.boards.index') }}" class="group relative rounded-xl border border-gray-200 dark:border-gray-800 bg-white/80 dark:bg-gray-900/80 backdrop-blur p-5 shadow-sm hover:shadow-md transition-all focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-indigo-50 dark:bg-indigo-900/30" aria-hidden="true">
                                        <!-- Squares 2x2 Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75h6.5v6.5h-6.5zM13.75 3.75h6.5v6.5h-6.5zM3.75 13.75h6.5v6.5h-6.5zM13.75 13.75h6.5v6.5h-6.5z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100">Manage Boards</div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Curriculum boards</p>
                                    </div>
                                </div>
                            </a>

                            <a href="{{ route('admin.questions.index') }}" class="group relative rounded-xl border border-gray-200 dark:border-gray-800 bg-white/80 dark:bg-gray-900/80 backdrop-blur p-5 shadow-sm hover:shadow-md transition-all focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-900/30" aria-hidden="true">
                                        <!-- Question Mark Circle Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9.75a2.25 2.25 0 00-2.25 2.25m2.25-2.25a2.25 2.25 0 012.25 2.25m-2.25-2.25V12m0 4.5h.008v.008H12V16.5z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100">Manage Questions</div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Question bank</p>
                                    </div>
                                </div>
                            </a>

                            <a href="{{ route('admin.subjects.index') }}" class="group relative rounded-xl border border-gray-200 dark:border-gray-800 bg-white/80 dark:bg-gray-900/80 backdrop-blur p-5 shadow-sm hover:shadow-md transition-all focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-900/30" aria-hidden="true">
                                        <!-- Book Open Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-amber-600 dark:text-amber-400 group-hover:scale-110 transition-transform">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5h8.25v15H3.75A2.25 2.25 0 011.5 17.25v-9A2.25 2.25 0 013.75 6h0zM12 4.5h8.25A2.25 2.25 0 0122.5 6.75v9A2.25 2.25 0 0120.25 18h-8.25V4.5z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100">Manage Subjects</div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Subjects per class</p>
                                    </div>
                                </div>
                            </a>

                            <a href="{{ route('admin.chapters.index') }}" class="group relative rounded-xl border border-gray-200 dark:border-gray-800 bg-white/80 dark:bg-gray-900/80 backdrop-blur p-5 shadow-sm hover:shadow-md transition-all focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-pink-50 dark:bg-pink-900/30" aria-hidden="true">
                                        <!-- List Bullet Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-pink-600 dark:text-pink-400 group-hover:scale-110 transition-transform">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12m-12 5.25h12m-12 5.25h12M3.75 6.75h.008v.008H3.75V6.75zm0 5.25h.008v.008H3.75v-.008zm0 5.25h.008v.008H3.75v-.008z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100">Manage Chapters</div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Per-subject chapters</p>
                                    </div>
                                </div>
                            </a>

                            <a href="{{ route('admin.blueprints.index') }}" class="group relative rounded-xl border border-gray-200 dark:border-gray-800 bg-white/80 dark:bg-gray-900/80 backdrop-blur p-5 shadow-sm hover:shadow-md transition-all focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-600">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-purple-50 dark:bg-purple-900/30" aria-hidden="true">
                                        <!-- Puzzle Piece Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h-1.5a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25H12m0-13.5h3.75a2.25 2.25 0 012.25 2.25v1.5m-6-3.75V9m0 0a2.25 2.25 0 104.5 0V6.75M12 9v4.5" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100">Manage Blueprints</div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Paper patterns</p>
                                    </div>
                                </div>
                            </a>

                            <a href="{{ route('admin.institutes.index') }}" class="group relative rounded-xl border border-gray-200 dark:border-gray-800 bg-white/80 dark:bg-gray-900/80 backdrop-blur p-5 shadow-sm hover:shadow-md transition-all focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-green-50 dark:bg-green-900/30" aria-hidden="true">
                                        <!-- Users Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-green-600 dark:text-green-400 group-hover:scale-110 transition-transform">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9 9 0 10-6 0M12 11.25a3 3 0 110-6 3 3 0 010 6z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100">Manage Institutes</div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Tenants & admins</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

   
</x-app-layout>
