@extends('layouts.app')

@section('title', 'Quiz Results - Aether LMS')

@section('content')
    <div class="max-w-[800px] mx-auto my-8 bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 md:p-10 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-0.5 transition-all duration-300">

        <!-- Assessment Header -->
        <header class="text-center border-b border-gray-200 dark:border-border pb-6 mb-10">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-4 {{ $attempt->passed ? 'bg-success/15 text-success' : 'bg-danger/15 text-danger' }}">
                Assessment {{ $attempt->passed ? 'Passed' : 'Failed' }}
            </span>
            <h1 class="text-3xl font-extrabold text-text-primary mb-2">{{ $quiz->title }} Results</h1>
            <p class="text-text-secondary mt-2 text-sm">For course: <strong class="text-text-primary font-medium">{{ $course->title }}</strong></p>
        </header>

        <!-- Score Analytics Dashboard -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-10 text-center">
            <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border rounded-[14px] p-6 shadow-sm flex flex-col justify-center items-center" style="{{ $attempt->passed ? 'border-color: var(--color-success);' : 'border-color: var(--color-danger);' }}">
                <span class="text-xs text-text-secondary uppercase font-bold tracking-wider mb-1">Your Score</span>
                <h3 class="text-4xl font-extrabold" style="{{ $attempt->passed ? 'color: var(--color-success);' : 'color: var(--color-danger);' }}">
                    {{ $attempt->score }}%
                </h3>
            </div>

            <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm flex flex-col justify-center items-center">
                <span class="text-xs text-text-secondary uppercase font-bold tracking-wider mb-1">Required Score</span>
                <h3 class="text-4xl font-extrabold text-text-primary">
                    {{ $quiz->passing_score }}%
                </h3>
            </div>
        </div>

        <!-- Result Feedback Banner -->
        <div class="p-6 rounded-lg text-center mb-10 font-semibold {{ $attempt->passed ? 'bg-success/10 text-success border border-success/30' : 'bg-danger/10 text-danger border border-danger/30' }}">
            @if ($attempt->passed)
                <h3 class="text-lg mb-1">Congratulations! You passed this assessment.</h3>
                <p class="text-sm font-medium opacity-90">This module's progress has been locked in as complete. Keep up the great work!</p>
            @else
                <h3 class="text-lg mb-1">You did not meet the passing criteria.</h3>
                <p class="text-sm font-medium opacity-90">Review the study materials in the previous lessons and try taking the assessment again.</p>
            @endif
        </div>

        <!-- Question Breakdown -->
        <section>
            <h2 class="text-xl font-extrabold text-text-primary border-b border-gray-200 dark:border-border pb-2 mb-6">Question Breakdown</h2>

            <div class="flex flex-col gap-6">
                @foreach ($details as $index => $item)
                    <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border rounded-[14px] p-6 shadow-sm transition-all duration-300"
                        style="{{ $item['is_correct'] ? 'border-color: rgba(16, 185, 129, 0.3); background-color: rgba(16, 185, 129, 0.02);' : 'border-color: rgba(239, 68, 68, 0.3); background-color: rgba(239, 68, 68, 0.02);' }}">

                        <div class="flex justify-between items-start mb-3 gap-4">
                            <h4 class="text-base font-bold text-text-primary leading-snug">
                                Q{{ $index + 1 }}: {{ $item['question']->question_text }}
                            </h4>

                            <span class="text-xs font-bold shrink-0 flex items-center gap-1 {{ $item['is_correct'] ? 'text-success' : 'text-danger' }}">
                                @if ($item['is_correct'])
                                    <span>Correct</span> ({{ $item['question']->points }} pts)
                                @else
                                    <span>Incorrect</span> (0 / {{ $item['question']->points }} pts)
                                @endif
                            </span>
                        </div>

                        <div class="text-xs md:text-sm flex flex-col gap-1.5 border-t border-gray-200 dark:border-border pt-3 mt-2 text-text-secondary">
                            <div>Your response:
                                <code class="font-mono bg-white dark:bg-surface-solid border border-gray-200 dark:border-border px-1.5 py-0.5 rounded text-sm {{ $item['is_correct'] ? 'text-success font-semibold' : 'text-danger font-semibold' }}">
                                    @if (is_array($item['submitted']))
                                        {{ implode(', ', $item['submitted']) }}
                                    @else
                                        {{ $item['submitted'] ?: '(empty)' }}
                                    @endif
                                </code>
                            </div>
                            <div>Correct answer:
                                <code class="font-mono bg-white dark:bg-surface-solid border border-gray-200 dark:border-border px-1.5 py-0.5 rounded text-sm text-success font-semibold">
                                    {{ implode(', ', $item['question']->correct_answers) }}
                                </code>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Bottom Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-10 border-t border-gray-200 dark:border-border pt-6">
            <a href="{{ route('courses.play', [$course->id, $quiz->lesson_id]) }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-surface-solid border border-gray-200 dark:border-border text-text-primary text-sm font-semibold rounded-lg hover:bg-gray-100 dark:hover:bg-border hover:-translate-y-0.5 transition-all duration-300 cursor-pointer">← Back to Course Player</a>

            @if (!$attempt->passed)
                <a href="{{ route('quizzes.show', [$course->id, $quiz->id]) }}"
                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-primary text-white rounded-lg font-semibold cursor-pointer shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:bg-primary-hover hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)] transition-all duration-300">Retake Quiz</a>
            @else
                @if ($hasCertificate)
                    <a href="{{ route('courses.certificate', $course->id) }}" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-br from-primary to-accent text-white rounded-lg font-semibold cursor-pointer hover:-translate-y-0.5 hover:shadow-[0_5px_15px_rgba(236,72,153,0.3)] transition-all duration-300" target="_blank">View Earned Certificate</a>
                @else
                    <a href="{{ route('courses.play', $course->id) }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-primary text-white rounded-lg font-semibold cursor-pointer shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:bg-primary-hover hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)] transition-all duration-300">Continue Course</a>
                @endif
            @endif
        </div>
    </div>
@endsection
