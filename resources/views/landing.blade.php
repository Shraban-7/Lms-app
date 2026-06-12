@extends('layouts.app')

@section('title', 'Welcome to Aether LMS')

@section('content')
    <!-- Hero Section -->
    <header class="py-24 md:py-32 text-center relative">
        <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight leading-tight mb-6 bg-gradient-to-r from-gray-800 to-gray-600 dark:from-white dark:to-gray-400 bg-clip-text text-transparent">
            Master New Skills with<br>
            <span class="bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent">Interactive Video Learning</span>
        </h1>
        <p class="text-lg md:text-xl text-text-secondary max-w-2xl mx-auto mb-10">
            A premium learning environment featuring modular courses, real-time progress calculations, interactive weighted quizzes, student-only forums, and automated certificate issuance.
        </p>

        @guest
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('register') }}"
                    class="px-6 py-3 bg-primary text-white rounded-lg font-semibold hover:bg-primary-hover hover:-translate-y-0.5 shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)] transition-all duration-300 cursor-pointer">
                    Get Started Now
                </a>
                <a href="{{ route('login') }}"
                    class="px-6 py-3 bg-surface-solid border border-gray-200 dark:border-border text-text-primary rounded-lg font-semibold hover:bg-gray-100 dark:hover:bg-border hover:-translate-y-0.5 transition-all duration-300 cursor-pointer">
                    Explore Catalog
                </a>
            </div>
        @else
            <a href="{{ route('dashboard') }}"
                class="px-6 py-3 bg-primary text-white rounded-lg font-semibold hover:bg-primary-hover hover:-translate-y-0.5 shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)] transition-all duration-300 cursor-pointer">
                Go to My Dashboard
            </a>
        @endguest
    </header>

    <!-- Courses Section -->
    <section class="mt-16">
        <div class="flex flex-col md:flex-row md:justify-between md:items-end border-b border-gray-200 dark:border-border pb-4 gap-4">
            <div>
                <h2 class="text-3xl font-extrabold text-text-primary">Featured Courses</h2>
                <p class="text-text-secondary mt-1">Unlock professional-grade training and earn verified certificates.</p>
            </div>
        </div>

        @if ($courses->isEmpty())
            <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-8 md:p-16 text-center mt-12 shadow-sm dark:shadow-card">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-text-secondary mx-auto mb-4">
                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z" />
                    <path d="M6 6h10M6 10h10" />
                </svg>
                <h3 class="text-xl font-bold text-text-primary">No courses available yet</h3>
                <p class="text-text-secondary mt-2">Check back later or register as an instructor to start building your own courses.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-12">
                @foreach ($courses as $course)
                    <article class="group bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-8 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-1 transition-all duration-300 flex flex-col h-full relative overflow-hidden">
                        <div class="relative -mx-8 -mt-8 mb-6 h-48 overflow-hidden rounded-t-[14px]">
                            <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <span class="absolute top-4 right-4 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-primary/20 text-primary backdrop-blur-md">
                                {{ $course->price > 0 ? '$' . number_format($course->price, 2) : 'Free' }}
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-text-primary mb-3 leading-snug">
                            {{ $course->title }}
                        </h3>

                        <p class="text-text-secondary text-sm mb-6 flex-grow">
                            {{ Str::limit($course->description, 120) }}
                        </p>

                        <div class="flex items-center justify-between border-t border-gray-200 dark:border-border pt-4 mt-auto">
                            <span class="text-xs text-text-muted">
                                By <strong class="text-text-primary font-medium">{{ $course->instructor->name }}</strong>
                            </span>
                            <a href="{{ route('courses.show', $course->id) }}"
                                class="inline-flex items-center justify-center px-4 py-2 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary-hover hover:-translate-y-0.5 transition-all duration-300">
                                Learn More
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
