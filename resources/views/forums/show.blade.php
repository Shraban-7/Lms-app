@extends('layouts.app')

@section('title')
    {{ $topic->title }} - Forum Thread
@endsection

@section('content')
<header class="mb-8">
    <div class="flex items-center gap-2 mb-2 text-sm">
        <a href="{{ route('forums.index', $course->id) }}" class="text-text-secondary hover:text-primary transition-colors font-medium">← Forum Index</a>
        <span class="text-text-muted">&bull;</span>
        <span class="text-text-secondary">Thread</span>
    </div>
    <h1 class="text-3xl font-extrabold text-text-primary leading-tight">{{ $topic->title }}</h1>
</header>

<div class="grid grid-cols-1 gap-6">
    
    <!-- Topic Original Post Card -->
    <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 md:p-8 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-0.5 transition-all duration-300">
        <div class="flex justify-between items-center pb-3 mb-4 border-b border-gray-200 dark:border-border text-xs text-text-secondary">
            <span>By <strong class="text-text-primary font-medium">{{ $topic->user->name }}</strong> ({{ ucfirst($topic->user->role) }})</span>
            <span>{{ $topic->created_at->format('M d, Y H:i') }}</span>
        </div>
        <div class="text-text-primary leading-relaxed text-[15px] whitespace-pre-line">
            {{ $topic->content }}
        </div>
    </div>

    <!-- Discussion Replies -->
    <section class="mt-6">
        <h2 class="text-xl font-extrabold text-text-primary border-b border-gray-200 dark:border-border pb-2 mb-6">Replies ({{ $topic->replies->count() }})</h2>

        <!-- Outer Replies List (Parent id is null) -->
        <div class="flex flex-col gap-6">
            @if($replies->isEmpty())
                <p class="text-text-secondary italic text-sm">No replies yet. Share your thoughts below.</p>
            @else
                @foreach($replies as $reply)
                    <!-- Parent Reply Card -->
                    <div class="bg-white/70 dark:bg-surface/60 border border-gray-200 dark:border-border rounded-lg p-5 shadow-sm hover:border-primary/50 transition-all duration-200" id="reply-{{ $reply->id }}">
                        <div class="flex justify-between items-center text-xs text-text-secondary mb-3">
                            <span><strong class="text-text-primary font-medium">{{ $reply->user->name }}</strong> ({{ ucfirst($reply->user->role) }})</span>
                            <span>{{ $reply->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="text-sm text-text-primary leading-relaxed whitespace-pre-line">
                            {{ $reply->content }}
                        </div>
                        
                        <!-- Reply Button -->
                        <div class="mt-3 flex items-center">
                            <button onclick="toggleReplyForm({{ $reply->id }})" class="inline-flex items-center justify-center px-3 py-1 bg-surface-solid border border-gray-200 dark:border-border text-text-primary text-[11px] font-semibold rounded hover:bg-gray-100 dark:hover:bg-border hover:-translate-y-0.5 transition-all duration-300 cursor-pointer">Reply</button>
                        </div>

                        <!-- Nested Reply Form (Hidden by default) -->
                        <div id="reply-form-{{ $reply->id }}" style="display: none;" class="mt-4 pt-4 border-t border-dashed border-gray-200 dark:border-border">
                            <form action="{{ route('forums.store-reply', [$course->id, $topic->id]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                                <div class="mb-3">
                                    <textarea class="w-full py-2 px-3 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" name="content" rows="2" placeholder="Write a nested reply..." required></textarea>
                                </div>
                                <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 bg-primary text-white text-[11px] font-semibold rounded hover:bg-primary-hover shadow-sm transition-all duration-300 cursor-pointer">Submit</button>
                            </form>
                        </div>
                    </div>

                    <!-- Level 1 Nested Replies -->
                    @foreach($reply->replies as $subReply)
                        <div class="ml-6 md:ml-10 border-l-2 border-primary pl-4 md:pl-5 bg-white/40 dark:bg-surface/30 border-y border-r border-gray-200 dark:border-border rounded-r-lg p-5 shadow-sm" id="reply-{{ $subReply->id }}">
                            <div class="flex justify-between items-center text-xs text-text-secondary mb-3">
                                <span><strong class="text-text-primary font-medium">{{ $subReply->user->name }}</strong> ({{ ucfirst($subReply->user->role) }})</span>
                                <span>{{ $subReply->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-sm text-text-primary leading-relaxed whitespace-pre-line">
                                {{ $subReply->content }}
                            </div>

                            <!-- Reply Button for level 2 -->
                            <div class="mt-3 flex items-center">
                                <button onclick="toggleReplyForm({{ $subReply->id }})" class="inline-flex items-center justify-center px-3 py-1 bg-surface-solid border border-gray-200 dark:border-border text-text-primary text-[11px] font-semibold rounded hover:bg-gray-100 dark:hover:bg-border hover:-translate-y-0.5 transition-all duration-300 cursor-pointer">Reply</button>
                            </div>

                            <!-- Nested Level 2 Reply Form -->
                            <div id="reply-form-{{ $subReply->id }}" style="display: none;" class="mt-4 pt-4 border-t border-dashed border-gray-200 dark:border-border">
                                <form action="{{ route('forums.store-reply', [$course->id, $topic->id]) }}" method="POST">
                                    @csrf
                                    <!-- Parent ID set to subReply ID -->
                                    <input type="hidden" name="parent_id" value="{{ $subReply->id }}">
                                    <div class="mb-3">
                                        <textarea class="w-full py-2 px-3 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" name="content" rows="2" placeholder="Write a nested reply..." required></textarea>
                                    </div>
                                    <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 bg-primary text-white text-[11px] font-semibold rounded hover:bg-primary-hover shadow-sm transition-all duration-300 cursor-pointer">Submit</button>
                                </form>
                            </div>

                            <!-- Level 2 Nested Replies -->
                            @foreach($subReply->replies as $subSubReply)
                                <div class="ml-10 md:ml-16 border-l-2 border-accent pl-4 md:pl-5 bg-white/20 dark:bg-surface/20 border-y border-r border-gray-200 dark:border-border rounded-r-lg p-5 shadow-sm" id="reply-{{ $subSubReply->id }}">
                                    <div class="flex justify-between items-center text-xs text-text-secondary mb-3">
                                        <span><strong class="text-text-primary font-medium">{{ $subSubReply->user->name }}</strong> ({{ ucfirst($subSubReply->user->role) }})</span>
                                        <span>{{ $subSubReply->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-sm text-text-primary leading-relaxed whitespace-pre-line">
                                        {{ $subSubReply->content }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                @endforeach
            @endif
        </div>

        <!-- Main Topic Reply Box -->
        <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 md:p-8 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-0.5 transition-all duration-300 mt-12">
            <h3 class="text-lg font-extrabold text-text-primary mb-4">Post a Reply</h3>
            <form action="{{ route('forums.store-reply', [$course->id, $topic->id]) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <textarea class="w-full py-3 px-4 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" name="content" rows="5" placeholder="Share your insights, answer a question, or comment here..." required></textarea>
                </div>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white rounded-lg font-semibold cursor-pointer shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:bg-primary-hover hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)] transition-all duration-300">Submit Reply</button>
            </form>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    function toggleReplyForm(replyId) {
        const formDiv = document.getElementById(`reply-form-${replyId}`);
        if (formDiv.style.display === 'none') {
            formDiv.style.display = 'block';
        } else {
            formDiv.style.display = 'none';
        }
    }
</script>
@endsection
