@extends('layouts.app')

@section('title')
    Discussion Forum: {{ $course->title }}
@endsection

@section('content')
<header class="mb-10 flex flex-col md:flex-row md:items-end justify-between border-b border-gray-200 dark:border-border pb-6 gap-4">
    <div>
        <div class="flex items-center gap-2 mb-2 text-sm">
            <a href="{{ route('courses.show', $course->id) }}" class="text-text-secondary hover:text-primary transition-colors font-medium">← Back to Course</a>
            <span class="text-text-muted">&bull;</span>
            <span class="text-text-secondary">Forum Board</span>
        </div>
        <h1 class="text-3xl font-extrabold text-text-primary">Course Discussion Board</h1>
        <p class="text-text-secondary">Ask questions, share lecture notes, and collaborate with other students.</p>
    </div>
</header>

<div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-8">
    
    <!-- Topics List -->
    <div class="flex flex-col gap-4">
        <h2 class="text-xl font-extrabold text-text-primary mb-2">Discussions</h2>
        
        @if($topics->isEmpty())
            <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-8 md:p-16 text-center text-text-secondary shadow-sm dark:shadow-card">
                <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="text-text-secondary mx-auto mb-4">
                    <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-1.12-2.5-2.5-2.5S6 10.62 6 12a2.5 2.5 0 0 0 2.5 2.5zM15.5 14.5a2.5 2.5 0 0 0 2.5-2.5c0-1.38-1.12-2.5-2.5-2.5S13 10.62 13 12a2.5 2.5 0 0 0 2.5 2.5z"/>
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                <h3 class="text-lg font-bold text-text-primary">No threads started yet</h3>
                <p class="text-text-secondary text-sm mt-2">Be the first to start the conversation by asking a question on the right!</p>
            </div>
        @else
            @foreach($topics as $topic)
                <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-0.5 transition-all duration-300 flex flex-col gap-3">
                    <div class="flex justify-between items-start gap-4">
                        <h3 class="text-lg font-bold text-text-primary leading-snug">
                            <a href="{{ route('forums.show', [$course->id, $topic->id]) }}" class="text-text-primary hover:text-primary transition-colors">
                                {{ $topic->title }}
                            </a>
                        </h3>
                        
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-primary/20 text-primary shrink-0">
                            {{ $topic->replies_count }} {{ Str::plural('reply', $topic->replies_count) }}
                        </span>
                    </div>

                    <p class="text-text-secondary text-sm leading-relaxed">
                        {{ Str::limit($topic->content, 180) }}
                    </p>

                    <div class="border-t border-gray-200 dark:border-border pt-3 mt-1 flex justify-between items-center text-xs text-text-muted">
                        <span>Posted by <strong class="text-text-primary font-medium">{{ $topic->user->name }}</strong> ({{ $topic->user->role }})</span>
                        <span>{{ $topic->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- Create Topic Sidebar Form -->
    <div>
        <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-0.5 transition-all duration-300 lg:sticky lg:top-24 flex flex-col">
            <h3 class="text-lg font-extrabold text-text-primary mb-4">New Topic</h3>
            
            <form action="{{ route('forums.store-topic', $course->id) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block font-semibold mb-1.5 text-xs uppercase tracking-wide text-text-secondary" for="t_title">Title</label>
                    <input class="w-full py-2.5 px-3.5 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" type="text" name="title" id="t_title" placeholder="Short description of question" required>
                </div>

                <div class="mb-4">
                    <label class="block font-semibold mb-1.5 text-xs uppercase tracking-wide text-text-secondary" for="t_content">Question Content</label>
                    <textarea class="w-full py-2.5 px-3.5 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" name="content" id="t_content" rows="6" placeholder="Describe your issue or thought in detail..." required></textarea>
                </div>

                <button type="submit" class="w-full mt-4 inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-primary text-white rounded-lg font-semibold cursor-pointer shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:bg-primary-hover hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)] transition-all duration-300">Post Topic</button>
            </form>
        </div>
    </div>
</div>
@endsection
