{{-- ══════════════════════════════════════════════
     NAVBAR — Premium Floating Design
══════════════════════════════════════════════ --}}
<style>
    /* Navbar — theme-aware tokens */
    :root {
        --nav-bg:         rgba(255, 255, 255, 0.75);
        --nav-bg-scrolled:rgba(255, 255, 255, 0.92);
        --nav-border:     rgba(0, 0, 0, 0.07);
    }
    [data-theme="dark"] {
        --nav-bg:         rgba(11, 15, 25, 0.75);
        --nav-bg-scrolled:rgba(11, 15, 25, 0.94);
        --nav-border:     rgba(255, 255, 255, 0.06);
    }

    /* Navbar scroll-aware transparency */
    #main-navbar {
        position: sticky;
        top: 0;
        z-index: 50;
        background: var(--nav-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-bottom: 1px solid var(--nav-border);
        transition: background 0.4s ease, box-shadow 0.4s ease, border-color 0.4s ease;
    }
    #main-navbar.scrolled {
        background: var(--nav-bg-scrolled) !important;
        box-shadow: 0 1px 30px rgba(0, 0, 0, 0.1);
    }

    /* Animated logo ring */
    .logo-ring {
        position: relative;
        width: 40px; height: 40px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, rgba(99,102,241,0.12), rgba(236,72,153,0.12));
        border: 1px solid rgba(99,102,241,0.25);
        transition: all 0.3s ease;
        flex-shrink: 0;
        overflow: hidden;
    }
    .logo-ring::before {
        content: '';
        position: absolute;
        inset: -1px;
        border-radius: 12px;
        background: linear-gradient(135deg, #6366f1, #ec4899);
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: -1;
    }
    .logo-group:hover .logo-ring {
        transform: scale(1.08) rotate(-3deg);
        border-color: rgba(99,102,241,0.5);
        box-shadow: 0 4px 16px rgba(99,102,241,0.25);
    }
    .logo-group:hover .logo-ring::before { opacity: 0.15; }

    .logo-text {
        font-size: 20px;
        font-weight: 800;
        letter-spacing: -0.04em;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        transition: opacity 0.2s;
    }
    .logo-group:hover .logo-text { opacity: 0.85; }

    /* Nav links */
    .nav-link {
        display: inline-flex; align-items: center;
        padding: 7px 14px;
        border-radius: 9px;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text-secondary);
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .nav-link:hover {
        color: var(--text-primary);
        background: rgba(99,102,241,0.07);
    }
    .nav-link.active {
        color: #6366f1;
        background: rgba(99,102,241,0.1);
    }

    /* CTA buttons */
    .nav-btn-ghost {
        display: inline-flex; align-items: center;
        padding: 7px 16px;
        border-radius: 9px;
        font-size: 13.5px; font-weight: 600;
        color: var(--text-primary);
        border: 1.5px solid var(--border-color);
        background: transparent;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .nav-btn-ghost:hover {
        border-color: rgba(99,102,241,0.4);
        color: #6366f1;
        background: rgba(99,102,241,0.05);
        transform: translateY(-1px);
    }
    .nav-btn-primary {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 18px;
        border-radius: 9px;
        font-size: 13.5px; font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border: none;
        text-decoration: none;
        transition: all 0.25s ease;
        box-shadow: 0 4px 14px rgba(99,102,241,0.35);
        white-space: nowrap;
        cursor: pointer;
        font-family: inherit;
    }
    .nav-btn-primary:hover {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        transform: translateY(-1.5px);
        box-shadow: 0 6px 20px rgba(99,102,241,0.45);
    }

    /* Dashboard badge pill */
    .nav-dashboard-pill {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 14px;
        border-radius: 9px;
        font-size: 13px; font-weight: 700;
        color: #6366f1;
        background: rgba(99,102,241,0.1);
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .nav-dashboard-pill:hover {
        background: rgba(99,102,241,0.18);
        transform: translateY(-1px);
    }

    /* Theme toggle */
    .theme-toggle-btn {
        width: 36px; height: 36px;
        border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        border: 1.5px solid var(--border-color);
        background: transparent;
        cursor: pointer;
        color: var(--text-secondary);
        transition: all 0.2s ease;
    }
    .theme-toggle-btn:hover {
        color: var(--text-primary);
        border-color: rgba(99,102,241,0.35);
        background: rgba(99,102,241,0.07);
    }

    /* Divider dot */
    .nav-dot {
        width: 4px; height: 4px; border-radius: 50%;
        background: var(--border-color);
        display: inline-block;
        flex-shrink: 0;
    }
</style>

<nav id="main-navbar">
    <div style="max-width:1300px; margin:0 auto; padding:0 28px; height:68px; display:flex; align-items:center; justify-content:space-between; gap:16px;">

        {{-- ── Logo ── --}}
        <a href="{{ route('landing') }}" class="logo-group" style="display:flex;align-items:center;gap:10px;text-decoration:none;flex-shrink:0;">
            <div class="logo-ring">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                     style="stroke:url(#nav-grad);stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round;">
                    <defs>
                        <linearGradient id="nav-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#6366f1"/>
                            <stop offset="100%" stop-color="#ec4899"/>
                        </linearGradient>
                    </defs>
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M6 12.5V17c0 1 3 3 6 3s6-2 6-3v-4.5"/>
                    <path d="M12 12v6"/>
                    <circle cx="12" cy="18" r="1.5" fill="url(#nav-grad)" stroke="none"/>
                </svg>
            </div>
            <span class="logo-text">AetherLMS</span>
        </a>

        {{-- ── Nav Links ── --}}
        <div style="display:flex;align-items:center;gap:4px;flex:1;justify-content:center;" class="nav-center-links">
            <a href="{{ route('landing') }}" class="nav-link {{ request()->routeIs('landing') ? 'active' : '' }}">Home</a>
            <a href="{{ route('landing') }}#courses" class="nav-link">Courses</a>
        </div>

        {{-- ── Right Actions ── --}}
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
            @auth
                {{-- Dashboard smart link --}}
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="nav-dashboard-pill">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        Admin
                    </a>
                @elseif(auth()->user()->isInstructor())
                    <a href="{{ route('instructor.dashboard') }}" class="nav-dashboard-pill">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('builder.index') }}" class="nav-link {{ request()->routeIs('builder.*') ? 'active' : '' }}">Builder</a>
                @else
                    <a href="{{ route('dashboard') }}" class="nav-dashboard-pill">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        My Learning
                    </a>
                @endif

                <span class="nav-dot"></span>

                {{-- User avatar + logout --}}
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,rgba(99,102,241,0.2),rgba(139,92,246,0.2));display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#6366f1;flex-shrink:0;border:1.5px solid rgba(99,102,241,0.2);">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="nav-btn-ghost" style="padding:6px 12px;font-size:13px;">
                            Sign out
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="nav-btn-ghost">Log in</a>
                <a href="{{ route('register') }}" class="nav-btn-primary">
                    Get started
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            @endauth

            {{-- Theme toggle --}}
            <button class="theme-toggle-btn" id="themeToggler" aria-label="Toggle theme">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
            </button>
        </div>
    </div>
</nav>

<script>
    // Scroll effect
    (function() {
        const nav = document.getElementById('main-navbar');
        if (!nav) return;
        function onScroll() {
            if (window.scrollY > 20) nav.classList.add('scrolled');
            else nav.classList.remove('scrolled');
        }
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    })();
</script>