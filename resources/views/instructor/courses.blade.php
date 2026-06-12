@extends('layouts.app')

@section('title', 'My Courses - Instructor')

@section('content')
<header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-12">
    <div>
        <h1 class="text-3xl font-extrabold text-text-primary">Instructor Workspace</h1>
        <p class="text-text-secondary mt-1">Manage your courses, lessons, and student curricula.</p>
    </div>
    <a href="{{ route('builder.create') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white rounded-lg font-semibold cursor-pointer shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:bg-primary-hover hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)] transition-all duration-300">Create New Course</a>
</header>

<div class="grid grid-cols-1 gap-6">
    @if($courses->isEmpty())
        <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-8 md:p-16 text-center text-text-secondary shadow-sm dark:shadow-card">
            <h3 class="text-xl font-bold text-text-primary">You haven't created any courses yet</h3>
            <p class="text-text-secondary mt-2 mb-8">Share your expertise with the world and build your first course.</p>
            <a href="{{ route('builder.create') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white rounded-lg font-semibold cursor-pointer shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:bg-primary-hover hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)] transition-all duration-300">Build Your First Course</a>
        </div>
    @else
        @foreach($courses as $course)
            <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-1 transition-all duration-300 flex flex-wrap items-center justify-between gap-6">
                <div class="flex flex-wrap sm:flex-nowrap items-center gap-6 flex-grow">
                    <div class="w-24 h-16 rounded-lg overflow-hidden bg-black shrink-0">
                        <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-text-primary mb-1">{{ $course->title }}</h3>
                        <div class="flex flex-wrap items-center gap-3 text-xs text-text-secondary font-medium">
                            <span>Lessons: <strong class="text-text-primary font-bold">{{ $course->lessons_count }}</strong></span>
                            <span>&bull;</span>
                            <span>Price: <strong class="text-text-primary font-bold">${{ number_format($course->price, 2) }}</strong></span>
                            <span>&bull;</span>
                            @if($course->is_published)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-success/15 text-success">Published</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-warning/15 text-warning">Draft</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <form action="{{ route('builder.toggle-publish', $course->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 bg-surface-solid border text-xs font-semibold rounded hover:bg-gray-100 dark:hover:bg-border hover:-translate-y-0.5 transition-all duration-300 cursor-pointer" style="{{ $course->is_published ? 'border-color: var(--color-warning); color: var(--color-warning);' : 'border-color: var(--color-success); color: var(--color-success);' }}">
                            {{ $course->is_published ? 'Unpublish' : 'Publish' }}
                        </button>
                    </form>

                    <a href="{{ route('builder.edit', $course->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 bg-primary text-white text-xs font-semibold rounded hover:bg-primary-hover hover:-translate-y-0.5 shadow-sm transition-all duration-300 cursor-pointer">Edit Curriculum Tree</a>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
