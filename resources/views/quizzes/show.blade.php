@extends('layouts.app')

@section('title')
    Taking Assessment: {{ $quiz->title }}
@endsection

@section('content')
<div class="max-w-[800px] mx-auto my-8 bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 md:p-10 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-0.5 transition-all duration-300">
    
    <header class="border-b border-gray-200 dark:border-border pb-6 mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-success/15 text-success mb-2">Assessment</span>
            <h1 class="text-2xl md:text-3xl font-extrabold text-text-primary leading-tight">{{ $quiz->title }}</h1>
            <p class="text-text-secondary text-sm mt-1">
                Passing Threshold: <strong class="text-text-primary font-medium">{{ $quiz->passing_score }}%</strong> | Total Questions: <strong class="text-text-primary font-medium">{{ $quiz->questions->count() }}</strong>
            </p>
        </div>
        <a href="{{ route('courses.play', [$course->id, $quiz->lesson_id]) }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-surface-solid border border-gray-200 dark:border-border text-text-primary text-sm font-semibold rounded-lg hover:bg-gray-100 dark:hover:bg-border hover:-translate-y-0.5 transition-all duration-300 cursor-pointer">← Exit Assessment</a>
    </header>

    <form method="POST" action="{{ route('quizzes.submit', [$course->id, $quiz->id]) }}">
        @csrf

        <div class="flex flex-col gap-10">
            @foreach($quiz->questions as $index => $question)
                <div class="pb-8 border-b border-gray-200 dark:border-border last:border-b-0 last:pb-0" id="q-block-{{ $question->id }}">
                    <div class="flex justify-between items-start mb-4 gap-4">
                        <h3 class="text-lg font-bold text-text-primary leading-snug">
                            Question {{ $index + 1 }}: {{ $question->question_text }}
                        </h3>
                        <span class="text-xs font-bold text-primary bg-primary/15 px-2.5 py-1 rounded shrink-0">
                            {{ $question->points }} {{ Str::plural('point', $question->points) }}
                        </span>
                    </div>

                    <!-- Answer Options based on Question Type -->
                    <div class="mt-3 flex flex-col gap-3">
                        @if($question->type === 'single_choice')
                            @foreach($question->options as $opt)
                                <label class="flex items-center gap-3 p-4 bg-surface-solid border border-gray-200 dark:border-border rounded-lg cursor-pointer transition-all duration-200 hover:border-primary hover:bg-primary/10 text-text-primary text-sm font-medium">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $opt }}" class="w-4 h-4 cursor-pointer accent-primary border-gray-300" required>
                                    <span>{{ $opt }}</span>
                                </label>
                            @endforeach

                        @elseif($question->type === 'multiple_choice')
                            @foreach($question->options as $opt)
                                <label class="flex items-center gap-3 p-4 bg-surface-solid border border-gray-200 dark:border-border rounded-lg cursor-pointer transition-all duration-200 hover:border-primary hover:bg-primary/10 text-text-primary text-sm font-medium">
                                    <!-- Use answers[Q][opt] or similar, but the backend parses array for checkboxes. Let's name it answers[Q][] -->
                                    <input type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $opt }}" class="w-4 h-4 cursor-pointer accent-primary rounded border-gray-300">
                                    <span>{{ $opt }}</span>
                                </label>
                            @endforeach

                        @elseif($question->type === 'true_false')
                            <label class="flex items-center gap-3 p-4 bg-surface-solid border border-gray-200 dark:border-border rounded-lg cursor-pointer transition-all duration-200 hover:border-primary hover:bg-primary/10 text-text-primary text-sm font-medium">
                                <input type="radio" name="answers[{{ $question->id }}]" value="true" class="w-4 h-4 cursor-pointer accent-primary border-gray-300" required>
                                <span>True</span>
                            </label>
                            <label class="flex items-center gap-3 p-4 bg-surface-solid border border-gray-200 dark:border-border rounded-lg cursor-pointer transition-all duration-200 hover:border-primary hover:bg-primary/10 text-text-primary text-sm font-medium">
                                <input type="radio" name="answers[{{ $question->id }}]" value="false" class="w-4 h-4 cursor-pointer accent-primary border-gray-300" required>
                                <span>False</span>
                            </label>

                        @elseif($question->type === 'short_answer')
                            <div class="mt-2">
                                <input class="w-full py-3 px-4 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)] text-sm" type="text" name="answers[{{ $question->id }}]" placeholder="Type your answer here..." required autocomplete="off">
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex justify-end border-t border-gray-200 dark:border-border pt-6 mt-10">
            <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-primary to-accent text-white rounded-lg font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-[0_5px_15px_rgba(236,72,153,0.3)] transition-all duration-300">Submit Assessment</button>
        </div>
    </form>
</div>
@endsection
