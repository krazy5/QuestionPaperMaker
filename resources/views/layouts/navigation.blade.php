<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                {{-- Desktop nav links --}}
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    {{-- Admin-only --}}
                    @if(auth()->user()->role === 'admin')
                        <x-nav-link :href="route('admin.blueprints.index')" :active="request()->routeIs('admin.blueprints.*')">
                            {{ __('Manage Blueprints') }}
                        </x-nav-link>
                    @endif

                    {{-- Institute-only --}}
                    @if(auth()->user()->role === 'institute')
                        <x-nav-link :href="route('institute.papers.index')" :active="request()->routeIs('institute.papers.*')">
                            {{ __('My Papers') }}
                        </x-nav-link>
                        {{-- <x-nav-link :href="route('institute.blueprints.index')" :active="request()->routeIs('institute.blueprints.*')">
                            {{ __('My Blueprints') }}
                        </x-nav-link> --}}
                        <x-nav-link :href="route('subscription.pricing')" :active="request()->routeIs('subscription.pricing')">
                            {{ __('Pricing') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            {{-- Desktop right side: profile dropdown (includes theme toggle) --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        {{-- Desktop theme toggle --}}
                        <button
                            type="button"
                            class="js-theme-toggle w-full text-left inline-flex items-center rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                            aria-pressed="false" aria-label="Toggle dark mode">
                            {{-- Sun (shown in dark mode to indicate switching back to light) --}}
                            <svg class="js-theme-sun h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364-1.414 1.414M6.05 17.95l-1.414 1.414m12.728 0-1.414-1.414M6.05 6.05 4.636 4.636M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8z"/>
                            </svg>
                            {{-- Moon (shown in light mode to indicate switching to dark) --}}
                            <svg class="js-theme-moon h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                            </svg>
                            <span class="ml-2 hidden sm:inline">Theme</span>
                        </button>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Mobile hamburger --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out" aria-label="Open menu">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            {{-- Admin-only --}}
            @if(auth()->user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.blueprints.index')" :active="request()->routeIs('admin.blueprints.*')">
                    {{ __('Manage Blueprints') }}
                </x-responsive-nav-link>
            @endif

            {{-- Institute-only --}}
            @if(auth()->user()->role === 'institute')
                <x-responsive-nav-link :href="route('institute.papers.index')" :active="request()->routeIs('institute.papers.*')">
                    {{ __('My Papers') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('institute.blueprints.index')" :active="request()->routeIs('institute.blueprints.*')">
                    {{ __('My Blueprints') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('subscription.pricing')" :active="request()->routeIs('subscription.pricing')">
                    {{ __('Pricing') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1 px-4">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                {{-- Mobile theme toggle --}}
                <button
                    type="button"
                    class="js-theme-toggle w-full inline-flex items-center justify-start rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                    aria-pressed="false" aria-label="Toggle dark mode">
                    <svg class="js-theme-sun h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364-1.414 1.414M6.05 17.95l-1.414 1.414m12.728 0-1.414-1.414M6.05 6.05 4.636 4.636M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8z"/>
                    </svg>
                    <svg class="js-theme-moon h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                    <span class="ml-2">Theme</span>
                </button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>

    {{-- Theme toggle controller (works for both desktop & mobile buttons) --}}
    <script>
        (function () {
            const root = document.documentElement;
            const KEY = 'theme'; // 'light' | 'dark' | (optional 'system')

            function prefersDark() {
                return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            }
            function getStored() {
                try { return localStorage.getItem(KEY); } catch (_) { return null; }
            }
            function setStored(v) {
                try { localStorage.setItem(KEY, v); } catch (_) {}
            }

            function apply(theme) {
                const dark = theme === 'dark' || (theme === 'system' && prefersDark());
                root.classList.toggle('dark', dark);

                document.querySelectorAll('.js-theme-sun').forEach(el => el.classList.toggle('hidden', !dark));
                document.querySelectorAll('.js-theme-moon').forEach(el => el.classList.toggle('hidden', dark));
                document.querySelectorAll('.js-theme-toggle').forEach(btn => btn.setAttribute('aria-pressed', String(dark)));
            }

            // Initialize
            let theme = getStored() || (prefersDark() ? 'dark' : 'light');
            apply(theme);

            // Bind all toggles
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.js-theme-toggle');
                if (!btn) return;
                theme = (document.documentElement.classList.contains('dark') ? 'light' : 'dark');
                setStored(theme);
                apply(theme);
            });

            // Optional: react to system theme changes if using 'system' mode
            try {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function () {
                    const stored = getStored();
                    if (!stored || stored === 'system') apply('system');
                });
            } catch (_) {}
        })();
    </script>
</nav>
