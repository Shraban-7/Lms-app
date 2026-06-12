@extends('layouts.app')

@section('title', 'AetherLMS — Premium Interactive Learning Platform')

@section('styles')
<style>
/* ══════════════════════════════════════
   LANDING PAGE STYLES
══════════════════════════════════════ */

/* Animated noise + gradient hero */
.hero-section {
    position: relative;
    padding: 100px 0 80px;
    overflow: hidden;
    text-align: center;
}

/* Floating orbs */
.orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    pointer-events: none;
    animation: orbFloat 8s ease-in-out infinite;
}
.orb-1 {
    width: 600px; height: 600px;
    top: -200px; left: -100px;
    background: radial-gradient(circle, rgba(99,102,241,0.18) 0%, transparent 70%);
    animation-delay: 0s;
}
.orb-2 {
    width: 500px; height: 500px;
    top: -100px; right: -150px;
    background: radial-gradient(circle, rgba(236,72,153,0.14) 0%, transparent 70%);
    animation-delay: -3s;
}
.orb-3 {
    width: 400px; height: 400px;
    bottom: -100px; left: 30%;
    background: radial-gradient(circle, rgba(20,184,166,0.1) 0%, transparent 70%);
    animation-delay: -5s;
}
@keyframes orbFloat {
    0%, 100% { transform: translateY(0px) scale(1); }
    50% { transform: translateY(-30px) scale(1.05); }
}

/* Badge pill */
.hero-badge {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 6px 16px;
    border-radius: 999px;
    background: rgba(99,102,241,0.1);
    border: 1px solid rgba(99,102,241,0.25);
    font-size: 12.5px;
    font-weight: 700;
    color: #6366f1;
    letter-spacing: 0.03em;
    margin-bottom: 28px;
    animation: fadeInUp 0.6s ease both;
}
[data-theme="dark"] .hero-badge {
    background: rgba(99,102,241,0.15);
    border-color: rgba(99,102,241,0.3);
}
.hero-badge-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: #6366f1;
    animation: pulse 2s ease infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.4); }
}

/* Hero title */
.hero-title {
    font-size: clamp(38px, 5.5vw, 68px);
    font-weight: 900;
    line-height: 1.08;
    letter-spacing: -0.04em;
    margin: 0 0 22px;
    animation: fadeInUp 0.7s ease 0.1s both;
}
.hero-title-plain {
    color: var(--text-primary);
}
.hero-title-gradient {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 40%, #ec4899 80%, #f43f5e 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Hero sub */
.hero-sub {
    font-size: clamp(15px, 1.8vw, 18px);
    color: var(--text-secondary);
    line-height: 1.65;
    max-width: 580px;
    margin: 0 auto 40px;
    animation: fadeInUp 0.7s ease 0.2s both;
}

/* CTA buttons */
.hero-cta {
    display: flex; align-items: center; justify-content: center;
    flex-wrap: wrap; gap: 12px;
    animation: fadeInUp 0.7s ease 0.3s both;
    margin-bottom: 60px;
}
.btn-hero-primary {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 14px 28px;
    border-radius: 12px;
    font-size: 15px; font-weight: 700;
    color: #fff;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    box-shadow: 0 8px 24px rgba(99,102,241,0.38);
    text-decoration: none;
    transition: all 0.25s ease;
    border: none; cursor: pointer;
}
.btn-hero-primary:hover {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    transform: translateY(-2px);
    box-shadow: 0 12px 32px rgba(99,102,241,0.5);
}
.btn-hero-secondary {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 13px 24px;
    border-radius: 12px;
    font-size: 15px; font-weight: 600;
    color: var(--text-primary);
    background: transparent;
    border: 1.5px solid var(--border-color);
    text-decoration: none;
    transition: all 0.25s ease;
}
.btn-hero-secondary:hover {
    border-color: rgba(99,102,241,0.4);
    background: rgba(99,102,241,0.06);
    transform: translateY(-2px);
}

/* Social proof strip */
.social-proof {
    display: flex; align-items: center; justify-content: center;
    gap: 6px;
    font-size: 13px; color: var(--text-secondary);
    animation: fadeInUp 0.7s ease 0.4s both;
    margin-bottom: 80px;
}
.stars { color: #f59e0b; letter-spacing: -1px; }
.avatar-stack { display:flex; }
.avatar-stack span {
    width: 28px; height: 28px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 10px; font-weight: 800;
    border: 2px solid var(--bg-main);
    margin-left: -8px;
    flex-shrink: 0;
}
.avatar-stack span:first-child { margin-left: 0; }

/* Stats row */
.stats-row {
    display: flex; align-items: center; justify-content: center;
    flex-wrap: wrap; gap: 0;
    border-top: 1px solid var(--border-color);
    border-bottom: 1px solid var(--border-color);
    padding: 0;
    margin-bottom: 90px;
    animation: fadeInUp 0.7s ease 0.5s both;
}
.stat-item {
    flex: 1; min-width: 160px;
    padding: 28px 20px;
    text-align: center;
    border-right: 1px solid var(--border-color);
}
.stat-item:last-child { border-right: none; }
.stat-number {
    font-size: 32px; font-weight: 900;
    letter-spacing: -0.04em;
    background: linear-gradient(135deg, #6366f1, #ec4899);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-clip: text;
}
.stat-label { font-size: 13px; color: var(--text-secondary); font-weight: 500; margin-top: 3px; }

/* Features section */
.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
    margin-bottom: 90px;
}
.feature-card {
    padding: 28px;
    border-radius: 18px;
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}
.feature-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: linear-gradient(135deg, transparent 60%, var(--feature-glow, rgba(99,102,241,0.04)) 100%);
    pointer-events: none;
}
.feature-card:hover {
    transform: translateY(-4px);
    border-color: rgba(99,102,241,0.25);
    box-shadow: 0 16px 40px -10px rgba(0,0,0,0.12);
}
[data-theme="dark"] .feature-card:hover {
    box-shadow: 0 16px 40px -10px rgba(0,0,0,0.4);
}
.feature-icon {
    width: 48px; height: 48px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 18px;
    font-size: 22px;
}
.feature-title { font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px; }
.feature-desc  { font-size: 13.5px; color: var(--text-secondary); line-height: 1.6; }

/* Section heading */
.section-header {
    text-align: center;
    margin-bottom: 50px;
}
.section-badge {
    display: inline-flex; align-items: center;
    padding: 5px 14px; border-radius: 999px;
    font-size: 12px; font-weight: 700; letter-spacing: 0.05em;
    text-transform: uppercase;
    background: rgba(99,102,241,0.1); color: #6366f1;
    border: 1px solid rgba(99,102,241,0.2);
    margin-bottom: 14px;
}
.section-title {
    font-size: clamp(26px, 3.5vw, 40px);
    font-weight: 900; letter-spacing: -0.03em;
    color: var(--text-primary);
    margin: 0 0 12px;
    line-height: 1.15;
}
.section-sub {
    font-size: 15px; color: var(--text-secondary);
    max-width: 520px; margin: 0 auto; line-height: 1.6;
}

/* Course cards */
.courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 22px;
    margin-bottom: 90px;
}
.course-card {
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
    border-radius: 18px;
    overflow: hidden;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    transition: all 0.3s cubic-bezier(.4,0,.2,1);
    display: flex; flex-direction: column;
}
.course-card:hover {
    transform: translateY(-5px);
    border-color: rgba(99,102,241,0.3);
    box-shadow: 0 20px 50px -12px rgba(0,0,0,0.15);
}
[data-theme="dark"] .course-card:hover {
    box-shadow: 0 20px 50px -12px rgba(0,0,0,0.5);
}
.course-thumb {
    position: relative;
    width: 100%; height: 188px;
    overflow: hidden;
    flex-shrink: 0;
}
.course-thumb img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}
.course-card:hover .course-thumb img { transform: scale(1.06); }
.course-price-badge {
    position: absolute; top: 14px; right: 14px;
    padding: 4px 12px;
    border-radius: 8px;
    font-size: 12px; font-weight: 800;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}
.course-price-paid {
    background: rgba(99,102,241,0.85);
    color: #fff;
}
.course-price-free {
    background: rgba(16,185,129,0.85);
    color: #fff;
}
.course-body { padding: 22px; flex: 1; display: flex; flex-direction: column; }
.course-title {
    font-size: 16px; font-weight: 800;
    color: var(--text-primary);
    margin: 0 0 8px; line-height: 1.35;
}
.course-desc {
    font-size: 13px; color: var(--text-secondary);
    line-height: 1.6; margin: 0 0 18px; flex: 1;
}
.course-footer {
    display: flex; align-items: center; justify-content: space-between;
    border-top: 1px solid var(--border-color);
    padding-top: 14px;
    gap: 8px;
}
.course-author {
    display: flex; align-items: center; gap: 7px;
}
.course-author-avatar {
    width: 26px; height: 26px; border-radius: 7px;
    background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(236,72,153,0.2));
    display: flex; align-items: center; justify-content: center;
    font-size: 9px; font-weight: 800; color: #6366f1; flex-shrink: 0;
}
.course-author-name { font-size: 12px; color: var(--text-secondary); font-weight: 600; }
.btn-course {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 8px 16px; border-radius: 9px;
    font-size: 12.5px; font-weight: 700;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff; text-decoration: none;
    transition: all 0.2s ease;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(99,102,241,0.3);
}
.btn-course:hover { transform: translateY(-1.5px); box-shadow: 0 6px 18px rgba(99,102,241,0.42); }

/* CTA bottom banner */
.cta-banner {
    border-radius: 24px;
    padding: 64px 48px;
    text-align: center;
    margin-bottom: 60px;
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #db2777 100%);
}
.cta-banner::before {
    content: '';
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    pointer-events: none;
}
.cta-banner-title {
    font-size: clamp(28px, 4vw, 44px);
    font-weight: 900; letter-spacing: -0.03em;
    color: #fff; margin: 0 0 14px; line-height: 1.15;
}
.cta-banner-sub {
    font-size: 16px; color: rgba(255,255,255,0.75);
    margin: 0 0 36px; line-height: 1.6;
}
.btn-cta-white {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 14px 30px; border-radius: 12px;
    font-size: 15px; font-weight: 700;
    background: #fff; color: #4f46e5;
    text-decoration: none;
    transition: all 0.25s ease;
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
}
.btn-cta-white:hover { transform: translateY(-2px); box-shadow: 0 14px 36px rgba(0,0,0,0.3); }

/* Fade in animation */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0);    }
}
.animate-in {
    animation: fadeInUp 0.6s ease both;
}

/* Empty state */
.empty-state {
    text-align: center; padding: 64px 24px;
    border-radius: 18px;
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
    backdrop-filter: blur(12px);
}
</style>
@endsection

@section('content')

{{-- ══════════════════════════════════════
     HERO SECTION
══════════════════════════════════════ --}}
<header class="hero-section">
    {{-- Floating orbs --}}
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    {{-- Announcement badge --}}
    <div class="hero-badge">
        <span class="hero-badge-dot"></span>
        Powered by modern learning science — Now live
    </div>

    {{-- Main headline --}}
    <h1 class="hero-title">
        <span class="hero-title-plain">Unlock Your Potential<br>with </span><span class="hero-title-gradient">Expert-Led Courses</span>
    </h1>

    {{-- Subheadline --}}
    <p class="hero-sub">
        A premium learning platform with interactive video lessons, weighted quizzes, real-time progress tracking, collaborative forums, and automated certificate issuance.
    </p>

    {{-- CTAs --}}
    <div class="hero-cta">
        @guest
            <a href="{{ route('register') }}" class="btn-hero-primary">
                Start Learning Free
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
            <a href="{{ route('login') }}" class="btn-hero-secondary">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                Sign in to continue
            </a>
        @else
            <a href="{{ route('dashboard') }}" class="btn-hero-primary">
                Go to My Dashboard
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        @endguest
    </div>

    {{-- Social proof --}}
    <div class="social-proof">
        <div class="avatar-stack">
            <span style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;">AL</span>
            <span style="background:linear-gradient(135deg,#ec4899,#f43f5e);color:#fff;">MK</span>
            <span style="background:linear-gradient(135deg,#14b8a6,#06b6d4);color:#fff;">JR</span>
            <span style="background:linear-gradient(135deg,#f59e0b,#f97316);color:#fff;">SP</span>
        </div>
        <span class="stars">★★★★★</span>
        <span>Trusted by <strong style="color:var(--text-primary);">{{ $courses->count() * 12 + 248 }}</strong> learners worldwide</span>
    </div>
</header>

{{-- ══════════════════════════════════════
     STATS
══════════════════════════════════════ --}}
<div class="stats-row">
    <div class="stat-item">
        <div class="stat-number">{{ $courses->count() }}+</div>
        <div class="stat-label">Expert-led courses</div>
    </div>
    <div class="stat-item">
        <div class="stat-number">100%</div>
        <div class="stat-label">Verified certificates</div>
    </div>
    <div class="stat-item">
        <div class="stat-number">∞</div>
        <div class="stat-label">Lifetime access</div>
    </div>
    <div class="stat-item">
        <div class="stat-number">4.9★</div>
        <div class="stat-label">Average rating</div>
    </div>
</div>

{{-- ══════════════════════════════════════
     FEATURES
══════════════════════════════════════ --}}
<section style="margin-bottom:90px;">
    <div class="section-header">
        <div class="section-badge">Platform Features</div>
        <h2 class="section-title">Everything you need to<br>master new skills</h2>
        <p class="section-sub">Built from the ground up for serious learners who want measurable results and premium experiences.</p>
    </div>

    <div class="features-grid">
        <div class="feature-card" style="--feature-glow:rgba(99,102,241,0.06);">
            <div class="feature-icon" style="background:rgba(99,102,241,0.12);color:#6366f1;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
            </div>
            <div class="feature-title">Interactive Video Player</div>
            <div class="feature-desc">Custom-built HTML5 video player with progress scrubbing, auto-save playback position, and seamless full-screen support.</div>
        </div>

        <div class="feature-card" style="--feature-glow:rgba(236,72,153,0.06);">
            <div class="feature-icon" style="background:rgba(236,72,153,0.12);color:#ec4899;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            </div>
            <div class="feature-title">Weighted Quizzes & Grading</div>
            <div class="feature-desc">Multi-type questions (MCQ, True/False, Short Answer) with customizable point weights and automatic weighted grade calculation.</div>
        </div>

        <div class="feature-card" style="--feature-glow:rgba(20,184,166,0.06);">
            <div class="feature-icon" style="background:rgba(20,184,166,0.12);color:#14b8a6;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            </div>
            <div class="feature-title">Real-time Progress Tracking</div>
            <div class="feature-desc">Visual progress rings, lesson completion tracking, and course-level completion percentages that update instantly.</div>
        </div>

        <div class="feature-card" style="--feature-glow:rgba(245,158,11,0.06);">
            <div class="feature-icon" style="background:rgba(245,158,11,0.12);color:#f59e0b;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="feature-title">Student Community Forums</div>
            <div class="feature-desc">Per-course discussion boards with threaded replies, letting learners collaborate, ask questions, and share insights.</div>
        </div>

        <div class="feature-card" style="--feature-glow:rgba(139,92,246,0.06);">
            <div class="feature-icon" style="background:rgba(139,92,246,0.12);color:#8b5cf6;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
            </div>
            <div class="feature-title">Automated Certificates</div>
            <div class="feature-desc">Upon completing all lessons and passing graded quizzes, learners receive a unique, printable certificate with a verification code.</div>
        </div>

        <div class="feature-card" style="--feature-glow:rgba(239,68,68,0.06);">
            <div class="feature-icon" style="background:rgba(239,68,68,0.12);color:#ef4444;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/></svg>
            </div>
            <div class="feature-title">Role-Based Access Control</div>
            <div class="feature-desc">Separate portals for Students, Instructors, Moderators, and Administrators — each with purpose-built dashboards and controls.</div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════
     COURSES GRID
══════════════════════════════════════ --}}
<section id="courses" style="margin-bottom:90px;">
    <div style="display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:40px;">
        <div>
            <div class="section-badge" style="margin-bottom:10px;">Course Catalog</div>
            <h2 class="section-title" style="text-align:left;margin-bottom:8px;">Featured Courses</h2>
            <p style="font-size:14px;color:var(--text-secondary);margin:0;">Unlock professional-grade skills and earn verified certificates.</p>
        </div>
        @if(!$courses->isEmpty())
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
            <span style="font-size:13px;color:var(--text-secondary);">{{ $courses->count() }} course{{ $courses->count()===1?'':'s' }} available</span>
            <span style="width:6px;height:6px;border-radius:50%;background:#10b981;display:inline-block;"></span>
        </div>
        @endif
    </div>

    @if($courses->isEmpty())
        <div class="empty-state">
            <svg style="width:56px;height:56px;margin:0 auto 16px;opacity:0.25;color:var(--text-secondary);" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10M6 10h10"/></svg>
            <h3 style="font-size:20px;font-weight:700;color:var(--text-primary);margin:0 0 8px;">No courses yet</h3>
            <p style="font-size:14px;color:var(--text-secondary);margin:0;">Check back soon, or register as an instructor to start building courses.</p>
        </div>
    @else
        <div class="courses-grid">
            @foreach($courses as $course)
            <article class="course-card">
                <div class="course-thumb">
                    @if($course->thumbnail)
                        <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" loading="lazy">
                    @else
                        <div style="width:100%;height:100%;background:linear-gradient(135deg,rgba(99,102,241,0.2),rgba(236,72,153,0.15));display:flex;align-items:center;justify-content:center;">
                            <svg style="width:48px;height:48px;opacity:0.3;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/></svg>
                        </div>
                    @endif
                    <span class="course-price-badge {{ $course->price > 0 ? 'course-price-paid' : 'course-price-free' }}">
                        {{ $course->price > 0 ? '$' . number_format($course->price, 2) : 'Free' }}
                    </span>
                </div>
                <div class="course-body">
                    <h3 class="course-title">{{ $course->title }}</h3>
                    <p class="course-desc">{{ Str::limit($course->description, 110) }}</p>
                    <div class="course-footer">
                        <div class="course-author">
                            <div class="course-author-avatar">{{ strtoupper(substr($course->instructor->name, 0, 2)) }}</div>
                            <span class="course-author-name">{{ $course->instructor->name }}</span>
                        </div>
                        <a href="{{ route('courses.show', $course->id) }}" class="btn-course">
                            View
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
    @endif
</section>

{{-- ══════════════════════════════════════
     CTA BOTTOM BANNER
══════════════════════════════════════ --}}
@guest
<div class="cta-banner">
    <h2 class="cta-banner-title">Ready to start your<br>learning journey?</h2>
    <p class="cta-banner-sub">Join hundreds of learners already advancing their careers with AetherLMS.</p>
    <a href="{{ route('register') }}" class="btn-cta-white">
        Create free account
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </a>
</div>
@endguest

@endsection

@section('scripts')
<script>
// Intersection Observer for scroll animations
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll('.feature-card, .course-card').forEach((el, i) => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(24px)';
    el.style.transition = `opacity 0.5s ease ${i * 0.06}s, transform 0.5s ease ${i * 0.06}s`;
    observer.observe(el);
});
</script>
@endsection
