@extends('layouts.app')

@section('title', 'Create Account — AetherLMS')

@section('styles')
<style>
.auth-wrap { max-width: 500px; margin: 50px auto; }
.auth-card {
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
    border-radius: 22px; padding: 38px 38px;
    backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
    box-shadow: 0 20px 60px -16px rgba(0,0,0,0.12);
    animation: authSlideIn 0.5s cubic-bezier(.4,0,.2,1) both;
}
[data-theme="dark"] .auth-card { box-shadow: 0 20px 60px -16px rgba(0,0,0,0.5); }
@keyframes authSlideIn { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }

.auth-logo { display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:24px; }
.auth-logo-ring { width:44px;height:44px;border-radius:14px;background:linear-gradient(135deg,rgba(99,102,241,0.15),rgba(236,72,153,0.12));border:1px solid rgba(99,102,241,0.3);display:flex;align-items:center;justify-content:center; }
.auth-logo-text { font-size:22px;font-weight:900;letter-spacing:-0.04em;background:linear-gradient(135deg,#6366f1,#8b5cf6,#ec4899);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text; }

.auth-heading { font-size:24px;font-weight:900;letter-spacing:-0.03em;color:var(--text-primary);text-align:center;margin:0 0 6px; }
.auth-sub { font-size:14px;color:var(--text-secondary);text-align:center;margin:0 0 26px; }

.form-group { margin-bottom:15px; }
.form-label { display:block;font-size:12.5px;font-weight:700;color:var(--text-secondary);margin-bottom:7px;letter-spacing:0.03em; }
.form-input { width:100%;padding:11px 14px;background:var(--bg-surface);border:1.5px solid var(--border-color);border-radius:11px;font-size:14px;color:var(--text-primary);outline:none;font-family:inherit;transition:border-color 0.2s,box-shadow 0.2s;box-sizing:border-box; }
.form-input:focus { border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,0.18); }
.form-input::placeholder { color:var(--text-secondary);opacity:0.6; }
.form-error { color:#ef4444;font-size:11.5px;margin-top:5px;display:block;font-weight:600; }

/* Role selector cards */
.role-picker { display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px; }
.role-option { position:relative; }
.role-option input[type="radio"] { position:absolute;opacity:0;width:0;height:0; }
.role-label {
    display:flex;flex-direction:column;align-items:center;gap:6px;
    padding:14px 10px; border-radius:12px;
    border:1.5px solid var(--border-color);
    cursor:pointer; text-align:center;
    transition:all 0.2s ease; background:var(--bg-surface);
}
.role-label:hover { border-color:rgba(99,102,241,0.4); background:rgba(99,102,241,0.04); }
.role-option input:checked + .role-label {
    border-color:#6366f1;
    background:rgba(99,102,241,0.08);
    box-shadow:0 0 0 3px rgba(99,102,241,0.12);
}
.role-icon { width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center; }
.role-name { font-size:13px;font-weight:700;color:var(--text-primary); }
.role-desc { font-size:11px;color:var(--text-secondary);line-height:1.4; }

.btn-auth { width:100%;padding:13px;border-radius:12px;border:none;font-size:14.5px;font-weight:800;font-family:inherit;cursor:pointer;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;box-shadow:0 6px 20px rgba(99,102,241,0.35);transition:all 0.25s ease;display:flex;align-items:center;justify-content:center;gap:8px; }
.btn-auth:hover { background:linear-gradient(135deg,#4f46e5,#7c3aed);transform:translateY(-2px);box-shadow:0 10px 28px rgba(99,102,241,0.45); }

.auth-footer { text-align:center;margin-top:22px;font-size:13.5px;color:var(--text-secondary); }
.auth-footer a { color:#6366f1;font-weight:700;text-decoration:none; }
.auth-footer a:hover { text-decoration:underline; }

.auth-orb { position:fixed;border-radius:50%;pointer-events:none;z-index:-1;filter:blur(70px); }
</style>
@endsection

@section('content')

<div class="auth-orb" style="width:500px;height:500px;top:-100px;right:-100px;background:radial-gradient(circle,rgba(99,102,241,0.12) 0%,transparent 70%);"></div>
<div class="auth-orb" style="width:400px;height:400px;bottom:-80px;left:-80px;background:radial-gradient(circle,rgba(20,184,166,0.09) 0%,transparent 70%);"></div>

<div class="auth-wrap">
    <div class="auth-card">

        {{-- Logo --}}
        <div class="auth-logo">
            <div class="auth-logo-ring">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                     style="stroke:url(#lg-reg);stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round;">
                    <defs><linearGradient id="lg-reg" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#6366f1"/><stop offset="100%" stop-color="#ec4899"/>
                    </linearGradient></defs>
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M6 12.5V17c0 1 3 3 6 3s6-2 6-3v-4.5"/>
                    <path d="M12 12v6"/>
                    <circle cx="12" cy="18" r="1.5" fill="url(#lg-reg)" stroke="none"/>
                </svg>
            </div>
            <span class="auth-logo-text">AetherLMS</span>
        </div>

        <h1 class="auth-heading">Create your account</h1>
        <p class="auth-sub">Join thousands of learners and educators today</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="name">Full Name</label>
                <input class="form-input" type="text" id="name" name="name"
                       value="{{ old('name') }}" placeholder="John Doe" required autofocus>
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input class="form-input" type="email" id="email" name="email"
                       value="{{ old('email') }}" placeholder="you@example.com" required>
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Role picker --}}
            <div class="form-group">
                <label class="form-label">I want to join as</label>
                <div class="role-picker">
                    <div class="role-option">
                        <input type="radio" name="role" id="role-student" value="student"
                               {{ old('role', 'student') === 'student' ? 'checked' : '' }}>
                        <label for="role-student" class="role-label">
                            <div class="role-icon" style="background:rgba(99,102,241,0.12);color:#6366f1;">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 14a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z" stroke-linecap="round" stroke-linejoin="round"/><path d="M17 21v-2a4 4 0 0 0-4-4H11a4 4 0 0 0-4 4v2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <span class="role-name">Student</span>
                            <span class="role-desc">Explore courses & earn certificates</span>
                        </label>
                    </div>
                    <div class="role-option">
                        <input type="radio" name="role" id="role-instructor" value="instructor"
                               {{ old('role') === 'instructor' ? 'checked' : '' }}>
                        <label for="role-instructor" class="role-label">
                            <div class="role-icon" style="background:rgba(236,72,153,0.12);color:#ec4899;">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10M6 10h8"/></svg>
                            </div>
                            <span class="role-name">Instructor</span>
                            <span class="role-desc">Build courses & request payouts</span>
                        </label>
                    </div>
                </div>
                @error('role')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:22px;">
                <div>
                    <label class="form-label" for="password">Password</label>
                    <input class="form-input" type="password" id="password" name="password"
                           placeholder="Min. 8 characters" required>
                    @error('password')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="form-label" for="password_confirmation">Confirm</label>
                    <input class="form-input" type="password" id="password_confirmation"
                           name="password_confirmation" placeholder="Repeat password" required>
                </div>
            </div>

            <button type="submit" class="btn-auth">
                Create my account
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
        </div>
    </div>
</div>
@endsection
