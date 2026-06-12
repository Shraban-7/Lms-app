@extends('layouts.app')

@section('title', 'Curriculum Tree Builder - Aether LMS')

@section('content')
<header class="flex flex-col md:flex-row md:items-end justify-between border-b border-gray-200 dark:border-border pb-6 mb-10 gap-4">
    <div>
        <div class="flex items-center gap-2 mb-2 text-sm">
            <a href="{{ route('builder.index') }}" class="text-text-secondary hover:text-primary transition-colors font-medium">← Back to Courses</a>
            <span class="text-text-muted">&bull;</span>
            @if($course->is_published)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-success/15 text-success">Published</span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-warning/15 text-warning">Draft</span>
            @endif
        </div>
        <h1 class="text-3xl font-extrabold text-text-primary">Syllabus Builder: {{ $course->title }}</h1>
        <p class="text-text-secondary">Add modules, arrange lessons, design interactive quizzes, and control weighted grading.</p>
    </div>

    <div>
        <form action="{{ route('builder.toggle-publish', $course->id) }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg font-semibold cursor-pointer transition-all duration-300 {{ $course->is_published ? 'bg-surface-solid border border-gray-200 dark:border-border text-text-primary hover:bg-gray-100 dark:hover:bg-border' : 'bg-primary text-white shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:bg-primary-hover hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)]' }} text-sm">
                {{ $course->is_published ? 'Unpublish Course' : 'Publish Course' }}
            </button>
        </form>
    </div>
</header>

<div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-8">
    
    <!-- Left Section: Main Tree Hierarchy -->
    <div class="flex flex-col gap-6 builder-tree-container">
        
        @if($course->modules->isEmpty())
            <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-8 md:p-16 text-center text-text-secondary shadow-sm dark:shadow-card">
                <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="text-text-secondary mx-auto mb-4">
                    <path d="M12 4v16m-8-8h16"/>
                </svg>
                <h3 class="text-lg font-bold text-text-primary">Your syllabus is empty</h3>
                <p class="text-text-secondary mt-2 text-sm">Create a module using the form on the right to start building your hierarchical course structure.</p>
            </div>
        @else
            @foreach($course->modules as $modIndex => $module)
                <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-primary/30 transition-all duration-200" id="module-{{ $module->id }}">
                    <!-- Module Header -->
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-200 dark:border-border gap-4">
                        <div>
                            <span class="text-xs font-bold uppercase text-primary">Module {{ $modIndex + 1 }}</span>
                            <h3 class="text-xl font-extrabold text-text-primary mt-1">{{ $module->title }}</h3>
                        </div>

                        <!-- Ordering controls & actions -->
                        <div class="flex items-center gap-1.5">
                            <button onclick="reorderItem('module', {{ $module->id }}, 'up')" class="inline-flex items-center justify-center w-8 h-8 bg-surface-solid border border-gray-200 dark:border-border text-text-primary text-xs font-semibold rounded hover:bg-gray-100 dark:hover:bg-border transition-all duration-300 cursor-pointer">▲</button>
                            <button onclick="reorderItem('module', {{ $module->id }}, 'down')" class="inline-flex items-center justify-center w-8 h-8 bg-surface-solid border border-gray-200 dark:border-border text-text-primary text-xs font-semibold rounded hover:bg-gray-100 dark:hover:bg-border transition-all duration-300 cursor-pointer">▼</button>
                            <button onclick="openLessonModal({{ $module->id }})" class="inline-flex items-center justify-center px-3 py-1.5 bg-primary text-white text-xs font-semibold rounded hover:bg-primary-hover hover:-translate-y-0.5 shadow-sm transition-all duration-300 cursor-pointer">+ Add Lesson</button>
                        </div>
                    </div>

                    <!-- Lessons List inside Module with Timeline Connector Line -->
                    @if($module->lessons->isEmpty())
                        <p class="text-text-muted text-sm italic py-2 pl-2">No lessons in this module yet.</p>
                    @else
                        <div class="relative ml-5 pl-6 border-l-2 border-dashed border-gray-200 dark:border-border/60 flex flex-col gap-4 my-2">
                            @foreach($module->lessons as $lesIndex => $lesson)
                                <div class="relative group/lesson" id="lesson-{{ $lesson->id }}">
                                    <!-- Timeline Node & Connector Line -->
                                    <div class="absolute -left-6 top-[22px] w-6 h-[2px] bg-gray-200 dark:bg-border/60 group-hover/lesson:bg-primary/50 transition-colors duration-300"></div>
                                    <div class="absolute -left-[31px] top-[16px] w-3 h-3 rounded-full border-2 border-gray-300 dark:border-border bg-surface-solid group-hover/lesson:border-primary shadow-[0_0_8px_rgba(0,0,0,0.05)] group-hover/lesson:shadow-[0_0_12px_var(--primary-glow)] transition-all duration-300 z-10"></div>
                                    
                                    <div class="bg-surface-solid border border-gray-200 dark:border-border rounded-lg p-4 flex flex-col gap-3.5 hover:border-primary/40 hover:shadow-[0_4px_12px_rgba(0,0,0,0.02)] transition-all duration-300">
                                        <div class="flex items-center justify-between gap-4">
                                            <div class="flex items-center gap-3">
                                                <span class="text-xs text-text-muted">{{ $modIndex + 1 }}.{{ $lesIndex + 1 }}</span>
                                                
                                                <!-- Type Badge -->
                                                @if($lesson->type === 'video')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-primary/20 text-primary">Video</span>
                                                @elseif($lesson->type === 'quiz')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-success/15 text-success">Quiz</span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-gray-100 dark:bg-border text-text-secondary">Text</span>
                                                @endif

                                                <strong class="text-sm font-semibold text-text-primary">{{ $lesson->title }}</strong>
                                            </div>

                                            <!-- Ordering & details -->
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-xs text-text-muted mr-1.5">{{ $lesson->duration_minutes }} min</span>
                                                <button onclick="reorderItem('lesson', {{ $lesson->id }}, 'up', {{ $module->id }})" class="inline-flex items-center justify-center w-6 h-6 bg-surface-solid border border-gray-200 dark:border-border text-text-primary text-[10px] font-semibold rounded hover:bg-gray-100 dark:hover:bg-border transition-all duration-300 cursor-pointer">▲</button>
                                                <button onclick="reorderItem('lesson', {{ $lesson->id }}, 'down', {{ $module->id }})" class="inline-flex items-center justify-center w-6 h-6 bg-surface-solid border border-gray-200 dark:border-border text-text-primary text-[10px] font-semibold rounded hover:bg-gray-100 dark:hover:bg-border transition-all duration-300 cursor-pointer">▼</button>
                                            </div>
                                        </div>

                                        <!-- If Lesson is Quiz: show questions & add question controls -->
                                        @if($lesson->type === 'quiz' && $lesson->quiz)
                                            <div class="mt-2 bg-[#0b0f19]/20 border border-gray-200 dark:border-border rounded-lg p-4">
                                                <div class="flex items-center justify-between pb-2 mb-3 border-b border-dashed border-gray-200 dark:border-border gap-4">
                                                    <span class="text-xs font-bold text-text-secondary">Quiz: <strong class="text-text-primary">{{ $lesson->quiz->title }}</strong> (Passing: {{ $lesson->quiz->passing_score }}%, Weight: {{ $lesson->quiz->weight }})</span>
                                                    <button onclick="openQuestionModal({{ $lesson->quiz->id }})" class="inline-flex items-center justify-center px-2.5 py-1 bg-surface-solid border border-gray-200 dark:border-border text-text-primary text-xs font-semibold rounded hover:bg-gray-100 dark:hover:bg-border transition-all duration-300 cursor-pointer">+ Add Question</button>
                                                </div>

                                                <!-- Quiz Questions List -->
                                                <div class="flex flex-col gap-2.5">
                                                    @if($lesson->quiz->questions->isEmpty())
                                                        <p class="text-text-muted text-xs italic">No questions added yet.</p>
                                                    @else
                                                        @foreach($lesson->quiz->questions as $qIndex => $question)
                                                            <div class="text-xs p-3 rounded bg-white/50 dark:bg-surface/50 border border-gray-200 dark:border-border flex flex-col gap-1.5">
                                                                <div class="flex justify-between items-start gap-4">
                                                                    <strong class="font-bold text-text-primary">Q{{ $qIndex + 1 }}: {{ $question->question_text }}</strong>
                                                                    <span class="text-primary font-bold whitespace-nowrap">{{ $question->points }} pts ({{ str_replace('_', ' ', $question->type) }})</span>
                                                                </div>
                                                                <div class="text-[10px] text-text-secondary mt-1">
                                                                    Correct answer(s): <code class="font-mono bg-white dark:bg-surface-solid px-1 py-0.5 rounded border border-gray-150 dark:border-border text-text-primary">{{ implode(', ', $question->correct_answers) }}</code>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>

    <!-- Right Section: Sidebar Forms to Add Content -->
    <aside class="flex flex-col gap-6">
        
        <!-- Add Module Card -->
        <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-0.5 transition-all duration-300 flex flex-col">
            <h3 class="text-lg font-extrabold text-text-primary mb-4">Create Module</h3>
            
            <form action="{{ route('builder.add-module', $course->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block font-semibold mb-1.5 text-xs uppercase tracking-wide text-text-secondary" for="mod_title">Module Title</label>
                    <input class="w-full py-2.5 px-3.5 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" type="text" id="mod_title" name="title" placeholder="e.g. Getting Started" required>
                </div>
                <div class="mb-4">
                    <label class="block font-semibold mb-1.5 text-xs uppercase tracking-wide text-text-secondary" for="mod_desc">Module Description</label>
                    <textarea class="w-full py-2.5 px-3.5 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" id="mod_desc" name="description" rows="3" placeholder="Syllabus scope..."></textarea>
                </div>
                <button type="submit" class="w-full mt-2 inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-primary text-white rounded-lg font-semibold cursor-pointer shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:bg-primary-hover hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)] transition-all duration-300 text-sm">Add Module</button>
            </form>
        </div>

    </aside>
</div>

<!-- Add Lesson Modal -->
<div id="lessonModal" style="display: none;" class="fixed inset-0 bg-black/65 z-[100] backdrop-blur-md items-center justify-center p-4">
    <div class="bg-white/95 dark:bg-[#141A2D]/95 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[20px] p-6 md:p-8 shadow-2xl w-full max-w-[550px] max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-extrabold text-text-primary mb-6" id="lessonModalTitle">Add Lesson</h3>
        
        <form id="lessonForm" method="POST" action="">
            @csrf
            
            <div class="mb-4">
                <label class="block font-semibold mb-1.5 text-xs uppercase tracking-wide text-text-secondary" for="les_title">Lesson Title</label>
                <input class="w-full py-2.5 px-3.5 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" type="text" id="les_title" name="title" required>
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1.5 text-xs uppercase tracking-wide text-text-secondary" for="les_type">Lesson Type</label>
                <select class="w-full py-2.5 px-3.5 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)] cursor-pointer" name="type" id="les_type" onchange="toggleLessonFields()" required>
                    <option value="text">Text Content</option>
                    <option value="video">Video Streaming</option>
                    <option value="quiz">Interactive Quiz</option>
                </select>
            </div>

            <div class="mb-4" id="fieldVideoUrl" style="display: none;">
                <label class="block font-semibold mb-1.5 text-xs uppercase tracking-wide text-text-secondary" for="les_video">Video Streaming URL (Direct MP4 URL)</label>
                <input class="w-full py-2.5 px-3.5 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" type="text" id="les_video" name="video_url" placeholder="https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4">
            </div>

            <div class="mb-4" id="fieldTextContent">
                <label class="block font-semibold mb-1.5 text-xs uppercase tracking-wide text-text-secondary" for="les_content">Text Content Body</label>
                <textarea class="w-full py-2.5 px-3.5 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" id="les_content" name="content_text" rows="4" placeholder="Enter lesson notes or markdown content..."></textarea>
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1.5 text-xs uppercase tracking-wide text-text-secondary" for="les_duration">Duration (Minutes)</label>
                <input class="w-full py-2.5 px-3.5 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" type="number" id="les_duration" name="duration_minutes" value="10" required>
            </div>

            <div class="flex justify-end gap-3 mt-8">
                <button type="button" onclick="closeLessonModal()" class="inline-flex items-center justify-center px-4 py-2 bg-surface-solid border border-gray-200 dark:border-border text-text-primary text-sm font-semibold rounded-lg hover:bg-gray-100 dark:hover:bg-border hover:-translate-y-0.5 transition-all duration-300 cursor-pointer">Cancel</button>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-hover hover:-translate-y-0.5 shadow-sm transition-all duration-300 cursor-pointer">Save Lesson</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Question Modal -->
<div id="questionModal" style="display: none;" class="fixed inset-0 bg-black/65 z-[100] backdrop-blur-md items-center justify-center p-4">
    <div class="bg-white/95 dark:bg-[#141A2D]/95 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[20px] p-6 md:p-8 shadow-2xl w-full max-w-[550px] max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-extrabold text-text-primary mb-6">Add Quiz Question</h3>
        
        <form id="questionForm" method="POST" action="">
            @csrf
            
            <div class="mb-4">
                <label class="block font-semibold mb-1.5 text-xs uppercase tracking-wide text-text-secondary" for="q_text">Question Prompt</label>
                <textarea class="w-full py-2.5 px-3.5 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" id="q_text" name="question_text" rows="2" required></textarea>
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1.5 text-xs uppercase tracking-wide text-text-secondary" for="q_type">Question Type</label>
                <select class="w-full py-2.5 px-3.5 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)] cursor-pointer" name="type" id="q_type" onchange="toggleQuestionFields()" required>
                    <option value="single_choice">Single Choice (Radio)</option>
                    <option value="multiple_choice">Multiple Choice (Checkboxes)</option>
                    <option value="true_false">True / False</option>
                    <option value="short_answer">Short Text Match</option>
                </select>
            </div>

            <!-- Options (Choices) -->
            <div id="optionsContainer" class="mb-4">
                <div class="mb-4">
                    <label class="block font-semibold mb-1.5 text-xs uppercase tracking-wide text-text-secondary">Answer Options (One per line)</label>
                    <textarea class="w-full py-2.5 px-3.5 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" id="q_options" name="options[]" rows="4" placeholder="Option A&#10;Option B&#10;Option C&#10;Option D"></textarea>
                </div>
            </div>

            <!-- Correct Answers -->
            <div class="mb-4">
                <label class="block font-semibold mb-1.5 text-xs uppercase tracking-wide text-text-secondary" id="lblCorrectAnswers">Correct Answer (For multiple choice, list one per line)</label>
                <textarea class="w-full py-2.5 px-3.5 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" id="q_correct" name="correct_answers[]" rows="2" placeholder="e.g. Option A (must match spelling exactly)" required></textarea>
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1.5 text-xs uppercase tracking-wide text-text-secondary" for="q_points">Points</label>
                <input class="w-full py-2.5 px-3.5 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" type="number" id="q_points" name="points" value="1" required>
            </div>

            <div class="flex justify-end gap-3 mt-8">
                <button type="button" onclick="closeQuestionModal()" class="inline-flex items-center justify-center px-4 py-2 bg-surface-solid border border-gray-200 dark:border-border text-text-primary text-sm font-semibold rounded-lg hover:bg-gray-100 dark:hover:bg-border hover:-translate-y-0.5 transition-all duration-300 cursor-pointer">Cancel</button>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-hover hover:-translate-y-0.5 shadow-sm transition-all duration-300 cursor-pointer">Save Question</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Lesson Modal Controls
    function openLessonModal(moduleId) {
        document.getElementById('lessonForm').action = `/instructor/modules/${moduleId}/lessons`;
        document.getElementById('lessonModal').style.display = 'flex';
        toggleLessonFields();
    }
    function closeLessonModal() {
        document.getElementById('lessonModal').style.display = 'none';
    }
    function toggleLessonFields() {
        const type = document.getElementById('les_type').value;
        const fieldVideo = document.getElementById('fieldVideoUrl');
        const fieldText = document.getElementById('fieldTextContent');
        
        if (type === 'video') {
            fieldVideo.style.display = 'block';
            fieldText.style.display = 'none';
        } else if (type === 'text') {
            fieldVideo.style.display = 'none';
            fieldText.style.display = 'block';
        } else {
            // Quiz
            fieldVideo.style.display = 'none';
            fieldText.style.display = 'none';
        }
    }

    // Question Modal Controls
    function openQuestionModal(quizId) {
        document.getElementById('questionForm').action = `/instructor/quizzes/${quizId}/questions`;
        document.getElementById('questionModal').style.display = 'flex';
        toggleQuestionFields();
    }
    function closeQuestionModal() {
        document.getElementById('questionModal').style.display = 'none';
    }
    function toggleQuestionFields() {
        const type = document.getElementById('q_type').value;
        const optContainer = document.getElementById('optionsContainer');
        const qOptionsTextarea = document.getElementById('q_options');
        const lblCorrectAnswers = document.getElementById('lblCorrectAnswers');
        const qCorrectTextarea = document.getElementById('q_correct');

        if (type === 'single_choice' || type === 'multiple_choice') {
            optContainer.style.display = 'block';
            lblCorrectAnswers.innerText = type === 'single_choice' 
                ? 'Correct Answer (Must match one of the options above)' 
                : 'Correct Answer(s) (List each correct answer on a new line)';
            qOptionsTextarea.setAttribute('required', 'true');
        } else if (type === 'true_false') {
            optContainer.style.display = 'none';
            qOptionsTextarea.removeAttribute('required');
            lblCorrectAnswers.innerText = 'Correct Answer (type exactly: true OR false)';
            qCorrectTextarea.placeholder = 'e.g. true';
        } else if (type === 'short_answer') {
            optContainer.style.display = 'none';
            qOptionsTextarea.removeAttribute('required');
            lblCorrectAnswers.innerText = 'Correct Answer text (If multiple acceptable answers, list one per line)';
            qCorrectTextarea.placeholder = 'e.g. Hydrogen';
        }
    }

    // Intercept question submits to format choices array
    document.getElementById('questionForm').addEventListener('submit', function(e) {
        const type = document.getElementById('q_type').value;
        const qOptionsTextarea = document.getElementById('q_options');
        const qCorrectTextarea = document.getElementById('q_correct');

        // Split correct answers lines into separate inputs
        const correctLines = qCorrectTextarea.value.split('\n').map(l => l.trim()).filter(l => l !== '');
        
        // Remove old correct_answers inputs
        const form = this;
        const existingCorrect = form.querySelectorAll('input[name="correct_answers[]"]');
        existingCorrect.forEach(el => el.remove());

        correctLines.forEach(line => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'correct_answers[]';
            input.value = line;
            form.appendChild(input);
        });

        // Parse options array if choices type
        if (type === 'single_choice' || type === 'multiple_choice') {
            const optionLines = qOptionsTextarea.value.split('\n').map(l => l.trim()).filter(l => l !== '');
            const existingOptions = form.querySelectorAll('input[name="options[]"]');
            existingOptions.forEach(el => el.remove());

            optionLines.forEach(line => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'options[]';
                input.value = line;
                form.appendChild(input);
            });
        }
        
        // Remove correct_answers[] and options[] textareas from name submission to prevent empty arrays overriding hidden fields
        qCorrectTextarea.removeAttribute('name');
        qOptionsTextarea.removeAttribute('name');
    });

    // Reorder Elements
    function reorderItem(type, id, direction, parentId = null) {
        let container, items, route;
        
        if (type === 'module') {
            container = document.querySelector('.builder-tree-container');
            items = Array.from(container.querySelectorAll('[id^="module-"]'));
            route = `/instructor/courses/{{ $course->id }}/reorder-modules`;
        } else {
            const modCard = document.getElementById(`module-${parentId}`);
            items = Array.from(modCard.querySelectorAll('[id^="lesson-"]'));
            route = `/instructor/modules/${parentId}/reorder-lessons`;
        }

        const index = items.findIndex(el => el.id === `${type}-${id}`);
        if (index === -1) return;

        if (direction === 'up' && index > 0) {
            // Swap with previous
            const temp = items[index];
            items[index] = items[index - 1];
            items[index - 1] = temp;
        } else if (direction === 'down' && index < items.length - 1) {
            // Swap with next
            const temp = items[index];
            items[index] = items[index + 1];
            items[index + 1] = temp;
        } else {
            return; // Can't move
        }

        // Send to backend
        const orders = items.map(el => parseInt(el.id.split('-')[1]));
        
        fetch(route, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({ orders: orders })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload(); // Quick visual reload to reconstruct orders & indices
            }
        });
    }
</script>
@endsection
