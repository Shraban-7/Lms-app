<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Control Center — AetherLMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fonts
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        (function() {
            const t = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <style>
        /* ── Layout Shell ── */
        html, body { height: 100%; margin: 0; overflow: hidden; }

        /* ── Sidebar ── */
        #admin-sidebar {
            width: 260px;
            min-width: 260px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: fixed;
            left: 0; top: 0;
            z-index: 100;
            transition: transform 0.35s cubic-bezier(.4,0,.2,1), box-shadow 0.35s ease;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Light / dark sidebar tokens */
        :root {
            --sidebar-bg: #ffffff;
            --sidebar-border: rgba(0,0,0,0.07);
            --sidebar-item-hover: rgba(79,70,229,0.06);
            --sidebar-item-active-bg: rgba(79,70,229,0.10);
            --sidebar-item-active-text: #4f46e5;
            --sidebar-section-label: #9ca3af;
            --card-bg: rgba(255,255,255,0.85);
            --card-border: rgba(0,0,0,0.07);
            --table-row-hover: rgba(79,70,229,0.03);
            --page-bg: #f3f4f8;
            --accent-pink: #ec4899;
            --accent-teal: #14b8a6;
            --accent-amber: #f59e0b;
        }
        [data-theme="dark"] {
            --sidebar-bg: #0f1623;
            --sidebar-border: rgba(255,255,255,0.06);
            --sidebar-item-hover: rgba(99,102,241,0.08);
            --sidebar-item-active-bg: rgba(99,102,241,0.15);
            --sidebar-item-active-text: #818cf8;
            --sidebar-section-label: #4b5563;
            --card-bg: rgba(17,24,39,0.7);
            --card-border: rgba(255,255,255,0.06);
            --table-row-hover: rgba(99,102,241,0.05);
            --page-bg: #0b0f19;
        }

        /* ── Main content shifts right of sidebar ── */
        #admin-main {
            margin-left: 260px;
            height: 100vh;
            overflow-y: auto;
            background: var(--page-bg);
            display: flex;
            flex-direction: column;
        }

        /* Scrollbar */
        #admin-main::-webkit-scrollbar,
        #admin-sidebar::-webkit-scrollbar { width: 5px; }
        #admin-main::-webkit-scrollbar-track,
        #admin-sidebar::-webkit-scrollbar-track { background: transparent; }
        #admin-main::-webkit-scrollbar-thumb,
        #admin-sidebar::-webkit-scrollbar-thumb {
            background: rgba(99,102,241,0.25);
            border-radius: 10px;
        }

        /* ── Nav item ── */
        .nav-item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s ease;
            margin: 1px 0;
            letter-spacing: 0.01em;
            white-space: nowrap;
        }
        .nav-item:hover {
            background: var(--sidebar-item-hover);
            color: var(--text-primary);
            transform: translateX(2px);
        }
        .nav-item.active {
            background: var(--sidebar-item-active-bg);
            color: var(--sidebar-item-active-text);
        }
        .nav-item .nav-icon {
            width: 18px; height: 18px;
            opacity: 0.7;
            flex-shrink: 0;
            transition: opacity 0.2s;
        }
        .nav-item.active .nav-icon,
        .nav-item:hover .nav-icon { opacity: 1; }

        .nav-section-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--sidebar-section-label);
            padding: 16px 14px 6px;
        }

        /* ── Stat Cards ── */
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 22px 24px;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px -8px rgba(0,0,0,0.15);
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 3px;
            border-radius: 16px 16px 0 0;
        }
        .stat-card.blue::before   { background: linear-gradient(90deg, #6366f1, #8b5cf6); }
        .stat-card.pink::before   { background: linear-gradient(90deg, #ec4899, #f43f5e); }
        .stat-card.teal::before   { background: linear-gradient(90deg, #14b8a6, #06b6d4); }
        .stat-card.amber::before  { background: linear-gradient(90deg, #f59e0b, #f97316); }

        .stat-icon {
            width: 42px; height: 42px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 14px;
        }
        .stat-icon.blue  { background: rgba(99,102,241,0.12); color: #6366f1; }
        .stat-icon.pink  { background: rgba(236,72,153,0.12); color: #ec4899; }
        .stat-icon.teal  { background: rgba(20,184,166,0.12); color: #14b8a6; }
        .stat-icon.amber { background: rgba(245,158,11,0.12); color: #f59e0b; }

        /* ── Glass panel ── */
        .glass-panel {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            overflow: hidden;
        }

        /* ── Data table ── */
        .data-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        .data-table thead tr {
            border-bottom: 1.5px solid var(--card-border);
        }
        .data-table th {
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--text-secondary);
            white-space: nowrap;
        }
        .data-table td {
            padding: 13px 16px;
            border-bottom: 1px solid var(--card-border);
            color: var(--text-primary);
        }
        .data-table tbody tr:last-child td { border-bottom: none; }
        .data-table tbody tr {
            transition: background 0.15s;
        }
        .data-table tbody tr:hover { background: var(--table-row-hover); }

        /* ── Badges ── */
        .badge {
            display: inline-flex; align-items: center;
            padding: 3px 9px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .badge-success { background: rgba(16,185,129,0.12); color: #10b981; }
        .badge-warning { background: rgba(245,158,11,0.12); color: #f59e0b; }
        .badge-danger  { background: rgba(239,68,68,0.12);  color: #ef4444; }
        .badge-primary { background: rgba(99,102,241,0.12); color: #6366f1; }
        .badge-teal    { background: rgba(20,184,166,0.12); color: #14b8a6; }

        /* ── Buttons ── */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 7px 16px;
            border-radius: 9px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            text-decoration: none;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary { background: #6366f1; color: #fff; box-shadow: 0 4px 12px rgba(99,102,241,0.3); }
        .btn-primary:hover { background: #4f46e5; box-shadow: 0 6px 16px rgba(99,102,241,0.4); }
        .btn-danger  { background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid rgba(239,68,68,0.2); }
        .btn-danger:hover { background: #ef4444; color: #fff; border-color: #ef4444; }
        .btn-success { background: #10b981; color: #fff; box-shadow: 0 4px 10px rgba(16,185,129,0.25); }
        .btn-success:hover { background: #059669; }
        .btn-warning { background: rgba(245,158,11,0.1); color: #f59e0b; border: 1px solid rgba(245,158,11,0.25); }
        .btn-warning:hover { background: #f59e0b; color: #fff; border-color: #f59e0b; }
        .btn-ghost   { background: transparent; border: 1px solid var(--card-border); color: var(--text-secondary); }
        .btn-ghost:hover { color: var(--text-primary); background: var(--sidebar-item-hover); }
        .btn-sm { padding: 5px 12px; font-size: 12px; border-radius: 7px; }

        /* ── Input ── */
        .form-input {
            width: 100%;
            padding: 10px 14px;
            background: var(--card-bg);
            border: 1.5px solid var(--card-border);
            border-radius: 10px;
            font-size: 13.5px;
            color: var(--text-primary);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: inherit;
        }
        .form-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.18);
        }
        .form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 7px;
            letter-spacing: 0.02em;
        }

        /* ── Progress bar ── */
        .progress-bar { height: 6px; border-radius: 999px; background: var(--card-border); overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 999px; }

        /* ── Topbar ── */
        #admin-topbar {
            position: sticky; top: 0; z-index: 50;
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--card-border);
            padding: 14px 28px;
            display: flex; align-items: center; justify-content: space-between;
            gap: 16px;
            flex-shrink: 0;
        }

        /* ── Mobile sidebar toggle ── */
        @media (max-width: 900px) {
            #admin-sidebar { transform: translateX(-100%); box-shadow: none; }
            #admin-sidebar.open { transform: translateX(0); box-shadow: 20px 0 60px rgba(0,0,0,0.3); }
            #admin-main { margin-left: 0; }
            #sidebar-overlay { display: block; }
        }
        #sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 90;
            backdrop-filter: blur(2px);
        }

        /* ── Alert flash ── */
        .flash-alert {
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .flash-success { background: rgba(16,185,129,0.1); color: #059669; border: 1px solid rgba(16,185,129,0.25); }
        .flash-error   { background: rgba(239,68,68,0.1);  color: #dc2626; border: 1px solid rgba(239,68,68,0.25); }

        /* ── Role select ── */
        .role-select {
            background: var(--card-bg);
            border: 1.5px solid var(--card-border);
            border-radius: 7px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-primary);
            padding: 5px 8px;
            cursor: pointer;
            outline: none;
            font-family: inherit;
            transition: border-color 0.2s;
        }
        .role-select:focus { border-color: #6366f1; }

        /* Print */
        @media print {
            #admin-sidebar, #admin-topbar, .no-print { display: none !important; }
            #admin-main { margin: 0; height: auto; overflow: visible; }
        }

        /* Shimmer glow on sidebar logo */
        .logo-glow {
            background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body>

{{-- Mobile overlay --}}
<div id="sidebar-overlay" onclick="closeSidebar()"></div>

{{-- ════════════════════════════════════════
     SIDEBAR
════════════════════════════════════════ --}}
<aside id="admin-sidebar">

    {{-- Brand --}}
    <div style="padding: 20px 18px 14px; border-bottom: 1px solid var(--sidebar-border);">
        <a href="{{ route('landing') }}" style="display:flex; align-items:center; gap:10px; text-decoration:none;">
            <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,rgba(99,102,241,0.2),rgba(236,72,153,0.2));border:1px solid rgba(99,102,241,0.3);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="stroke:url(#sg);stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round;">
                    <defs><linearGradient id="sg" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#6366f1"/><stop offset="100%" stop-color="#ec4899"/></linearGradient></defs>
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M6 12.5V17c0 1 3 3 6 3s6-2 6-3v-4.5"/><path d="M12 12v6"/>
                </svg>
            </div>
            <div>
                <div class="logo-glow" style="font-size:17px;font-weight:800;letter-spacing:-0.03em;">AetherLMS</div>
                <div style="font-size:10px;font-weight:600;color:var(--text-secondary);letter-spacing:0.06em;text-transform:uppercase;margin-top:1px;">Admin Panel</div>
            </div>
        </a>
    </div>

    {{-- Nav --}}
    <nav style="flex:1; padding: 10px 10px;">

        <div class="nav-section-label">Overview</div>
        <a href="{{ route('admin.dashboard', ['tab'=>'analytics']) }}"
           class="nav-item {{ $tab==='analytics' ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20V10M18 20V4M6 20v-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Analytics
        </a>
        <a href="{{ route('admin.dashboard', ['tab'=>'reports']) }}"
           class="nav-item {{ $tab==='reports' ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" stroke-linecap="round" stroke-linejoin="round"/><polyline points="14 2 14 8 20 8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Reports
        </a>

        <div class="nav-section-label">Users</div>
        <a href="{{ route('admin.dashboard', ['tab'=>'students']) }}"
           class="nav-item {{ $tab==='students' ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 14a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z" stroke-linecap="round" stroke-linejoin="round"/><path d="M17 21v-2a4 4 0 0 0-4-4H11a4 4 0 0 0-4 4v2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Students
            <span style="margin-left:auto;background:rgba(99,102,241,0.15);color:#6366f1;font-size:10px;font-weight:700;padding:2px 7px;border-radius:999px;">{{ $totalStudents }}</span>
        </a>
        <a href="{{ route('admin.dashboard', ['tab'=>'instructors']) }}"
           class="nav-item {{ $tab==='instructors' ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9" cy="7" r="4" stroke-linecap="round" stroke-linejoin="round"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Instructors
            <span style="margin-left:auto;background:rgba(236,72,153,0.12);color:#ec4899;font-size:10px;font-weight:700;padding:2px 7px;border-radius:999px;">{{ $totalInstructors }}</span>
        </a>
        <a href="{{ route('admin.dashboard', ['tab'=>'moderators']) }}"
           class="nav-item {{ $tab==='moderators' ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Moderators
            <span style="margin-left:auto;background:rgba(20,184,166,0.12);color:#14b8a6;font-size:10px;font-weight:700;padding:2px 7px;border-radius:999px;">{{ $totalModerators }}</span>
        </a>

        <div class="nav-section-label">Content & Finance</div>
        <a href="{{ route('admin.dashboard', ['tab'=>'courses']) }}"
           class="nav-item {{ $tab==='courses' ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Courses
            <span style="margin-left:auto;background:rgba(245,158,11,0.12);color:#f59e0b;font-size:10px;font-weight:700;padding:2px 7px;border-radius:999px;">{{ $totalCourses }}</span>
        </a>
        <a href="{{ route('admin.dashboard', ['tab'=>'payments']) }}"
           class="nav-item {{ $tab==='payments' ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="20" height="14" x="2" y="5" rx="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M2 10h20" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Payments
        </a>

        <div class="nav-section-label">System</div>
        <a href="{{ route('admin.dashboard', ['tab'=>'settings']) }}"
           class="nav-item {{ $tab==='settings' ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            Site Settings
        </a>

    </nav>

    {{-- User profile footer --}}
    <div style="padding: 14px 16px; border-top: 1px solid var(--sidebar-border); display:flex; align-items:center; gap:10px;">
        <div style="width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:#fff;flex-shrink:0;">
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
        <div style="min-width:0;flex:1;">
            <div style="font-size:13px;font-weight:700;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ auth()->user()->name }}</div>
            <div style="font-size:11px;color:var(--text-secondary);">Administrator</div>
        </div>
        {{-- Theme toggle --}}
        <button id="sidebarThemeToggler" title="Toggle Theme" style="background:none;border:none;cursor:pointer;color:var(--text-secondary);padding:4px;border-radius:6px;transition:color 0.2s;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
        </button>
    </div>
</aside>

{{-- ════════════════════════════════════════
     MAIN AREA
════════════════════════════════════════ --}}
<div id="admin-main">

    {{-- Topbar --}}
    <div id="admin-topbar">
        <div style="display:flex;align-items:center;gap:12px;">
            {{-- Mobile hamburger --}}
            <button onclick="openSidebar()" class="no-print" style="display:none;background:none;border:none;cursor:pointer;color:var(--text-primary);padding:4px;" id="hamburger-btn">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <div>
                <div style="font-size:11px;font-weight:600;color:var(--text-secondary);letter-spacing:0.06em;text-transform:uppercase;">
                    Admin Console
                    <span style="margin: 0 6px; opacity:.4;">›</span>
                    <span style="color:var(--text-primary);">
                        @if($tab==='analytics') Platform Overview
                        @elseif($tab==='students') Student Management
                        @elseif($tab==='instructors') Instructor Management
                        @elseif($tab==='moderators') Moderator Controls
                        @elseif($tab==='courses') Course Catalog
                        @elseif($tab==='payments') Payments & Payouts
                        @elseif($tab==='reports') System Reports
                        @elseif($tab==='settings') Site Configuration
                        @endif
                    </span>
                </div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <span style="font-size:11px;color:var(--text-secondary);">{{ now()->format('D, M j Y') }}</span>
            <a href="{{ route('landing') }}" class="btn btn-ghost btn-sm no-print">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12" stroke-linecap="round"/></svg>
                Exit
            </a>
        </div>
    </div>

    {{-- Content --}}
    <div style="padding: 28px 32px; flex:1;">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="flash-alert flash-success">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash-alert flash-error">✕ {{ session('error') }}</div>
        @endif

        {{-- ══════════════════════════════════
             TAB: ANALYTICS
        ══════════════════════════════════ --}}
        @if($tab === 'analytics')

            <div style="margin-bottom:24px;">
                <h1 style="font-size:24px;font-weight:800;color:var(--text-primary);margin:0 0 4px;">Platform Overview</h1>
                <p style="font-size:13px;color:var(--text-secondary);margin:0;">Real-time metrics across the entire Aether LMS platform.</p>
            </div>

            {{-- Stat Cards --}}
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:18px;margin-bottom:24px;">
                <div class="stat-card blue">
                    <div class="stat-icon blue">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 14a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z" stroke-linecap="round" stroke-linejoin="round"/><path d="M17 21v-2a4 4 0 0 0-4-4H11a4 4 0 0 0-4 4v2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-secondary);margin-bottom:4px;">Students</div>
                    <div style="font-size:34px;font-weight:800;color:var(--text-primary);line-height:1;">{{ $totalStudents }}</div>
                    <div style="font-size:11.5px;color:var(--text-secondary);margin-top:6px;">Registered learners</div>
                </div>
                <div class="stat-card pink">
                    <div class="stat-icon pink">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-secondary);margin-bottom:4px;">Instructors</div>
                    <div style="font-size:34px;font-weight:800;color:var(--text-primary);line-height:1;">{{ $totalInstructors }}</div>
                    <div style="font-size:11.5px;color:var(--text-secondary);margin-top:6px;">Active educators</div>
                </div>
                <div class="stat-card teal">
                    <div class="stat-icon teal">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-secondary);margin-bottom:4px;">Moderators</div>
                    <div style="font-size:34px;font-weight:800;color:var(--text-primary);line-height:1;">{{ $totalModerators }}</div>
                    <div style="font-size:11.5px;color:var(--text-secondary);margin-top:6px;">Platform guardians</div>
                </div>
                <div class="stat-card amber">
                    <div class="stat-icon amber">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-secondary);margin-bottom:4px;">Courses</div>
                    <div style="font-size:34px;font-weight:800;color:var(--text-primary);line-height:1;">{{ $totalCourses }}</div>
                    <div style="font-size:11.5px;color:var(--text-secondary);margin-top:6px;">Active courses</div>
                </div>
            </div>

            {{-- Secondary metrics row --}}
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:18px;margin-bottom:28px;">
                <div class="glass-panel" style="padding:20px 22px;">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-secondary);margin-bottom:8px;">Avg Quiz Score</div>
                    <div style="font-size:28px;font-weight:800;color:var(--text-primary);">{{ $avgQuizScore }}<span style="font-size:16px;font-weight:600;color:var(--text-secondary);">%</span></div>
                    <div class="progress-bar" style="margin-top:10px;">
                        <div class="progress-fill" style="width:{{ min($avgQuizScore, 100) }}%;background:linear-gradient(90deg,#6366f1,#8b5cf6);"></div>
                    </div>
                </div>
                <div class="glass-panel" style="padding:20px 22px;">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-secondary);margin-bottom:8px;">Certificates Issued</div>
                    <div style="font-size:28px;font-weight:800;color:#10b981;">{{ $certificatesCount }}</div>
                    <div style="font-size:11.5px;color:var(--text-secondary);margin-top:6px;">Total completions verified</div>
                </div>
                <div class="glass-panel" style="padding:20px 22px;">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-secondary);margin-bottom:8px;">Lesson Completions</div>
                    <div style="font-size:28px;font-weight:800;color:#6366f1;">{{ $lessonCompletions }}</div>
                    <div style="font-size:11.5px;color:var(--text-secondary);margin-top:6px;">Individual lessons done</div>
                </div>
                <div class="glass-panel" style="padding:20px 22px;">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-secondary);margin-bottom:8px;">Approved Payouts</div>
                    <div style="font-size:28px;font-weight:800;color:#ec4899;">${{ number_format($totalPayoutVolume, 2) }}</div>
                    <div style="font-size:11.5px;color:var(--text-secondary);margin-top:6px;">Total disbursed volume</div>
                </div>
            </div>

            {{-- Activity chart --}}
            <div class="glass-panel" style="padding:24px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                    <div>
                        <h3 style="font-size:15px;font-weight:700;color:var(--text-primary);margin:0 0 3px;">Platform Activity</h3>
                        <p style="font-size:12px;color:var(--text-secondary);margin:0;">Visual snapshot of key platform metrics</p>
                    </div>
                    <span class="badge badge-primary">Live</span>
                </div>
                <svg viewBox="0 0 600 140" style="width:100%;height:140px;" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="gBlue" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#6366f1" stop-opacity="0.4"/><stop offset="100%" stop-color="#6366f1" stop-opacity="0"/></linearGradient>
                        <linearGradient id="gPink" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#ec4899" stop-opacity="0.4"/><stop offset="100%" stop-color="#ec4899" stop-opacity="0"/></linearGradient>
                        <linearGradient id="gTeal" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#14b8a6" stop-opacity="0.4"/><stop offset="100%" stop-color="#14b8a6" stop-opacity="0"/></linearGradient>
                        <linearGradient id="barBlue" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#6366f1"/><stop offset="100%" stop-color="rgba(99,102,241,0.2)"/></linearGradient>
                        <linearGradient id="barPink" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#ec4899"/><stop offset="100%" stop-color="rgba(236,72,153,0.2)"/></linearGradient>
                        <linearGradient id="barTeal" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#14b8a6"/><stop offset="100%" stop-color="rgba(20,184,166,0.2)"/></linearGradient>
                        <linearGradient id="barAmber" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#f59e0b"/><stop offset="100%" stop-color="rgba(245,158,11,0.2)"/></linearGradient>
                    </defs>
                    {{-- Grid lines --}}
                    @foreach([25,50,75,100] as $y)
                    <line x1="0" y1="{{ $y }}" x2="600" y2="{{ $y }}" stroke="currentColor" stroke-opacity="0.07" stroke-width="1"/>
                    @endforeach

                    {{-- Bars: Students, Instructors, Courses, Certs --}}
                    @php
                        $maxVal = max($totalStudents, $totalInstructors, $totalCourses, $certificatesCount, 1);
                        $bars = [
                            ['Students',    $totalStudents,    'barBlue',  60],
                            ['Instructors', $totalInstructors, 'barPink',  165],
                            ['Courses',     $totalCourses,     'barTeal',  270],
                            ['Certs',       $certificatesCount,'barAmber', 375],
                        ];
                    @endphp
                    @foreach($bars as $bar)
                        @php $h = max(10, round($bar[1]/$maxVal * 100)); @endphp
                        <rect x="{{ $bar[3] }}" y="{{ 110 - $h }}" width="50" height="{{ $h }}" rx="6" fill="url(#{{ $bar[2] }})"/>
                        <text x="{{ $bar[3] + 25 }}" y="128" text-anchor="middle" fill="currentColor" opacity="0.5" font-size="10" font-family="inherit">{{ $bar[0] }}</text>
                        <text x="{{ $bar[3] + 25 }}" y="{{ 110 - $h - 5 }}" text-anchor="middle" fill="currentColor" opacity="0.7" font-size="11" font-weight="700" font-family="inherit">{{ $bar[1] }}</text>
                    @endforeach
                </svg>
            </div>

        {{-- ══════════════════════════════════
             TAB: STUDENTS
        ══════════════════════════════════ --}}
        @elseif($tab === 'students')
            <div style="margin-bottom:24px;display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                <div>
                    <h1 style="font-size:24px;font-weight:800;color:var(--text-primary);margin:0 0 4px;">Student Management</h1>
                    <p style="font-size:13px;color:var(--text-secondary);margin:0;">{{ $totalStudents }} registered students · Manage roles and accounts</p>
                </div>
            </div>
            <div class="glass-panel">
                <div style="overflow-x:auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="text-align:left;">Student</th>
                                <th>Completions</th>
                                <th>Quizzes</th>
                                <th>Certs</th>
                                <th>Role</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $st)
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <div style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,rgba(99,102,241,0.2),rgba(139,92,246,0.2));display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#6366f1;flex-shrink:0;">
                                            {{ strtoupper(substr($st->name,0,2)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:700;font-size:13.5px;color:var(--text-primary);">{{ $st->name }}</div>
                                            <div style="font-size:11.5px;color:var(--text-secondary);">{{ $st->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align:center;font-weight:600;">{{ $st->completions_count }}</td>
                                <td style="text-align:center;font-weight:600;">{{ $st->quiz_attempts_count }}</td>
                                <td style="text-align:center;">
                                    <span class="badge badge-success">{{ $st->certificates_count }}</span>
                                </td>
                                <td>
                                    <form action="{{ route('admin.users.update-role', $st->id) }}" method="POST">
                                        @csrf
                                        <select name="role" onchange="this.form.submit()" class="role-select">
                                            <option value="student" {{ $st->role==='student'?'selected':'' }}>Student</option>
                                            <option value="instructor" {{ $st->role==='instructor'?'selected':'' }}>Instructor</option>
                                            <option value="moderator" {{ $st->role==='moderator'?'selected':'' }}>Moderator</option>
                                        </select>
                                    </form>
                                </td>
                                <td style="text-align:right;">
                                    <form action="{{ route('admin.users.delete', $st->id) }}" method="POST" onsubmit="return confirm('Delete this student account permanently?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-secondary);">No students registered yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        {{-- ══════════════════════════════════
             TAB: INSTRUCTORS
        ══════════════════════════════════ --}}
        @elseif($tab === 'instructors')
            <div style="margin-bottom:24px;">
                <h1 style="font-size:24px;font-weight:800;color:var(--text-primary);margin:0 0 4px;">Instructor Management</h1>
                <p style="font-size:13px;color:var(--text-secondary);margin:0;">{{ $totalInstructors }} instructors · Manage educator accounts and payout balances</p>
            </div>
            <div class="glass-panel">
                <div style="overflow-x:auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="text-align:left;">Instructor</th>
                                <th>Courses</th>
                                <th>Payout Balance</th>
                                <th>Role</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($instructors as $inst)
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <div style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,rgba(236,72,153,0.2),rgba(244,63,94,0.2));display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#ec4899;flex-shrink:0;">
                                            {{ strtoupper(substr($inst->name,0,2)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:700;font-size:13.5px;color:var(--text-primary);">{{ $inst->name }}</div>
                                            <div style="font-size:11.5px;color:var(--text-secondary);">{{ $inst->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align:center;">
                                    <span class="badge badge-primary">{{ $inst->courses_count }}</span>
                                </td>
                                <td style="font-weight:700;color:#10b981;">${{ number_format($inst->payout_balance, 2) }}</td>
                                <td>
                                    <form action="{{ route('admin.users.update-role', $inst->id) }}" method="POST">
                                        @csrf
                                        <select name="role" onchange="this.form.submit()" class="role-select">
                                            <option value="student" {{ $inst->role==='student'?'selected':'' }}>Student</option>
                                            <option value="instructor" {{ $inst->role==='instructor'?'selected':'' }}>Instructor</option>
                                            <option value="moderator" {{ $inst->role==='moderator'?'selected':'' }}>Moderator</option>
                                        </select>
                                    </form>
                                </td>
                                <td style="text-align:right;">
                                    <form action="{{ route('admin.users.delete', $inst->id) }}" method="POST" onsubmit="return confirm('Delete this instructor permanently?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-secondary);">No instructors registered yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        {{-- ══════════════════════════════════
             TAB: MODERATORS
        ══════════════════════════════════ --}}
        @elseif($tab === 'moderators')
            <div style="margin-bottom:24px;">
                <h1 style="font-size:24px;font-weight:800;color:var(--text-primary);margin:0 0 4px;">Moderator Controls</h1>
                <p style="font-size:13px;color:var(--text-secondary);margin:0;">Appoint or revoke moderator access for platform guardians.</p>
            </div>

            <div style="display:grid;grid-template-columns:1fr 340px;gap:22px;align-items:start;">
                {{-- Moderators table --}}
                <div class="glass-panel">
                    <div style="padding:18px 22px;border-bottom:1px solid var(--card-border);display:flex;align-items:center;justify-content:space-between;">
                        <h3 style="font-size:14px;font-weight:700;color:var(--text-primary);margin:0;">Active Moderators</h3>
                        <span class="badge badge-teal">{{ $totalModerators }} active</span>
                    </div>
                    <div style="overflow-x:auto;">
                        @if($moderators->isEmpty())
                            <div style="text-align:center;padding:48px 24px;color:var(--text-secondary);">
                                <svg style="width:40px;height:40px;margin:0 auto 12px;opacity:0.3;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <p style="font-size:13px;margin:0;">No moderators appointed yet.</p>
                            </div>
                        @else
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th style="text-align:left;">Moderator</th>
                                        <th style="text-align:right;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($moderators as $mod)
                                    <tr>
                                        <td>
                                            <div style="display:flex;align-items:center;gap:10px;">
                                                <div style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,rgba(20,184,166,0.2),rgba(6,182,212,0.2));display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#14b8a6;flex-shrink:0;">
                                                    {{ strtoupper(substr($mod->name,0,2)) }}
                                                </div>
                                                <div>
                                                    <div style="font-weight:700;font-size:13.5px;color:var(--text-primary);">{{ $mod->name }}</div>
                                                    <div style="font-size:11.5px;color:var(--text-secondary);">{{ $mod->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="text-align:right;">
                                            <form action="{{ route('admin.users.update-role', $mod->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="role" value="student">
                                                <button type="submit" class="btn btn-warning btn-sm">Demote</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>

                {{-- Appoint panel --}}
                <div class="glass-panel" style="padding:22px;">
                    <h3 style="font-size:14px;font-weight:700;color:var(--text-primary);margin:0 0 6px;">Appoint Moderator</h3>
                    <p style="font-size:12.5px;color:var(--text-secondary);margin:0 0 18px;line-height:1.55;">Search a user by email address to promote them to the Moderator role.</p>

                    <form action="{{ route('admin.dashboard') }}" method="GET">
                        <input type="hidden" name="tab" value="moderators">
                        <div style="margin-bottom:14px;">
                            <label class="form-label">User Email Address</label>
                            <div style="display:flex;gap:8px;">
                                <input class="form-input" type="email" name="search_email" value="{{ request('search_email') }}" placeholder="user@example.com" style="flex:1;">
                                <button type="submit" class="btn btn-primary" style="flex-shrink:0;">Find</button>
                            </div>
                        </div>
                    </form>

                    @php
                        $searchedEmail = request('search_email');
                        $searchedUser  = $searchedEmail ? \App\Models\User::where('email', $searchedEmail)->first() : null;
                    @endphp

                    @if($searchedEmail)
                        @if($searchedUser)
                            <div style="padding:16px;background:var(--sidebar-item-hover);border:1px solid var(--card-border);border-radius:10px;margin-top:4px;">
                                <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                                    <div style="width:36px;height:36px;border-radius:9px;background:linear-gradient(135deg,rgba(99,102,241,0.2),rgba(139,92,246,0.2));display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:#6366f1;flex-shrink:0;">
                                        {{ strtoupper(substr($searchedUser->name,0,2)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:700;font-size:13.5px;color:var(--text-primary);">{{ $searchedUser->name }}</div>
                                        <span class="badge badge-primary" style="margin-top:3px;text-transform:capitalize;">{{ $searchedUser->role }}</span>
                                    </div>
                                </div>
                                @if($searchedUser->role !== 'moderator')
                                    <form action="{{ route('admin.users.update-role', $searchedUser->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="role" value="moderator">
                                        <button type="submit" class="btn btn-success" style="width:100%;">Promote to Moderator</button>
                                    </form>
                                @else
                                    <p style="text-align:center;font-size:12.5px;color:#14b8a6;font-weight:600;margin:0;">✓ Already a Moderator</p>
                                @endif
                            </div>
                        @else
                            <div style="padding:12px 16px;background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);border-radius:10px;font-size:13px;font-weight:600;color:#ef4444;margin-top:4px;">
                                No account found for "{{ $searchedEmail }}"
                            </div>
                        @endif
                    @endif
                </div>
            </div>

        {{-- ══════════════════════════════════
             TAB: COURSES
        ══════════════════════════════════ --}}
        @elseif($tab === 'courses')
            <div style="margin-bottom:24px;">
                <h1 style="font-size:24px;font-weight:800;color:var(--text-primary);margin:0 0 4px;">Course Catalog</h1>
                <p style="font-size:13px;color:var(--text-secondary);margin:0;">{{ $totalCourses }} courses · Review and moderate all platform courses</p>
            </div>
            <div class="glass-panel">
                <div style="overflow-x:auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="text-align:left;">Course</th>
                                <th>Instructor</th>
                                <th>Modules</th>
                                <th>Lessons</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($courses as $course)
                            <tr>
                                <td style="max-width:220px;">
                                    <div style="font-weight:700;font-size:13.5px;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $course->title }}</div>
                                </td>
                                <td>
                                    <div style="font-size:12.5px;color:var(--text-secondary);">{{ $course->instructor->name }}</div>
                                </td>
                                <td style="text-align:center;font-weight:600;">{{ $course->modules_count }}</td>
                                <td style="text-align:center;font-weight:600;">{{ $course->lessons_count }}</td>
                                <td style="font-weight:700;">${{ number_format($course->price, 2) }}</td>
                                <td>
                                    @if($course->is_published)
                                        <span class="badge badge-success">Published</span>
                                    @else
                                        <span class="badge badge-warning">Draft</span>
                                    @endif
                                </td>
                                <td style="text-align:right;">
                                    <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;">
                                        <a href="{{ route('courses.show', $course->id) }}" class="btn btn-ghost btn-sm" target="_blank">View</a>
                                        <form action="{{ route('admin.courses.delete', $course->id) }}" method="POST" onsubmit="return confirm('Delete this course permanently?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-secondary);">No courses found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        {{-- ══════════════════════════════════
             TAB: PAYMENTS
        ══════════════════════════════════ --}}
        @elseif($tab === 'payments')
            <div style="margin-bottom:24px;">
                <h1 style="font-size:24px;font-weight:800;color:var(--text-primary);margin:0 0 4px;">Payments & Payouts</h1>
                <p style="font-size:13px;color:var(--text-secondary);margin:0;">Review and process instructor payout requests.</p>
            </div>

            {{-- Pending --}}
            <div style="margin-bottom:10px;font-size:13px;font-weight:700;color:var(--text-secondary);letter-spacing:0.03em;text-transform:uppercase;">
                Pending Requests
            </div>
            <div class="glass-panel" style="margin-bottom:28px;">
                @if($pendingPayouts->isEmpty())
                    <div style="text-align:center;padding:48px;color:var(--text-secondary);">
                        <svg style="width:40px;height:40px;margin:0 auto 12px;opacity:0.3;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="M12 8v4M12 16h.01" stroke-linecap="round"/></svg>
                        <p style="font-size:13px;margin:0;">No pending payouts at this time.</p>
                    </div>
                @else
                    <div style="overflow-x:auto;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="text-align:left;">Instructor</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Requested</th>
                                    <th style="text-align:right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingPayouts as $payout)
                                <tr>
                                    <td>
                                        <div style="font-weight:700;font-size:13.5px;color:var(--text-primary);">{{ $payout->instructor->name }}</div>
                                        <div style="font-size:11.5px;color:var(--text-secondary);">{{ $payout->instructor->email }}</div>
                                    </td>
                                    <td style="font-weight:700;font-size:15px;color:var(--text-primary);">${{ number_format($payout->amount, 2) }}</td>
                                    <td><span class="badge badge-primary" style="text-transform:capitalize;">{{ str_replace('_',' ',$payout->method) }}</span></td>
                                    <td style="font-size:12px;color:var(--text-secondary);">{{ $payout->created_at->format('M d, Y H:i') }}</td>
                                    <td style="text-align:right;">
                                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;">
                                            <form action="{{ route('admin.payouts.approve', $payout->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                            </form>
                                            <form action="{{ route('admin.payouts.reject', $payout->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Completed --}}
            <div style="margin-bottom:10px;font-size:13px;font-weight:700;color:var(--text-secondary);letter-spacing:0.03em;text-transform:uppercase;">
                Completed Ledger
            </div>
            <div class="glass-panel">
                @if($completedPayouts->isEmpty())
                    <div style="text-align:center;padding:32px;color:var(--text-secondary);font-size:13px;">No completed payouts found.</div>
                @else
                    <div style="overflow-x:auto;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="text-align:left;">Instructor</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Processed</th>
                                    <th>Tx ID</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($completedPayouts as $payout)
                                <tr>
                                    <td style="font-weight:700;font-size:13.5px;color:var(--text-primary);">{{ $payout->instructor->name }}</td>
                                    <td style="font-weight:700;">${{ number_format($payout->amount, 2) }}</td>
                                    <td style="font-size:12px;text-transform:capitalize;">{{ str_replace('_',' ',$payout->method) }}</td>
                                    <td>
                                        @if($payout->status === 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @else
                                            <span class="badge badge-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td style="font-size:12px;color:var(--text-secondary);">{{ $payout->updated_at->format('M d, Y') }}</td>
                                    <td style="font-size:11px;font-family:monospace;color:var(--text-secondary);">{{ $payout->tx_id ?: '—' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        {{-- ══════════════════════════════════
             TAB: REPORTS
        ══════════════════════════════════ --}}
        @elseif($tab === 'reports')
            <div style="margin-bottom:24px;display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                <div>
                    <h1 style="font-size:24px;font-weight:800;color:var(--text-primary);margin:0 0 4px;">System Reports</h1>
                    <p style="font-size:13px;color:var(--text-secondary);margin:0;">Platform health audit — Generated {{ now()->format('M d, Y H:i') }}</p>
                </div>
                <button onclick="window.print()" class="btn btn-primary no-print">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8" stroke-linecap="round"/></svg>
                    Print Report
                </button>
            </div>

            <div id="reportsPrintArea">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;margin-bottom:24px;">
                    <div class="stat-card blue">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-secondary);margin-bottom:6px;">Total Accounts</div>
                        <div style="font-size:30px;font-weight:800;color:var(--text-primary);">{{ $totalStudents + $totalInstructors + $totalModerators + 1 }}</div>
                        <div style="font-size:11.5px;color:var(--text-secondary);margin-top:6px;">{{ $totalStudents }} Students · {{ $totalInstructors }} Instructors · {{ $totalModerators }} Mods</div>
                    </div>
                    <div class="stat-card teal">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-secondary);margin-bottom:6px;">Avg Quiz Pass Rate</div>
                        <div style="font-size:30px;font-weight:800;color:var(--text-primary);">{{ $avgQuizScore }}<span style="font-size:16px;">%</span></div>
                        <div class="progress-bar" style="margin-top:10px;">
                            <div class="progress-fill" style="width:{{ min($avgQuizScore,100) }}%;background:linear-gradient(90deg,#14b8a6,#06b6d4);"></div>
                        </div>
                    </div>
                    <div class="stat-card pink">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-secondary);margin-bottom:6px;">Certificates Issued</div>
                        <div style="font-size:30px;font-weight:800;color:#10b981;">{{ $certificatesCount }}</div>
                        <div style="font-size:11.5px;color:var(--text-secondary);margin-top:6px;">Verified course completions</div>
                    </div>
                    <div class="stat-card amber">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-secondary);margin-bottom:6px;">Lesson Completions</div>
                        <div style="font-size:30px;font-weight:800;color:#f59e0b;">{{ $lessonCompletions }}</div>
                        <div style="font-size:11.5px;color:var(--text-secondary);margin-top:6px;">Individual lessons marked done</div>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:24px;">
                    <div class="glass-panel" style="padding:22px;">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-secondary);margin-bottom:6px;">Approved Payout Volume</div>
                        <div style="font-size:28px;font-weight:800;color:#ec4899;">${{ number_format($totalPayoutVolume, 2) }}</div>
                        <div style="font-size:12px;color:var(--text-secondary);margin-top:6px;">${{ number_format($pendingPayoutVolume, 2) }} pending approval</div>
                    </div>
                    <div class="glass-panel" style="padding:22px;">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-secondary);margin-bottom:8px;">Ledger Status</div>
                        <span class="badge badge-success" style="font-size:13px;padding:6px 14px;">Active — Verified</span>
                        <div style="font-size:11px;color:var(--text-secondary);margin-top:10px;font-family:monospace;">ID: {{ md5(date('Ymd')) }}</div>
                    </div>
                </div>

                <div class="glass-panel" style="padding:22px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <div style="font-size:12.5px;font-weight:600;color:var(--text-secondary);">Audit Certification</div>
                            <div style="font-size:13px;color:var(--text-primary);margin-top:3px;">AetherLMS Platform Health Report</div>
                        </div>
                        <div style="text-align:center;border-top:1.5px solid var(--card-border);padding-top:6px;width:140px;">
                            <div style="font-size:10px;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-secondary);">Platform Administrator</div>
                        </div>
                    </div>
                </div>
            </div>

        {{-- ══════════════════════════════════
             TAB: SETTINGS
        ══════════════════════════════════ --}}
        @elseif($tab === 'settings')
            <div style="margin-bottom:24px;">
                <h1 style="font-size:24px;font-weight:800;color:var(--text-primary);margin:0 0 4px;">Site Configuration</h1>
                <p style="font-size:13px;color:var(--text-secondary);margin:0;">Manage global platform settings and feature flags.</p>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;max-width:900px;">
                {{-- Settings form --}}
                <div class="glass-panel" style="padding:26px;grid-column:1/-1;">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
                            <div>
                                <label class="form-label" for="site_name">Site Name</label>
                                <input class="form-input" type="text" name="site_name" id="site_name" value="{{ $settings['site_name'] }}" required>
                            </div>
                            <div>
                                <label class="form-label" for="site_tagline">Brand Tagline</label>
                                <input class="form-input" type="text" name="site_tagline" id="site_tagline" value="{{ $settings['site_tagline'] }}" required>
                            </div>
                            <div>
                                <label class="form-label" for="contact_email">Support Email</label>
                                <input class="form-input" type="email" name="contact_email" id="contact_email" value="{{ $settings['contact_email'] }}" required>
                            </div>
                            <div>
                                <label class="form-label" for="commission_rate">Platform Commission (%)</label>
                                <input class="form-input" type="number" step="0.01" name="commission_rate" id="commission_rate" value="{{ $settings['commission_rate'] }}" min="0" max="100" required>
                            </div>
                            <div>
                                <label class="form-label" for="enable_registration">Registration System</label>
                                <select name="enable_registration" id="enable_registration" class="form-input" style="cursor:pointer;">
                                    <option value="true" {{ $settings['enable_registration']==='true'?'selected':'' }}>Open — Allow New Users</option>
                                    <option value="false" {{ $settings['enable_registration']==='false'?'selected':'' }}>Closed — Disable Registration</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label" for="maintenance_mode">Maintenance Mode</label>
                                <select name="maintenance_mode" id="maintenance_mode" class="form-input" style="cursor:pointer;">
                                    <option value="false" {{ $settings['maintenance_mode']==='false'?'selected':'' }}>Live — Normal Operation</option>
                                    <option value="true" {{ $settings['maintenance_mode']==='true'?'selected':'' }}>Offline — Under Maintenance</option>
                                </select>
                            </div>
                        </div>
                        <div style="display:flex;justify-content:flex-end;gap:10px;border-top:1px solid var(--card-border);padding-top:18px;">
                            <button type="reset" class="btn btn-ghost">Reset</button>
                            <button type="submit" class="btn btn-primary">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                Save Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>{{-- /content --}}
</div>{{-- /admin-main --}}

<script>
    // Mobile sidebar
    function openSidebar() {
        document.getElementById('admin-sidebar').classList.add('open');
        document.getElementById('sidebar-overlay').style.display = 'block';
    }
    function closeSidebar() {
        document.getElementById('admin-sidebar').classList.remove('open');
        document.getElementById('sidebar-overlay').style.display = 'none';
    }

    // Show hamburger on mobile
    function checkMobile() {
        const hamburger = document.getElementById('hamburger-btn');
        if (window.innerWidth <= 900) {
            hamburger.style.display = 'flex';
        } else {
            hamburger.style.display = 'none';
            document.getElementById('admin-sidebar').classList.remove('open');
            document.getElementById('sidebar-overlay').style.display = 'none';
        }
    }
    checkMobile();
    window.addEventListener('resize', checkMobile);

    // Theme toggler
    const themeToggler = document.getElementById('sidebarThemeToggler');
    if (themeToggler) {
        themeToggler.addEventListener('click', () => {
            const current = document.documentElement.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
        });
    }

    // Auto-dismiss flash
    setTimeout(() => {
        document.querySelectorAll('.flash-alert').forEach(el => {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        });
    }, 4000);
</script>

</body>
</html>
