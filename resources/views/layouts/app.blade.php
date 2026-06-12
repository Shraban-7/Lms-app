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

    @include('layouts.navbar')

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
