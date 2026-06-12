@extends('layouts.app')

@section('title', 'Instructor Workspace — AetherLMS')

@section('styles')
<style>
/* ════════════════════════════════════════
   INSTRUCTOR DASHBOARD STYLES
════════════════════════════════════════ */

/* Shared tokens */
.stat-card {
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
    border-radius: 18px;
    padding: 22px 24px;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    position: relative; overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 16px 40px -10px rgba(0,0,0,0.15); border-color: rgba(99,102,241,0.3); }
[data-theme="dark"] .stat-card:hover { box-shadow: 0 16px 40px -10px rgba(0,0,0,0.5); }
.stat-card::before { content:''; position:absolute; top:0;left:0;right:0;height:3px; border-radius:18px 18px 0 0; }
.stat-card.indigo::before  { background:linear-gradient(90deg,#6366f1,#8b5cf6); }
.stat-card.emerald::before { background:linear-gradient(90deg,#10b981,#14b8a6); }
.stat-card.amber::before   { background:linear-gradient(90deg,#f59e0b,#f97316); }

.stat-icon { width:44px;height:44px;border-radius:13px;display:flex;align-items:center;justify-content:center;margin-bottom:16px; }
.stat-icon.indigo  { background:rgba(99,102,241,0.12);color:#6366f1; }
.stat-icon.emerald { background:rgba(16,185,129,0.12);color:#10b981; }
.stat-icon.amber   { background:rgba(245,158,11,0.12);color:#f59e0b; }
.stat-value { font-size:30px;font-weight:900;letter-spacing:-0.04em;color:var(--text-primary);line-height:1;margin-bottom:4px; }
.stat-label { font-size:11.5px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-secondary); }
.stat-sub   { font-size:11.5px;color:var(--text-secondary);margin-top:6px; }

.glass { background:var(--bg-surface);border:1px solid var(--border-color);border-radius:18px;backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px); }

.section-title { font-size:17px;font-weight:800;color:var(--text-primary);margin:0;letter-spacing:-0.02em; }
.section-badge { display:inline-flex;padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700;letter-spacing:0.05em;text-transform:uppercase;background:rgba(99,102,241,0.1);color:#6366f1;border:1px solid rgba(99,102,241,0.2); }

.btn { display:inline-flex;align-items:center;justify-content:center;gap:6px;border-radius:10px;font-weight:700;font-family:inherit;cursor:pointer;transition:all 0.2s ease;text-decoration:none;border:none; }
.btn:hover { transform:translateY(-1.5px); }
.btn-primary { background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;padding:9px 20px;font-size:13px;box-shadow:0 4px 14px rgba(99,102,241,0.3); }
.btn-primary:hover { box-shadow:0 8px 20px rgba(99,102,241,0.45);background:linear-gradient(135deg,#4f46e5,#7c3aed); }
.btn-ghost { background:transparent;border:1.5px solid var(--border-color);color:var(--text-primary);padding:8px 18px;font-size:13px; }
.btn-ghost:hover { border-color:rgba(99,102,241,0.4);color:#6366f1;background:rgba(99,102,241,0.05); }
.btn-success { background:linear-gradient(135deg,#10b981,#059669);color:#fff;padding:7px 14px;font-size:12px;box-shadow:0 4px 10px rgba(16,185,129,0.25); }
.btn-success:hover { box-shadow:0 6px 16px rgba(16,185,129,0.4); }
.btn-warning { background:rgba(245,158,11,0.1);border:1.5px solid rgba(245,158,11,0.3);color:#f59e0b;padding:7px 14px;font-size:12px; }
.btn-warning:hover { background:#f59e0b;color:#fff;border-color:#f59e0b; }
.btn-sm { padding:6px 14px;font-size:12px;border-radius:8px; }

.form-label { display:block;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-secondary);margin-bottom:7px; }
.form-input { width:100%;padding:10px 14px;background:var(--bg-surface);border:1.5px solid var(--border-color);border-radius:10px;font-size:14px;color:var(--text-primary);outline:none;transition:border-color 0.2s,box-shadow 0.2s;font-family:inherit; }
.form-input:focus { border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,0.18); }

.badge { display:inline-flex;align-items:center;padding:3px 9px;border-radius:6px;font-size:10.5px;font-weight:700;letter-spacing:0.04em;text-transform:uppercase; }
.badge-success { background:rgba(16,185,129,0.12);color:#10b981; }
.badge-warning { background:rgba(245,158,11,0.12);color:#f59e0b; }
.badge-pending { background:rgba(99,102,241,0.12);color:#6366f1; }
.badge-danger  { background:rgba(239,68,68,0.12);color:#ef4444; }

.course-row {
    display:flex;align-items:center;gap:14px;
    padding:16px 18px;border-radius:14px;
    border:1px solid var(--border-color);
    background:var(--bg-surface);
    backdrop-filter:blur(12px);
    transition:all 0.2s ease;
}
.course-row:hover { border-color:rgba(99,102,241,0.3);transform:translateX(3px); }

.payout-row {
    display:flex;align-items:center;justify-content:space-between;
    padding:12px 14px;border-radius:11px;
    border:1px solid var(--border-color);
    background:var(--bg-surface);
    gap:8px;
}

.empty-state { text-align:center;padding:48px 24px;border-radius:18px;background:var(--bg-surface);border:1px dashed var(--border-color);backdrop-filter:blur(12px); }
</style>
@endsection

@section('content')

{{-- ── Header ── --}}
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;margin-bottom:30px;">
    <div>
        <h1 style="font-size:clamp(22px,3vw,30px);font-weight:900;letter-spacing:-0.03em;color:var(--text-primary);margin:0 0 4px;">
            Instructor Workspace
        </h1>
        <p style="font-size:14px;color:var(--text-secondary);margin:0;">Manage your courses, monitor earnings, and request payouts.</p>
    </div>
    <a href="{{ route('builder.create') }}" class="btn btn-primary">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Create New Course
    </a>
</div>

{{-- ── Stats ── --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:18px;margin-bottom:32px;">
    <div class="stat-card indigo">
        <div class="stat-icon indigo">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="20" height="14" x="2" y="5" rx="2"/><path d="M2 10h20"/></svg>
        </div>
        <div class="stat-value">${{ number_format($user->payout_balance, 2) }}</div>
        <div class="stat-label">Available Balance</div>
        <div class="stat-sub">Ready for immediate payout</div>
    </div>
    <div class="stat-card emerald">
        <div class="stat-icon emerald">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="stat-value">${{ number_format($totalApproved, 2) }}</div>
        <div class="stat-label">Approved Payouts</div>
        <div class="stat-sub">Successfully transferred</div>
    </div>
    <div class="stat-card amber">
        <div class="stat-icon amber">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
        </div>
        <div class="stat-value">${{ number_format($lifetimeEarnings, 2) }}</div>
        <div class="stat-label">Lifetime Earnings</div>
        <div class="stat-sub">Gross platform revenue</div>
    </div>
    <div class="stat-card" style="border-top:3px solid transparent;background:var(--bg-surface);border-image:linear-gradient(90deg,#ec4899,#6366f1) 1;">
        <div class="stat-icon" style="background:rgba(236,72,153,0.12);color:#ec4899;">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/></svg>
        </div>
        <div class="stat-value">{{ $courses->count() }}</div>
        <div class="stat-label">Total Courses</div>
        <div class="stat-sub">{{ $courses->where('is_published', true)->count() }} published</div>
    </div>
</div>

{{-- ── Main 2-col grid ── --}}
<div style="display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start;">

    {{-- LEFT: Course list --}}
    <div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;gap:12px;flex-wrap:wrap;">
            <h2 class="section-title">My Courses</h2>
            @if($courses->count())
                <span class="section-badge">{{ $courses->count() }} total</span>
            @endif
        </div>

        @if($courses->isEmpty())
            <div class="empty-state">
                <svg style="width:52px;height:52px;margin:0 auto 14px;opacity:0.2;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/></svg>
                <h3 style="font-size:17px;font-weight:700;color:var(--text-primary);margin:0 0 8px;">No courses yet</h3>
                <p style="font-size:13.5px;color:var(--text-secondary);margin:0 0 20px;">Share your expertise — create your first course now.</p>
                <a href="{{ route('builder.create') }}" class="btn btn-primary">Build First Course</a>
            </div>
        @else
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($courses as $course)
                <div class="course-row">
                    {{-- Thumbnail --}}
                    <div style="width:72px;height:50px;border-radius:10px;overflow:hidden;background:linear-gradient(135deg,rgba(99,102,241,0.15),rgba(236,72,153,0.1));flex-shrink:0;">
                        @if($course->thumbnail)
                            <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                <svg style="width:22px;height:22px;opacity:0.25;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/></svg>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:14px;font-weight:800;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:5px;">
                            {{ $course->title }}
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                            @if($course->is_published)
                                <span class="badge badge-success">Published</span>
                            @else
                                <span class="badge badge-warning">Draft</span>
                            @endif
                            <span style="font-size:11.5px;color:var(--text-secondary);">{{ $course->lessons_count }} lesson{{ $course->lessons_count != 1 ? 's' : '' }}</span>
                            <span style="font-size:11.5px;color:var(--text-secondary);">·</span>
                            <span style="font-size:11.5px;font-weight:700;color:var(--text-primary);">${{ number_format($course->price, 2) }}</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div style="display:flex;align-items:center;gap:7px;flex-shrink:0;">
                        <form action="{{ route('builder.toggle-publish', $course->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn {{ $course->is_published ? 'btn-warning' : 'btn-success' }} btn-sm">
                                {{ $course->is_published ? 'Unpublish' : 'Publish' }}
                            </button>
                        </form>
                        <a href="{{ route('builder.edit', $course->id) }}" class="btn btn-ghost btn-sm">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Edit
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- RIGHT: Payout panel + history --}}
    <div style="display:flex;flex-direction:column;gap:18px;">

        {{-- Payout request card --}}
        <div class="glass" style="padding:24px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                <div style="width:38px;height:38px;border-radius:11px;background:rgba(99,102,241,0.12);display:flex;align-items:center;justify-content:center;color:#6366f1;flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <div>
                    <div style="font-size:15px;font-weight:800;color:var(--text-primary);">Request Payout</div>
                    <div style="font-size:12px;color:var(--text-secondary);">Balance: <strong style="color:#10b981;">${{ number_format($user->payout_balance, 2) }}</strong></div>
                </div>
            </div>

            <form action="{{ route('instructor.request-payout') }}" method="POST">
                @csrf
                <div style="margin-bottom:14px;">
                    <label class="form-label" for="amount">Amount (USD)</label>
                    <input class="form-input" type="number" step="0.01" name="amount" id="amount"
                           value="{{ old('amount', $user->payout_balance) }}" min="10" required>
                    @error('amount')
                        <span style="color:#ef4444;font-size:11.5px;margin-top:5px;display:block;font-weight:600;">{{ $message }}</span>
                    @enderror
                </div>
                <div style="margin-bottom:18px;">
                    <label class="form-label" for="method">Transfer Method</label>
                    <select class="form-input" name="method" id="method" style="cursor:pointer;" required>
                        <option value="bank_transfer">🏦 Bank Transfer</option>
                        <option value="paypal">💳 PayPal</option>
                    </select>
                    @error('method')
                        <span style="color:#ef4444;font-size:11.5px;margin-top:5px;display:block;font-weight:600;">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit"
                        class="btn btn-primary"
                        style="width:100%;justify-content:center;{{ $user->payout_balance < 10 ? 'opacity:0.5;cursor:not-allowed;pointer-events:none;' : '' }}"
                        {{ $user->payout_balance < 10 ? 'disabled' : '' }}>
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Request Transfer
                </button>
                @if($user->payout_balance < 10)
                    <p style="text-align:center;font-size:11.5px;color:var(--text-secondary);margin:10px 0 0;">Minimum payout is $10.00</p>
                @endif
            </form>
        </div>

        {{-- Payout history --}}
        <div class="glass" style="padding:22px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <div style="font-size:15px;font-weight:800;color:var(--text-primary);">Payout History</div>
                @if($payouts->count())
                    <span style="font-size:11px;font-weight:600;color:var(--text-secondary);">{{ $payouts->count() }} transactions</span>
                @endif
            </div>

            @if($payouts->isEmpty())
                <div style="text-align:center;padding:28px 0;color:var(--text-secondary);">
                    <svg style="width:36px;height:36px;margin:0 auto 10px;opacity:0.2;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/></svg>
                    <p style="font-size:13px;margin:0;">No payout requests yet.</p>
                </div>
            @else
                <div style="display:flex;flex-direction:column;gap:8px;">
                    @foreach($payouts as $payout)
                    <div class="payout-row">
                        <div>
                            <div style="font-size:13.5px;font-weight:800;color:var(--text-primary);">${{ number_format($payout->amount, 2) }}</div>
                            <div style="font-size:11px;color:var(--text-secondary);margin-top:2px;text-transform:capitalize;">
                                {{ str_replace('_', ' ', $payout->method) }} · {{ $payout->created_at->format('M d, Y') }}
                            </div>
                        </div>
                        @if($payout->status === 'approved')
                            <span class="badge badge-success">Approved</span>
                        @elseif($payout->status === 'pending')
                            <span class="badge badge-pending">Pending</span>
                        @else
                            <span class="badge badge-danger">Rejected</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>{{-- /right --}}
</div>{{-- /grid --}}

@endsection

@section('scripts')
<script>
document.querySelectorAll('.stat-card').forEach((el, i) => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(14px)';
    el.style.transition = `opacity 0.4s ease ${i*0.07}s, transform 0.4s ease ${i*0.07}s`;
    requestAnimationFrame(() => {
        el.style.opacity = '1';
        el.style.transform = 'translateY(0)';
    });
});
</script>
@endsection
