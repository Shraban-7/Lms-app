@extends('layouts.app')

@section('title')
    {{ $course->title }} - Aether LMS
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-12">
    <!-- Course Details & Outline -->
    <div>
        <header class="mb-8">
            <div class="flex items-center gap-2 mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-primary/20 text-primary">Course</span>
                <span class="text-xs text-text-secondary">Created by <strong class="text-text-primary font-medium">{{ $course->instructor->name }}</strong></span>
            </div>
            <h1 class="text-4xl font-extrabold text-text-primary leading-tight mb-4">{{ $course->title }}</h1>
            <p class="text-text-secondary text-lg leading-relaxed">{{ $course->description }}</p>
        </header>

        <!-- Course Curriculum Tree -->
        <section class="mt-12">
            <h2 class="text-2xl font-extrabold text-text-primary mb-6">Course Outline</h2>
            
            @if($course->modules->isEmpty())
                <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-8 text-center text-text-secondary shadow-sm dark:shadow-card">
                    <p>No content has been added to this course syllabus yet.</p>
                </div>
            @else
                <div class="flex flex-col gap-4">
                    @foreach($course->modules as $index => $module)
                        <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-0.5 transition-all duration-300">
                            <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-200 dark:border-border">
                                <div>
                                    <span class="text-xs font-bold uppercase text-primary">Module {{ $index + 1 }}</span>
                                    <h3 class="text-lg font-bold text-text-primary">{{ $module->title }}</h3>
                                </div>
                                <span class="text-sm text-text-secondary font-medium">{{ $module->lessons->count() }} Lessons</span>
                            </div>

                            @if($module->description)
                                <p class="text-sm text-text-secondary mb-4">{{ $module->description }}</p>
                            @endif

                            <div class="flex flex-col gap-2 pl-2">
                                @foreach($module->lessons as $lesson)
                                    <div class="flex items-center justify-between p-3 rounded-lg bg-surface-solid border border-gray-200 dark:border-border text-sm">
                                        <div class="flex items-center gap-2.5">
                                            <!-- Icon based on type -->
                                            @if($lesson->type === 'video')
                                                <span class="text-primary flex items-center">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                                                </span>
                                            @elseif($lesson->type === 'quiz')
                                                <span class="text-accent flex items-center">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z"/><path d="m9 12 2 2 4-4"/></svg>
                                                </span>
                                            @else
                                                <span class="text-text-secondary flex items-center">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                </span>
                                            @endif
                                            <span class="text-text-primary font-medium">{{ $lesson->title }}</span>
                                        </div>
                                        <span class="text-xs text-text-muted">{{ $lesson->duration_minutes }} min</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    <!-- Purchase / Enrollment Sidecard -->
    <div>
        <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-1 transition-all duration-300 lg:sticky lg:top-24 flex flex-col gap-6">
            <div class="h-48 -mx-6 -mt-6 overflow-hidden rounded-t-[14px]">
                <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
            </div>

            <div>
                <span class="text-xs text-text-secondary uppercase font-bold tracking-wider">Course Pricing</span>
                <div class="text-3xl font-extrabold text-text-primary mt-1">
                    {{ $course->price > 0 ? '$' . number_format($course->price, 2) : 'Free' }}
                </div>
            </div>

            @auth
                @if($progress !== null && $progress['completed'] > 0)
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('courses.play', $course->id) }}" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white rounded-lg font-semibold cursor-pointer shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:bg-primary-hover hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)] transition-all duration-300">Resume Learning</a>
                        <div class="flex items-center justify-between text-xs text-text-secondary mt-2">
                            <span>Progress</span>
                            <strong>{{ $progress['percentage'] }}% Complete</strong>
                        </div>
                        <div class="w-full h-2 bg-gray-200 dark:bg-border rounded-full overflow-hidden mt-1">
                            <div class="h-full bg-gradient-to-r from-primary to-accent rounded-full transition-[width] duration-500" style="width: {{ $progress['percentage'] }}%;"></div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('courses.play', $course->id) }}" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white rounded-lg font-semibold cursor-pointer shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:bg-primary-hover hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)] transition-all duration-300">Enroll & Start Learning</a>
                @endif

                @if($hasCertificate)
                    <a href="{{ route('courses.certificate', $course->id) }}" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-surface-solid border border-success/30 dark:border-success/20 text-success rounded-lg font-semibold hover:bg-success/5 hover:-translate-y-0.5 transition-all duration-300 cursor-pointer" target="_blank">
                        View Certificate
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white rounded-lg font-semibold cursor-pointer shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:bg-primary-hover hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)] transition-all duration-300">Login to Enroll</a>
            @endauth

            <a href="{{ route('forums.index', $course->id) }}" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-surface-solid border border-gray-200 dark:border-border text-text-primary rounded-lg font-semibold hover:bg-gray-100 dark:hover:bg-border hover:-translate-y-0.5 transition-all duration-300 cursor-pointer">
                Course Discussion Forum
            </a>

            <div class="border-t border-gray-200 dark:border-border pt-4 text-xs text-text-secondary flex flex-col gap-3">
                <div class="flex items-center gap-2">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-text-secondary"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z"/><path d="M12 6v6l4 2"/></svg>
                    <span>Self-paced course module syllabus</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-text-secondary"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span>Verified completion certificate included</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
