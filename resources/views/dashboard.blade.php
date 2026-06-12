@extends('layouts.app')

@section('title', 'My Learning Hub — AetherLMS')

@section('styles')
<style>
/* ════════════════════════════════════════
   STUDENT DASHBOARD STYLES
════════════════════════════════════════ */

/* Page header */
.dash-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 32px;
}
.dash-greeting {
    font-size: clamp(22px, 3vw, 30px);
    font-weight: 900;
    letter-spacing: -0.03em;
    color: var(--text-primary);
    margin: 0 0 4px;
}
.dash-sub {
    font-size: 14px;
    color: var(--text-secondary);
    margin: 0;
}
.dash-time-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
}

/* Stat cards */
.stat-card {
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
    border-radius: 18px;
    padding: 22px 24px;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    position: relative;
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 16px 40px -10px rgba(0,0,0,0.15);
    border-color: rgba(99,102,241,0.3);
}
[data-theme="dark"] .stat-card:hover {
    box-shadow: 0 16px 40px -10px rgba(0,0,0,0.5);
}
.stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    right: 0; height: 3px;
    border-radius: 18px 18px 0 0;
}
.stat-card.indigo::before  { background: linear-gradient(90deg, #6366f1, #8b5cf6); }
.stat-card.emerald::before { background: linear-gradient(90deg, #10b981, #14b8a6); }
.stat-card.rose::before    { background: linear-gradient(90deg, #f43f5e, #ec4899); }
.stat-card.amber::before   { background: linear-gradient(90deg, #f59e0b, #f97316); }

.stat-icon {
    width: 44px; height: 44px; border-radius: 13px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 16px; flex-shrink: 0;
}
.stat-icon.indigo  { background: rgba(99,102,241,0.12); color: #6366f1; }
.stat-icon.emerald { background: rgba(16,185,129,0.12);  color: #10b981; }
.stat-icon.rose    { background: rgba(244,63,94,0.12);   color: #f43f5e; }
.stat-icon.amber   { background: rgba(245,158,11,0.12);  color: #f59e0b; }

.stat-value {
    font-size: 32px; font-weight: 900;
    letter-spacing: -0.04em; color: var(--text-primary);
    line-height: 1; margin-bottom: 4px;
}
.stat-label {
    font-size: 11.5px; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.06em;
    color: var(--text-secondary);
}

/* Section headers */
.section-hd {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 18px; gap: 12px; flex-wrap: wrap;
}
.section-title {
    font-size: 18px; font-weight: 800;
    color: var(--text-primary); margin: 0;
    letter-spacing: -0.02em;
}
.section-badge {
    display: inline-flex;
    padding: 4px 12px; border-radius: 999px;
    font-size: 11px; font-weight: 700; letter-spacing: 0.05em;
    text-transform: uppercase;
    background: rgba(99,102,241,0.1); color: #6366f1;
    border: 1px solid rgba(99,102,241,0.2);
}

/* Glass panel base */
.glass {
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
    border-radius: 18px;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
}

/* Progress bar */
.prog-track {
    height: 7px; border-radius: 999px;
    background: var(--border-color); overflow: hidden;
    flex: 1; min-width: 100px;
}
.prog-fill {
    height: 100%; border-radius: 999px;
    background: linear-gradient(90deg, #6366f1, #8b5cf6, #ec4899);
    transition: width 0.8s cubic-bezier(.4,0,.2,1);
}

/* Course row (enrolled) */
.enrolled-row {
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 20px 22px;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    display: flex; flex-wrap: wrap;
    align-items: center; gap: 18px;
    transition: all 0.25s ease;
}
.enrolled-row:hover {
    border-color: rgba(99,102,241,0.3);
    box-shadow: 0 8px 24px -8px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}
[data-theme="dark"] .enrolled-row:hover {
    box-shadow: 0 8px 24px -8px rgba(0,0,0,0.4);
}

/* Course card (available) */
.avail-card {
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
    border-radius: 18px;
    overflow: hidden;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    display: flex; flex-direction: column;
    transition: all 0.3s cubic-bezier(.4,0,.2,1);
}
.avail-card:hover {
    transform: translateY(-4px);
    border-color: rgba(99,102,241,0.3);
    box-shadow: 0 16px 40px -10px rgba(0,0,0,0.15);
}
[data-theme="dark"] .avail-card:hover {
    box-shadow: 0 16px 40px -10px rgba(0,0,0,0.5);
}
.avail-thumb {
    width: 100%; height: 160px; overflow: hidden;
    background: linear-gradient(135deg,rgba(99,102,241,0.15),rgba(236,72,153,0.1));
    flex-shrink: 0; position: relative;
}
.avail-thumb img { width:100%;height:100%;object-fit:cover;transition:transform 0.4s ease; }
.avail-card:hover .avail-thumb img { transform: scale(1.05); }

/* Certificate card */
.cert-card {
    background: var(--bg-surface);
    border: 1px solid rgba(16,185,129,0.25);
    border-radius: 18px;
    padding: 22px;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    background-image: linear-gradient(135deg, transparent 60%, rgba(16,185,129,0.04) 100%);
    transition: all 0.25s ease;
    display: flex; flex-direction: column;
}
.cert-card:hover {
    transform: translateY(-3px);
    border-color: rgba(16,185,129,0.5);
    box-shadow: 0 12px 32px -8px rgba(16,185,129,0.15);
}

/* Buttons */
.btn { display:inline-flex;align-items:center;justify-content:center;gap:6px;border-radius:10px;font-weight:700;font-family:inherit;cursor:pointer;transition:all 0.2s ease;text-decoration:none;border:none; }
.btn:hover { transform: translateY(-1.5px); }
.btn-primary { background: linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; padding:9px 20px; font-size:13px; box-shadow: 0 4px 14px rgba(99,102,241,0.3); }
.btn-primary:hover { box-shadow: 0 8px 20px rgba(99,102,241,0.45); background: linear-gradient(135deg,#4f46e5,#7c3aed); }
.btn-ghost { background:transparent; border:1.5px solid var(--border-color); color:var(--text-primary); padding:8px 18px; font-size:13px; }
.btn-ghost:hover { border-color:rgba(99,102,241,0.4); color:#6366f1; background:rgba(99,102,241,0.05); }
.btn-sm { padding:6px 14px; font-size:12px; border-radius:8px; }

/* Empty state */
.empty-state {
    text-align:center; padding:52px 24px;
    border-radius:18px;
    background:var(--bg-surface);
    border:1px dashed var(--border-color);
    backdrop-filter:blur(12px);
}
</style>
@endsection

@section('content')

{{-- ── Page Header ── --}}
<div class="dash-header">
    <div>
        <h1 class="dash-greeting">
            Welcome back, {{ explode(' ', auth()->user()->name)[0] }}! 👋
        </h1>
        <p class="dash-sub">Here's your personal learning hub. Keep up the great work!</p>
    </div>
    <div class="dash-time-badge">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        {{ now()->format('l, M j') }}
    </div>
</div>

{{-- ── Stat Cards ── --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:18px;margin-bottom:36px;">
    <div class="stat-card indigo">
        <div class="stat-icon indigo">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z" stroke-linecap="round" stroke-linejoin="round"/><path d="M6 6h10M6 10h10" stroke-linecap="round"/></svg>
        </div>
        <div class="stat-value">{{ count($enrolledCourses) }}</div>
        <div class="stat-label">Courses in Progress</div>
    </div>
    <div class="stat-card emerald">
        <div class="stat-icon emerald">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <div class="stat-value">{{ $certificates->count() }}</div>
        <div class="stat-label">Certificates Earned</div>
    </div>
    <div class="stat-card rose">
        <div class="stat-icon rose">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <div class="stat-value">{{ count($availableCourses) }}</div>
        <div class="stat-label">Courses Available</div>
    </div>
    <div class="stat-card amber">
        <div class="stat-icon amber">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
        @php
            $avgProgress = count($enrolledCourses) > 0
                ? round(collect($enrolledCourses)->avg('progress'))
                : 0;
        @endphp
        <div class="stat-value">{{ $avgProgress }}<span style="font-size:18px;font-weight:700;color:var(--text-secondary);">%</span></div>
        <div class="stat-label">Avg. Progress</div>
    </div>
</div>

{{-- ── Enrolled / In-Progress Courses ── --}}
<section style="margin-bottom:40px;">
    <div class="section-hd">
        <h2 class="section-title">My Enrolled Courses</h2>
        @if(!empty($enrolledCourses))
            <span class="section-badge">{{ count($enrolledCourses) }} Active</span>
        @endif
    </div>

    @if(empty($enrolledCourses))
        <div class="empty-state">
            <svg style="width:52px;height:52px;margin:0 auto 14px;opacity:0.2;color:var(--text-secondary);" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/></svg>
            <h3 style="font-size:17px;font-weight:700;color:var(--text-primary);margin:0 0 8px;">No courses started yet</h3>
            <p style="font-size:13.5px;color:var(--text-secondary);margin:0 0 20px;">Pick a course from below and begin your learning journey.</p>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:14px;">
            @foreach($enrolledCourses as $data)
            <div class="enrolled-row">
                {{-- Course info --}}
                <div style="flex:1;min-width:240px;">
                    <div style="font-size:15px;font-weight:800;color:var(--text-primary);margin-bottom:4px;line-height:1.3;">
                        {{ $data['course']->title }}
                    </div>
                    <div style="font-size:12px;color:var(--text-secondary);margin-bottom:12px;">
                        By <strong style="color:var(--text-primary);font-weight:600;">{{ $data['course']->instructor->name }}</strong>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="prog-track">
                            <div class="prog-fill" style="width:{{ $data['progress'] }}%;"></div>
                        </div>
                        <span style="font-size:12.5px;font-weight:800;color:var(--text-primary);white-space:nowrap;">{{ $data['progress'] }}%</span>
                    </div>
                </div>

                {{-- Grade + Actions --}}
                <div style="display:flex;align-items:center;gap:14px;flex-shrink:0;flex-wrap:wrap;">
                    @if($data['has_quizzes'])
                    <div style="text-align:center;padding:10px 16px;border-radius:12px;background:{{ $data['grade'] >= 70 ? 'rgba(16,185,129,0.1)' : 'rgba(245,158,11,0.1)' }};border:1px solid {{ $data['grade'] >= 70 ? 'rgba(16,185,129,0.2)' : 'rgba(245,158,11,0.2)' }};">
                        <div style="font-size:18px;font-weight:900;color:{{ $data['grade'] >= 70 ? '#10b981' : '#f59e0b' }};line-height:1;">{{ $data['grade'] }}%</div>
                        <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-secondary);margin-top:2px;">Grade</div>
                    </div>
                    @else
                    <div style="text-align:center;padding:10px 16px;border-radius:12px;background:var(--border-color);">
                        <div style="font-size:18px;font-weight:900;color:var(--text-secondary);">N/A</div>
                        <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-secondary);margin-top:2px;">No Quiz</div>
                    </div>
                    @endif

                    <div style="display:flex;align-items:center;gap:8px;">
                        <a href="{{ route('courses.show', $data['course']->id) }}" class="btn btn-ghost btn-sm">Overview</a>
                        <a href="{{ route('courses.play', $data['course']->id) }}" class="btn btn-primary btn-sm">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                            Resume
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</section>

{{-- ── Available Courses ── --}}
@if(!empty($availableCourses))
<section style="margin-bottom:40px;">
    <div class="section-hd">
        <h2 class="section-title">Explore More Courses</h2>
        <span class="section-badge">{{ count($availableCourses) }} New</span>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:18px;">
        @foreach($availableCourses as $data)
        <div class="avail-card">
            <div class="avail-thumb">
                @if($data['course']->thumbnail)
                    <img src="{{ $data['course']->thumbnail }}" alt="{{ $data['course']->title }}" loading="lazy">
                @else
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:40px;height:40px;opacity:0.2;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/></svg>
                    </div>
                @endif
                <div style="position:absolute;top:12px;right:12px;">
                    <span style="background:rgba(99,102,241,0.85);color:#fff;padding:3px 10px;border-radius:7px;font-size:11.5px;font-weight:800;backdrop-filter:blur(8px);">
                        {{ $data['course']->price > 0 ? '$'.number_format($data['course']->price, 2) : 'Free' }}
                    </span>
                </div>
            </div>
            <div style="padding:18px;flex:1;display:flex;flex-direction:column;">
                <div style="font-size:15px;font-weight:800;color:var(--text-primary);margin-bottom:6px;line-height:1.35;">{{ $data['course']->title }}</div>
                <div style="font-size:12.5px;color:var(--text-secondary);flex:1;line-height:1.6;margin-bottom:14px;">{{ Str::limit($data['course']->description, 90) }}</div>
                <div style="display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border-color);padding-top:12px;">
                    <div style="display:flex;align-items:center;gap:7px;">
                        <div style="width:26px;height:26px;border-radius:8px;background:linear-gradient(135deg,rgba(99,102,241,0.2),rgba(236,72,153,0.15));display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:800;color:#6366f1;">
                            {{ strtoupper(substr($data['course']->instructor->name, 0, 2)) }}
                        </div>
                        <span style="font-size:12px;color:var(--text-secondary);font-weight:600;">{{ $data['course']->instructor->name }}</span>
                    </div>
                    <a href="{{ route('courses.show', $data['course']->id) }}" class="btn btn-primary btn-sm">
                        Enroll
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif

{{-- ── Certificates ── --}}
<section style="margin-bottom:40px;">
    <div class="section-hd">
        <h2 class="section-title">My Certificates</h2>
        @if($certificates->count())
            <span class="section-badge" style="background:rgba(16,185,129,0.1);color:#10b981;border-color:rgba(16,185,129,0.25);">{{ $certificates->count() }} Earned</span>
        @endif
    </div>

    @if($certificates->isEmpty())
        <div class="empty-state">
            <svg style="width:52px;height:52px;margin:0 auto 14px;opacity:0.2;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
            <h3 style="font-size:17px;font-weight:700;color:var(--text-primary);margin:0 0 8px;">No certificates yet</h3>
            <p style="font-size:13.5px;color:var(--text-secondary);margin:0;">Complete 100% of a course with a passing grade to earn your certificate!</p>
        </div>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:18px;">
            @foreach($certificates as $cert)
            <div class="cert-card">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                    <div style="width:38px;height:38px;border-radius:11px;background:rgba(16,185,129,0.12);display:flex;align-items:center;justify-content:center;color:#10b981;flex-shrink:0;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <span style="display:inline-flex;align-items:center;padding:4px 10px;border-radius:7px;background:rgba(16,185,129,0.12);color:#10b981;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;">Verified</span>
                </div>
                <div style="font-size:15px;font-weight:800;color:var(--text-primary);margin-bottom:6px;line-height:1.35;">{{ $cert->course->title }}</div>
                <div style="font-size:12px;color:var(--text-secondary);margin-bottom:16px;font-family:monospace;">
                    Code: <span style="background:var(--border-color);padding:2px 7px;border-radius:5px;font-size:11px;">{{ $cert->verification_code }}</span>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;border-top:1px solid rgba(16,185,129,0.15);padding-top:12px;margin-top:auto;">
                    <span style="font-size:12px;color:var(--text-secondary);">
                        <svg style="width:12px;height:12px;display:inline;margin-right:4px;vertical-align:-1px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        {{ $cert->issued_at->format('M d, Y') }}
                    </span>
                    <a href="{{ route('courses.certificate', $cert->course_id) }}" class="btn btn-ghost btn-sm" target="_blank">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        View & Print
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</section>

@endsection

@section('scripts')
<script>
// Animate stat cards on load
document.querySelectorAll('.stat-card').forEach((el, i) => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(16px)';
    el.style.transition = `opacity 0.4s ease ${i * 0.08}s, transform 0.4s ease ${i * 0.08}s`;
    requestAnimationFrame(() => {
        el.style.opacity = '1';
        el.style.transform = 'translateY(0)';
    });
});

// Animate progress bars
document.querySelectorAll('.prog-fill').forEach(bar => {
    const target = bar.style.width;
    bar.style.width = '0';
    setTimeout(() => { bar.style.width = target; }, 300);
});
</script>
@endsection
