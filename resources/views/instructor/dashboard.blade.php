@extends('layouts.app')

@section('title', 'Instructor Dashboard - Aether LMS')

@section('content')
<header class="mb-12">
    <h1 class="text-3xl font-extrabold text-text-primary mb-2">Instructor Workspace</h1>
    <p class="text-text-secondary text-sm md:text-base">Manage your courses, track student syllabus builder structures, and request payouts.</p>
</header>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-1 transition-all duration-300 flex flex-col">
        <span class="text-xs text-text-secondary uppercase font-bold tracking-wider mb-1">Current Balance</span>
        <h3 class="text-3xl font-extrabold text-primary">${{ number_format($user->payout_balance, 2) }}</h3>
        <p class="text-xs text-text-muted mt-2">Available for immediate payout request.</p>
    </div>

    <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-1 transition-all duration-300 flex flex-col">
        <span class="text-xs text-text-secondary uppercase font-bold tracking-wider mb-1">Approved Payouts</span>
        <h3 class="text-3xl font-extrabold text-success">${{ number_format($totalApproved, 2) }}</h3>
        <p class="text-xs text-text-muted mt-2">Successfully transferred to your accounts.</p>
    </div>

    <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-1 transition-all duration-300 flex flex-col">
        <span class="text-xs text-text-secondary uppercase font-bold tracking-wider mb-1">Lifetime Earnings</span>
        <h3 class="text-3xl font-extrabold text-text-primary">${{ number_format($lifetimeEarnings, 2) }}</h3>
        <p class="text-xs text-text-muted mt-2">Gross revenue earned on the platform.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-8">
    
    <!-- Left Section: Created Courses List -->
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between border-b border-gray-200 dark:border-border pb-3 mb-2 gap-4">
            <h2 class="text-xl font-extrabold text-text-primary">My Created Courses</h2>
            <a href="{{ route('builder.create') }}" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary-hover hover:-translate-y-0.5 shadow-sm transition-all duration-300">Create New Course</a>
        </div>

        @if($courses->isEmpty())
            <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-8 md:p-12 text-center text-text-secondary shadow-sm dark:shadow-card">
                <h3 class="text-lg font-bold text-text-primary">You haven't created any courses yet</h3>
                <p class="text-text-secondary text-sm mt-2 mb-6">Share your expertise with the world and build your first course.</p>
                <a href="{{ route('builder.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary text-white rounded-lg font-semibold cursor-pointer shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:bg-primary-hover hover:-translate-y-0.5 transition-all duration-300 text-sm">Build Your First Course</a>
            </div>
        @else
            <div class="flex flex-col gap-4">
                @foreach($courses as $course)
                    <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-4 shadow-sm dark:shadow-card hover:border-primary/30 transition-all duration-300 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-4 w-full sm:w-auto">
                            <div class="w-20 h-14 rounded-lg overflow-hidden bg-black shrink-0 border border-gray-100 dark:border-border">
                                <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-text-primary mb-1 truncate max-w-[280px] md:max-w-[340px]">{{ $course->title }}</h3>
                                <div class="flex items-center gap-2 text-xs text-text-secondary font-medium">
                                    <span>Lessons: <strong class="text-text-primary font-bold">{{ $course->lessons_count }}</strong></span>
                                    <span>&bull;</span>
                                    <span>Price: <strong class="text-text-primary font-bold">${{ number_format($course->price, 2) }}</strong></span>
                                    <span>&bull;</span>
                                    @if($course->is_published)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-success/15 text-success">Published</span>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-warning/15 text-warning">Draft</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                            <form action="{{ route('builder.toggle-publish', $course->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center px-2.5 py-1.5 bg-surface-solid border text-xs font-semibold rounded-lg hover:bg-gray-100 dark:hover:bg-border hover:-translate-y-0.5 transition-all duration-300 cursor-pointer" style="{{ $course->is_published ? 'border-color: var(--color-warning); color: var(--color-warning);' : 'border-color: var(--color-success); color: var(--color-success);' }}">
                                    {{ $course->is_published ? 'Unpublish' : 'Publish' }}
                                </button>
                            </form>

                            <a href="{{ route('builder.edit', $course->id) }}" class="inline-flex items-center justify-center px-2.5 py-1.5 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary-hover hover:-translate-y-0.5 shadow-sm transition-all duration-300 cursor-pointer">Edit Course</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Right Section: Payout Options & History Ledger -->
    <div class="flex flex-col gap-6">
        
        <!-- Request Payout Card Form -->
        <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-0.5 transition-all duration-300">
            <h3 class="text-lg font-extrabold text-text-primary mb-4">Request a Payout</h3>
            
            <form action="{{ route('instructor.request-payout') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block font-semibold mb-1.5 text-xs uppercase tracking-wide text-text-secondary" for="amount">Payout Amount (USD)</label>
                    <input class="w-full py-2.5 px-3.5 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" type="number" step="0.01" name="amount" id="amount" value="{{ old('amount', $user->payout_balance) }}" min="10" required>
                    @error('amount')
                        <span class="text-danger text-xs mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block font-semibold mb-1.5 text-xs uppercase tracking-wide text-text-secondary" for="method">Transfer Method</label>
                    <select class="w-full py-2.5 px-3.5 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)] cursor-pointer" name="method" id="method" required>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="paypal">PayPal</option>
                    </select>
                    @error('method')
                        <span class="text-danger text-xs mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="w-full mt-4 inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-primary text-white rounded-lg font-semibold cursor-pointer shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:bg-primary-hover hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-none transition-all duration-300" {{ $user->payout_balance < 10 ? 'disabled' : '' }}>
                    Request Transfer
                </button>

                @if($user->payout_balance < 10)
                    <p class="text-xs text-text-muted text-center mt-3">Minimum payout request is $10.00</p>
                @endif
            </form>
        </div>

        <!-- Payout Transaction History Ledger -->
        <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card overflow-hidden">
            <h3 class="text-lg font-extrabold text-text-primary border-b border-gray-200 dark:border-border pb-3 mb-4">Payout History</h3>

            @if($payouts->isEmpty())
                <div class="text-center py-4 text-text-secondary text-xs">
                    <p>No payout requests found.</p>
                </div>
            @else
                <div class="flex flex-col gap-3">
                    @foreach($payouts as $payout)
                        <div class="p-3 bg-surface-solid border border-gray-200 dark:border-border rounded-lg flex flex-col gap-2">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-text-muted">{{ $payout->created_at->format('M d, Y H:i') }}</span>
                                <strong class="text-text-primary font-bold">${{ number_format($payout->amount, 2) }}</strong>
                            </div>
                            <div class="flex justify-between items-center text-[10px]">
                                <span class="text-text-secondary uppercase font-semibold">{{ str_replace('_', ' ', $payout->method) }}</span>
                                @if($payout->status === 'approved')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-success/15 text-success font-bold uppercase">{{ $payout->status }}</span>
                                @elseif($payout->status === 'pending')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-warning/15 text-warning font-bold uppercase">{{ $payout->status }}</span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-danger/15 text-danger font-bold uppercase">{{ $payout->status }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
