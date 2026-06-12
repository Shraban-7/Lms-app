@extends('layouts.app')

@section('title', 'Sign In — AetherLMS')

@section('styles')
<style>
.auth-wrap {
    max-width: 460px;
    margin: 60px auto;
}
.auth-card {
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
    border-radius: 22px;
    padding: 40px 38px;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    box-shadow: 0 20px 60px -16px rgba(0,0,0,0.12);
    animation: authSlideIn 0.5s cubic-bezier(.4,0,.2,1) both;
}
[data-theme="dark"] .auth-card {
    box-shadow: 0 20px 60px -16px rgba(0,0,0,0.5);
}
@keyframes authSlideIn {
    from { opacity:0; transform: translateY(20px); }
    to   { opacity:1; transform: translateY(0); }
}

.auth-logo {
    display: flex; align-items: center; justify-content: center;
    gap: 10px; margin-bottom: 28px;
}
.auth-logo-ring {
    width: 44px; height: 44px; border-radius: 14px;
    background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(236,72,153,0.12));
    border: 1px solid rgba(99,102,241,0.3);
    display: flex; align-items: center; justify-content: center;
}
.auth-logo-text {
    font-size: 22px; font-weight: 900; letter-spacing: -0.04em;
    background: linear-gradient(135deg, #6366f1, #8b5cf6, #ec4899);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-clip: text;
}

.auth-heading { font-size: 24px; font-weight: 900; letter-spacing: -0.03em; color: var(--text-primary); text-align: center; margin: 0 0 6px; }
.auth-sub { font-size: 14px; color: var(--text-secondary); text-align: center; margin: 0 0 28px; }

.auth-divider {
    display: flex; align-items: center; gap: 12px;
    margin: 22px 0;
    color: var(--text-secondary); font-size: 12px;
}
.auth-divider::before,
.auth-divider::after {
    content: ''; flex: 1; height: 1px;
    background: var(--border-color);
}

.form-group { margin-bottom: 16px; }
.form-label { display: block; font-size: 12.5px; font-weight: 700; color: var(--text-secondary); margin-bottom: 7px; letter-spacing: 0.03em; }
.form-input {
    width: 100%; padding: 11px 14px;
    background: var(--bg-surface);
    border: 1.5px solid var(--border-color);
    border-radius: 11px;
    font-size: 14px; color: var(--text-primary);
    outline: none; font-family: inherit;
    transition: border-color 0.2s, box-shadow 0.2s;
    box-sizing: border-box;
}
.form-input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.18);
}
.form-input::placeholder { color: var(--text-secondary); opacity: 0.6; }
.form-error { color: #ef4444; font-size: 11.5px; margin-top: 5px; display: block; font-weight: 600; }

.btn-auth {
    width: 100%; padding: 13px;
    border-radius: 12px; border: none;
    font-size: 14.5px; font-weight: 800;
    font-family: inherit; cursor: pointer;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff;
    box-shadow: 0 6px 20px rgba(99,102,241,0.35);
    transition: all 0.25s ease;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.btn-auth:hover {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(99,102,241,0.45);
}

.auth-footer {
    text-align: center; margin-top: 24px;
    font-size: 13.5px; color: var(--text-secondary);
}
.auth-footer a { color: #6366f1; font-weight: 700; text-decoration: none; }
.auth-footer a:hover { text-decoration: underline; }

/* Floating orbs */
.auth-orb {
    position: fixed; border-radius: 50%; pointer-events: none; z-index: -1;
    filter: blur(70px);
}
</style>
@endsection

@section('content')

{{-- Orbs --}}
<div class="auth-orb" style="width:500px;height:500px;top:-100px;left:-100px;background:radial-gradient(circle,rgba(99,102,241,0.12) 0%,transparent 70%);"></div>
<div class="auth-orb" style="width:400px;height:400px;bottom:-80px;right:-80px;background:radial-gradient(circle,rgba(236,72,153,0.1) 0%,transparent 70%);"></div>

<div class="auth-wrap">
    <div class="auth-card">

        {{-- Logo --}}
        <div class="auth-logo">
            <div class="auth-logo-ring">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                     style="stroke:url(#lg-login);stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round;">
                    <defs><linearGradient id="lg-login" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#6366f1"/><stop offset="100%" stop-color="#ec4899"/>
                    </linearGradient></defs>
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M6 12.5V17c0 1 3 3 6 3s6-2 6-3v-4.5"/>
                    <path d="M12 12v6"/>
                    <circle cx="12" cy="18" r="1.5" fill="url(#lg-login)" stroke="none"/>
                </svg>
            </div>
            <span class="auth-logo-text">AetherLMS</span>
        </div>

        <h1 class="auth-heading">Welcome back</h1>
        <p class="auth-sub">Sign in to continue your learning journey</p>

        @if(session('error'))
            <div style="padding:12px 16px;border-radius:10px;background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);color:#dc2626;font-size:13px;font-weight:600;margin-bottom:18px;">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email address</label>
                <input class="form-input" type="email" id="email" name="email"
                       value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input class="form-input" type="password" id="password" name="password"
                       placeholder="••••••••" required>
                @error('password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div style="display:flex;align-items:center;gap:8px;margin:14px 0 22px;">
                <input type="checkbox" name="remember" id="remember"
                       style="width:16px;height:16px;accent-color:#6366f1;cursor:pointer;border-radius:4px;">
                <label for="remember" style="font-size:13.5px;color:var(--text-secondary);cursor:pointer;">Keep me signed in</label>
            </div>

            <button type="submit" class="btn-auth">
                Sign in to AetherLMS
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </form>

        <div class="auth-footer">
            New to AetherLMS? <a href="{{ route('register') }}">Create an account</a>
        </div>
    </div>
</div>
@endsection
