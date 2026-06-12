@extends('layouts.app')

@section('title', 'My Dashboard - Aether LMS')

@section('content')
<header class="mb-12">
    <h1 class="text-3xl font-extrabold text-text-primary mb-2">Welcome back, {{ auth()->user()->name }}!</h1>
    <p class="text-text-secondary text-sm md:text-base">Here is a summary of your learning progress and credentials.</p>
</header>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
    <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-1 transition-all duration-300 flex items-center gap-5">
        <div class="bg-primary/10 text-primary p-4 rounded-lg flex items-center justify-center">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z" />
            </svg>
        </div>
        <div>
            <h3 class="text-2xl font-extrabold text-text-primary">{{ count($enrolledCourses) }}</h3>
            <p class="text-text-secondary text-xs font-semibold tracking-wide uppercase">Courses in Progress</p>
        </div>
    </div>

    <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-1 transition-all duration-300 flex items-center gap-5">
        <div class="bg-success/10 text-success p-4 rounded-lg flex items-center justify-center">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z"/>
                <path d="m9 12 2 2 4-4"/>
            </svg>
        </div>
        <div>
            <h3 class="text-2xl font-extrabold text-text-primary">{{ $certificates->count() }}</h3>
            <p class="text-text-secondary text-xs font-semibold tracking-wide uppercase">Certificates Earned</p>
        </div>
    </div>
</div>

<!-- Enrolled/In-Progress Courses -->
<section class="mb-16">
    <h2 class="text-2xl font-extrabold text-text-primary border-b border-gray-200 dark:border-border pb-3 mb-6">Your Enrolled Courses</h2>
    @if(empty($enrolledCourses))
        <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-8 text-center text-text-secondary shadow-sm dark:shadow-card">
            <p>You haven't started any courses yet. Choose a course from the available selection below!</p>
        </div>
    @else
        <div class="flex flex-col gap-6">
            @foreach($enrolledCourses as $data)
                <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-1 transition-all duration-300 flex flex-wrap md:flex-nowrap justify-between items-center gap-6">
                    <div class="flex-grow min-w-[280px]">
                        <h3 class="text-xl font-bold text-text-primary mb-1">{{ $data['course']->title }}</h3>
                        <p class="text-text-secondary text-sm mb-4">Instructor: <strong class="text-text-primary font-medium">{{ $data['course']->instructor->name }}</strong></p>
                        
                        <div class="flex items-center gap-4">
                            <div class="w-full max-w-[250px] h-2 bg-gray-200 dark:bg-border rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-primary to-accent rounded-full transition-[width] duration-500" style="width: {{ $data['progress'] }}%;"></div>
                            </div>
                            <span class="text-sm font-extrabold text-text-primary">{{ $data['progress'] }}% Complete</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between md:justify-end gap-6 w-full md:w-auto">
                        <div class="text-right">
                            <span class="text-xs text-text-secondary block mb-1 font-medium">Weighted Grade</span>
                            <strong class="text-lg font-extrabold text-text-primary" style="{{ $data['grade'] >= 70 ? 'color: var(--color-success);' : '' }}">
                                {{ $data['has_quizzes'] ? $data['grade'] . '%' : 'N/A' }}
                            </strong>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('courses.show', $data['course']->id) }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-surface-solid border border-gray-200 dark:border-border text-text-primary text-sm font-semibold rounded-lg hover:bg-gray-100 dark:hover:bg-border hover:-translate-y-0.5 transition-all duration-300">Overview</a>
                            <a href="{{ route('courses.play', $data['course']->id) }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-hover hover:-translate-y-0.5 shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)] transition-all duration-300">Resume Learning</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>

<!-- Available Courses -->
<section class="mb-16">
    <h2 class="text-2xl font-extrabold text-text-primary border-b border-gray-200 dark:border-border pb-3 mb-6">Available to Start</h2>
    @if(empty($availableCourses))
        <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-8 text-center text-text-secondary shadow-sm dark:shadow-card">
            <p>No new courses are available at this time.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($availableCourses as $data)
                <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between h-full">
                    <div>
                        <h3 class="text-lg font-bold text-text-primary mb-2 leading-snug">{{ $data['course']->title }}</h3>
                        <p class="text-text-secondary text-sm mb-4">{{ Str::limit($data['course']->description, 100) }}</p>
                    </div>
                    <div class="flex items-center justify-between border-t border-gray-200 dark:border-border pt-4 mt-auto">
                        <span class="text-xs text-text-muted font-medium">By <strong class="text-text-primary font-medium">{{ $data['course']->instructor->name }}</strong></span>
                        <a href="{{ route('courses.show', $data['course']->id) }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-hover hover:-translate-y-0.5 shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)] transition-all duration-300">Start Course</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>

<!-- Certificates Section -->
<section class="mb-16">
    <h2 class="text-2xl font-extrabold text-text-primary border-b border-gray-200 dark:border-border pb-3 mb-6">My Certificates</h2>
    @if($certificates->isEmpty())
        <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-8 text-center text-text-secondary shadow-sm dark:shadow-card">
            <p>You haven't earned any certificates yet. Complete 100% of a course and maintain a passing quiz average to get certified!</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($certificates as $cert)
                <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-success/30 dark:border-success/20 rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-success hover:-translate-y-1 transition-all duration-300 bg-gradient-to-br from-white/70 to-success/5 dark:from-surface/60 dark:to-success/5">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="text-success bg-success/15 p-2 rounded-full flex items-center justify-center">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z"/>
                                <path d="m9 12 2 2 4-4"/>
                            </svg>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-success/15 text-success">Verified Credentials</span>
                    </div>

                    <h3 class="text-lg font-bold text-text-primary mb-1">{{ $cert->course->title }}</h3>
                    <p class="text-xs text-text-muted mb-4">Verification Code: <code class="font-mono bg-surface-solid border border-gray-200 dark:border-border px-1.5 py-0.5 rounded text-text-primary">{{ $cert->verification_code }}</code></p>

                    <div class="flex items-center justify-between border-t border-gray-200 dark:border-border pt-4">
                        <span class="text-xs text-text-secondary">Issued: {{ $cert->issued_at->format('M d, Y') }}</span>
                        <a href="{{ route('courses.certificate', $cert->course_id) }}" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-surface-solid border border-gray-200 dark:border-border text-text-primary text-xs font-semibold rounded-lg hover:bg-gray-100 dark:hover:bg-border hover:-translate-y-0.5 transition-all duration-300" target="_blank">View & Print</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>
@endsection
