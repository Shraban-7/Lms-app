@extends('layouts.app')

@section('title')
    {{ $lesson->title }} - Player
@endsection

@section('styles')
    <style>
        /* Clean up main padding for immersive player */
        main>.container {
            max-width: 100% !important;
            margin-top: 0 !important;
            padding: 0 !important;
        }
    </style>
@endsection

@section('content')
    <div class="grid grid-cols-1 min-[901px]:grid-cols-[350px_1fr] h-auto min-[901px]:h-[calc(100vh-73px)] overflow-visible min-[901px]:overflow-hidden">

        <!-- Player Sidebar: Module/Lesson outline -->
        <aside class="border-r border-gray-200 dark:border-border bg-surface-solid overflow-y-auto p-6 flex flex-col gap-6 h-auto min-[901px]:h-full">
            <div>
                <h2 class="text-lg font-extrabold text-text-primary mb-2 truncate">
                    {{ $course->title }}
                </h2>
                <div class="flex justify-between items-center text-xs text-text-secondary mb-1">
                    <span>Course Progress</span>
                    <strong id="sidebarProgressText" class="font-bold">{{ $progressInfo['percentage'] }}%</strong>
                </div>
                <div class="w-full h-1.5 bg-gray-200 dark:bg-border rounded-full overflow-hidden mb-2">
                    <div class="h-full bg-gradient-to-r from-primary to-accent rounded-full transition-[width] duration-500" id="sidebarProgressBar" style="width: {{ $progressInfo['percentage'] }}%;">
                    </div>
                </div>
            </div>

            <div class="flex-grow overflow-y-auto pr-1">
                @foreach ($course->modules as $modIndex => $module)
                    <div class="border border-gray-200 dark:border-border rounded-lg overflow-hidden mb-3 bg-white/40 dark:bg-surface/30">
                        <div class="bg-white/70 dark:bg-surface/60 border-b border-gray-200 dark:border-border p-3 font-bold text-xs text-text-primary flex items-center justify-between cursor-pointer">
                            <span>{{ $module->title }}</span>
                        </div>
                        <ul class="list-none bg-gray-50 dark:bg-[#0B0F19] divide-y divide-gray-200 dark:divide-border">
                            @foreach ($module->lessons as $les)
                                <li>
                                    <a href="{{ route('courses.play', [$course->id, $les->id]) }}"
                                        class="flex items-center justify-between px-4 py-2.5 text-xs transition-all duration-200 hover:bg-primary/10 hover:text-text-primary {{ $lesson->id === $les->id ? 'bg-primary/10 text-text-primary font-semibold' : 'text-text-secondary' }}"
                                        id="lesson-link-{{ $les->id }}">

                                        <div class="flex items-center gap-2 max-w-[80%]">
                                            <!-- Complete icon status indicator -->
                                            @if ($les->type === 'quiz')
                                                @php
                                                    $quizBest = $les->quiz
                                                        ? $les->quiz->userBestAttempt(auth()->user())
                                                        : null;
                                                    $quizPassed = $quizBest && $quizBest->passed;
                                                @endphp
                                                <span class="quiz-indicator flex items-center {{ $quizPassed ? 'text-success' : 'text-text-muted' }}" id="indicator-{{ $les->id }}">
                                                    <svg width="14" height="14" fill="none" stroke="currentColor"
                                                        stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path
                                                            d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z" />
                                                        <path d="m9 12 2 2 4-4" />
                                                    </svg>
                                                </span>
                                            @else
                                                @php $isDone = $les->isCompletedBy(auth()->user()); @endphp
                                                <span class="complete-indicator flex items-center {{ $isDone ? 'text-success' : 'text-text-muted' }}" id="indicator-{{ $les->id }}">
                                                    <svg width="14" height="14" fill="none" stroke="currentColor"
                                                        stroke-width="2.5" viewBox="0 0 24 24">
                                                        <polyline points="20 6 9 17 4 12" />
                                                    </svg>
                                                </span>
                                            @endif
                                            <span class="truncate">{{ $les->title }}</span>
                                        </div>
                                        <span class="text-[10px] text-text-muted">
                                            {{ $les->type === 'video' ? 'Video' : ($les->type === 'quiz' ? 'Quiz' : 'Text') }}
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-gray-200 dark:border-border pt-4">
                <a href="{{ route('courses.show', $course->id) }}"
                    class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-surface-solid border border-gray-200 dark:border-border text-text-primary text-xs font-semibold rounded-lg hover:bg-gray-100 dark:hover:bg-border hover:-translate-y-0.5 transition-all duration-300 cursor-pointer">
                    Back to Overview
                </a>
            </div>
        </aside>

        <!-- Player Content Window -->
        <section class="overflow-y-auto p-6 md:p-8 flex flex-col gap-6 h-auto min-[901px]:h-full">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-gray-200 dark:border-border gap-4">
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-primary/20 text-primary mb-1">{{ $lesson->type }} Lesson</span>
                    <h1 class="text-2xl font-extrabold text-text-primary mt-1">{{ $lesson->title }}</h1>
                </div>

                <!-- Complete Toggle Button (Only for text/video) -->
                @if ($lesson->type !== 'quiz')
                    @php $isLessonCompleted = $lesson->isCompletedBy(auth()->user()); @endphp
                    <button class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 {{ $isLessonCompleted ? 'bg-surface-solid border border-gray-200 dark:border-border text-text-primary hover:bg-gray-100 dark:hover:bg-border' : 'bg-primary text-white shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:bg-primary-hover hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)]' }} text-sm"
                        id="btnToggleComplete">
                        {{ $isLessonCompleted ? 'Mark Incomplete' : 'Mark Lesson Complete' }}
                    </button>
                @endif
            </div>

            <!-- Video Player viewport -->
            @if ($lesson->type === 'video')
                <div class="video-wrapper">
                    <video class="custom-video" id="courseVideo" controls src="{{ $lesson->video_url }}"></video>
                    <!-- Custom controls overlay styled by Vanilla CSS -->
                    <div class="video-controls" id="customVideoControls">
                        <div class="video-progress-container" id="videoProgress">
                            <div class="video-progress-bar" id="videoProgressBar"></div>
                        </div>
                        <div class="video-buttons-row">
                            <div class="video-controls-left">
                                <span class="video-btn" id="videoPlayBtn">Play</span>
                                <span class="video-btn" id="videoSkipBack">⏪ 10s</span>
                                <span class="video-btn" id="videoSkipForward">10s ⏩</span>
                                <span style="font-size: 0.8rem;" id="videoTime">0:00 / 0:00</span>
                            </div>
                            <div class="video-controls-right">
                                <select id="videoSpeed" class="bg-black/60 border border-gray-600 dark:border-border text-xs rounded px-2 py-1 text-white cursor-pointer focus:outline-none">
                                    <option value="0.5">0.5x</option>
                                    <option value="1" selected>1.0x</option>
                                    <option value="1.25">1.25x</option>
                                    <option value="1.5">1.5x</option>
                                    <option value="2">2.0x</option>
                                </select>
                                <span class="video-btn" id="videoFullscreen">Fullscreen</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Lesson Text content -->
            <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 md:p-8 shadow-sm dark:shadow-card leading-relaxed text-text-primary">
                @if ($lesson->type === 'quiz')
                    <!-- Quiz Details Preview -->
                    <div class="text-center py-8">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                            class="text-primary mx-auto mb-6">
                            <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z" />
                            <path d="m9 12 2 2 4-4" />
                        </svg>
                        <h2 class="text-2xl font-extrabold text-text-primary mb-2">{{ $lesson->quiz->title }}</h2>
                        <p class="text-text-secondary text-sm md:text-base max-w-lg mx-auto mb-8">
                            This is a graded assessment. You need at least
                            <strong>{{ $lesson->quiz->passing_score }}%</strong> to pass and qualify for the course
                            completion certificate.
                        </p>

                        @php
                            $best = $lesson->quiz->userBestAttempt(auth()->user());
                        @endphp

                        @if ($best)
                            <div class="max-w-[400px] mx-auto mb-8 bg-white/70 dark:bg-surface/60 backdrop-blur-xl border rounded-[14px] p-6 shadow-sm" style="{{ $best->passed ? 'border-color: var(--color-success);' : 'border-color: var(--color-danger);' }}">
                                <p class="text-xs text-text-secondary uppercase font-semibold tracking-wider mb-1">Your Best Score</p>
                                <h3 class="text-2xl font-extrabold" style="{{ $best->passed ? 'color: var(--color-success);' : 'color: var(--color-danger);' }}">
                                    {{ $best->score }}% ({{ $best->passed ? 'PASSED' : 'FAILED' }})
                                </h3>
                            </div>
                        @endif

                        <a href="{{ route('quizzes.show', [$course->id, $lesson->quiz->id]) }}"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white rounded-lg font-semibold cursor-pointer shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:bg-primary-hover hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)] transition-all duration-300">
                            {{ $best ? 'Retake Assessment' : 'Start Assessment' }}
                        </a>
                    </div>
                @else
                    {!! nl2br(e($lesson->content_text)) !!}
                @endif
            </div>

        </section>
    </div>
@endsection

@section('scripts')
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Complete Lesson Toggle Logic
        const btnToggleComplete = document.getElementById('btnToggleComplete');
        if (btnToggleComplete) {
            btnToggleComplete.addEventListener('click', () => {
                fetch("{{ route('courses.complete-lesson', [$course->id, $lesson->id]) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'completed') {
                            btnToggleComplete.innerText = 'Mark Incomplete';
                            btnToggleComplete.classList.remove('bg-primary', 'text-white', 'shadow-[0_4px_14px_rgba(99,102,241,0.4)]', 'hover:bg-primary-hover', 'hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)]');
                            btnToggleComplete.classList.add('bg-surface-solid', 'border', 'border-gray-200', 'dark:border-border', 'text-text-primary', 'hover:bg-gray-100', 'dark:hover:bg-border');

                            const indicator = document.getElementById("indicator-{{ $lesson->id }}");
                            if (indicator) {
                                indicator.classList.remove('text-text-muted');
                                indicator.classList.add('text-success');
                            }
                        } else if (data.status === 'incomplete') {
                            btnToggleComplete.innerText = 'Mark Lesson Complete';
                            btnToggleComplete.classList.remove('bg-surface-solid', 'border', 'border-gray-200', 'dark:border-border', 'text-text-primary', 'hover:bg-gray-100', 'dark:hover:bg-border');
                            btnToggleComplete.classList.add('bg-primary', 'text-white', 'shadow-[0_4px_14px_rgba(99,102,241,0.4)]', 'hover:bg-primary-hover', 'hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)]');

                            const indicator = document.getElementById("indicator-{{ $lesson->id }}");
                            if (indicator) {
                                indicator.classList.remove('text-success');
                                indicator.classList.add('text-text-muted');
                            }
                        }

                        if (data.progress !== undefined) {
                            document.getElementById('sidebarProgressText').innerText = data.progress + '%';
                            document.getElementById('sidebarProgressBar').style.width = data.progress + '%';
                        }

                        if (data.certificate_earned) {
                            alert(data.message);
                            window.location.reload();
                        }
                    });
            });
        }

        // Video Player & Playback Position Logic
        const video = document.getElementById('courseVideo');
        if (video) {
            // Restore position
            const restoredPos = parseFloat("{{ $playbackPosition }}");
            if (restoredPos > 0) {
                video.currentTime = restoredPos;
            }

            // Periodically save playback position
            let lastSavedTime = 0;
            video.addEventListener('timeupdate', () => {
                const currentTime = Math.floor(video.currentTime);
                if (currentTime !== lastSavedTime && currentTime % 5 === 0) {
                    lastSavedTime = currentTime;
                    fetch("{{ route('lessons.playback', $lesson->id) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            position: video.currentTime
                        })
                    });
                }
            });

            // HTML5 custom controls mapping
            const playBtn = document.getElementById('videoPlayBtn');
            const skipBack = document.getElementById('videoSkipBack');
            const skipForward = document.getElementById('videoSkipForward');
            const speedSel = document.getElementById('videoSpeed');
            const timeLbl = document.getElementById('videoTime');
            const progressBar = document.getElementById('videoProgressBar');
            const progressCont = document.getElementById('videoProgress');
            const fullscreen = document.getElementById('videoFullscreen');

            if (playBtn) {
                playBtn.addEventListener('click', () => {
                    if (video.paused) {
                        video.play();
                        playBtn.innerText = 'Pause';
                    } else {
                        video.pause();
                        playBtn.innerText = 'Play';
                    }
                });
                video.addEventListener('play', () => playBtn.innerText = 'Pause');
                video.addEventListener('pause', () => playBtn.innerText = 'Play');
            }

            if (skipBack) skipBack.addEventListener('click', () => video.currentTime = Math.max(0, video.currentTime - 10));
            if (skipForward) skipForward.addEventListener('click', () => video.currentTime = Math.min(video.duration, video.currentTime + 10));
            if (speedSel) speedSel.addEventListener('change', (e) => video.playbackRate = parseFloat(e.target.value));
            if (fullscreen) fullscreen.addEventListener('click', () => {
                if (video.requestFullscreen) video.requestFullscreen();
                else if (video.webkitRequestFullscreen) video.webkitRequestFullscreen();
            });

            // Time updates
            video.addEventListener('timeupdate', () => {
                const formatTime = (time) => {
                    const min = Math.floor(time / 60);
                    const sec = Math.floor(time % 60);
                    return min + ':' + (sec < 10 ? '0' : '') + sec;
                };
                if (timeLbl && video.duration) {
                    timeLbl.innerText = formatTime(video.currentTime) + ' / ' + formatTime(video.duration);
                }
                if (progressBar && video.duration) {
                    const percent = (video.currentTime / video.duration) * 100;
                    progressBar.style.width = percent + '%';
                }
            });

            // Click seek
            if (progressCont) {
                progressCont.addEventListener('click', (e) => {
                    const rect = progressCont.getBoundingClientRect();
                    const pos = (e.clientX - rect.left) / rect.width;
                    video.currentTime = pos * video.duration;
                });
            }
        }
    </script>
@endsection
