<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aether LMS - Premium Course Platform')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fonts
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('styles')
    <script>
        // Synchronous theme initialization to prevent flash
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</head>

<body class="bg-gray-50 dark:bg-[#0B0F19] text-text-primary font-sans">
    <div class="glow-backdrop-1"></div>
    <div class="glow-backdrop-2"></div>

    <!-- Navigation Header -->
    <nav
        class="sticky top-0 z-50 bg-white/70 dark:bg-surface/60 backdrop-blur-xl border-b border-gray-200 dark:border-border py-4">
        <div class="max-w-[1300px] mx-auto px-8 flex items-center justify-between">
            <a href="{{ route('landing') }}"
                class="flex items-center gap-3 group">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gray-950/5 dark:bg-white/5 backdrop-blur-md border border-gray-950/10 dark:border-white/10 shadow-[0_4px_12px_rgba(0,0,0,0.05)] dark:shadow-[0_4px_12px_rgba(0,0,0,0.3)] group-hover:scale-105 group-hover:border-primary/50 transition-all duration-300">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                        style="stroke: url(#gradient); stroke-width: 2.2; stroke-linecap: round; stroke-linejoin: round;">
                        <defs>
                            <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="var(--primary)" />
                                <stop offset="100%" stop-color="var(--accent)" />
                            </linearGradient>
                        </defs>
                        <!-- A premium floating graduation cap / rocket fusion representing upward mobility (Antigravity) -->
                        <path d="M12 2L2 7l10 5 10-5-10-5z" />
                        <path d="M6 12.5V17c0 1 3 3 6 3s6-2 6-3v-4.5" />
                        <path d="M12 12v6" />
                        <circle cx="12" cy="18" r="1.5" fill="url(#gradient)" />
                    </svg>
                </div>
                <span class="text-2xl font-extrabold bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent group-hover:opacity-90 transition-opacity tracking-tight">AetherLMS</span>
            </a>

            <ul class="flex items-center space-x-1">
                @auth
                    <li><a href="{{ route('dashboard') }}"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-primary/10 text-primary font-semibold' : 'text-text-secondary hover:text-text-primary hover:bg-gray-100/50 dark:hover:bg-white/5' }}">Dashboard</a>
                    </li>
                    @if (auth()->user()->isInstructor())
                        <li><a href="{{ route('builder.index') }}"
                                class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-300 {{ request()->routeIs('builder.*') ? 'bg-primary/10 text-primary font-semibold' : 'text-text-secondary hover:text-text-primary hover:bg-gray-100/50 dark:hover:bg-white/5' }}">Course Builder</a></li>
                        <li><a href="{{ route('instructor.dashboard') }}"
                                class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-300 {{ request()->routeIs('instructor.dashboard') ? 'bg-primary/10 text-primary font-semibold' : 'text-text-secondary hover:text-text-primary hover:bg-gray-100/50 dark:hover:bg-white/5' }}">Instructor Payouts</a></li>
                    @endif
                    @if (auth()->user()->isAdmin())
                        <li><a href="{{ route('admin.dashboard') }}"
                                class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-300 {{ request()->routeIs('admin.dashboard') ? 'bg-primary/10 text-primary font-semibold' : 'text-text-secondary hover:text-text-primary hover:bg-gray-100/50 dark:hover:bg-white/5' }}">Admin Panel</a></li>
                    @endif
                    <li class="pl-2">
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                class="bg-surface-solid dark:bg-surface-solid text-text-primary border border-gray-200 dark:border-border hover:bg-gray-100 dark:hover:bg-border hover:-translate-y-0.5 px-3 py-1.5 text-sm rounded-lg transition-all duration-300 cursor-pointer">Logout</button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-primary hover:bg-gray-100/50 dark:hover:bg-white/5 transition-all duration-300">Login</a></li>
                    <li><a href="{{ route('register') }}"
                            class="bg-primary text-white px-3.5 py-1.5 text-sm rounded-lg hover:bg-primary-hover hover:-translate-y-0.5 transition-all duration-300 shadow-sm font-medium">Sign Up</a></li>
                @endauth

                <!-- Theme Switcher Toggle -->
                <li>
                    <button
                        class="cursor-pointer text-xl bg-transparent border-none text-text-secondary hover:text-text-primary transition-all duration-300"
                        id="themeToggler" aria-label="Toggle Theme">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                        </svg>
                    </button>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="min-h-[calc(100vh-150px)] pb-16">
        <div class="max-w-[1300px] mx-auto px-8 mt-8">
            @if (session('success'))
                <div class="bg-success/10 text-success border border-success py-4 px-6 rounded-lg mb-6 font-semibold">
                    {{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="bg-danger/10 text-danger border border-danger py-4 px-6 rounded-lg mb-6 font-semibold">
                    {{ session('error') }}</div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Simple Modern Footer -->
    <footer class="border-t border-gray-200 dark:border-border py-8 text-center text-sm text-text-secondary">
        <div class="max-w-[1300px] mx-auto px-8">
            <p>&copy; {{ date('Y') }} AetherLMS. Engineered for Visual and Functional Excellence.</p>
        </div>
    </footer>

    <script>
        const themeToggler = document.getElementById('themeToggler');
        if (themeToggler) {
            themeToggler.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
            });
        }
    </script>
    @yield('scripts')
</body>

</html>
