<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CourseBuilderController extends Controller
{
    public function index()
    {
        $courses = Auth::user()->courses()->withCount('lessons')->get();
        return view('instructor.courses', compact('courses'));
    }

    public function create()
    {
        return view('instructor.create_course');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'thumbnail' => 'nullable|url',
        ]);

        $course = Course::create([
            'instructor_id' => Auth::id(),
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . rand(1000, 9999),
            'description' => $request->description,
            'price' => $request->price,
            'thumbnail' => $request->thumbnail ?: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=600',
            'is_published' => false,
        ]);

        return redirect()->route('builder.edit', $course->id)->with('success', 'Course created! Start building your structure.');
    }

    public function edit(Course $course)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $course->load(['modules.lessons.quiz.questions']);
        return view('instructor.builder', compact('course'));
    }

    public function togglePublish(Course $course)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $course->update([
            'is_published' => !$course->is_published
        ]);

        $status = $course->is_published ? 'published' : 'unpublished';
        return back()->with('success', "Course {$status} successfully!");
    }

    public function addModule(Request $request, Course $course)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $order = $course->modules()->count();

        Module::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'description' => $request->description,
            'order' => $order,
        ]);

        return back()->with('success', 'Module added successfully.');
    }

    public function addLesson(Request $request, Module $module)
    {
        if ($module->course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:text,video,quiz',
            'content_text' => 'nullable|string',
            'video_url' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        $order = $module->lessons()->count();

        $lesson = Lesson::create([
            'module_id' => $module->id,
            'title' => $request->title,
            'type' => $request->type,
            'content_text' => $request->content_text,
            'video_url' => $request->video_url,
            'duration_minutes' => $request->duration_minutes,
            'order' => $order,
        ]);

        // If type is quiz, initialize a default quiz record
        if ($request->type === 'quiz') {
            Quiz::create([
                'lesson_id' => $lesson->id,
                'title' => $request->title . ' Quiz',
                'passing_score' => 70,
                'weight' => 1,
            ]);
        }

        return back()->with('success', 'Lesson added successfully.');
    }

    public function addQuestion(Request $request, Quiz $quiz)
    {
        if ($quiz->lesson->module->course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|string|in:single_choice,multiple_choice,true_false,short_answer',
            'points' => 'required|integer|min:1',
            'options' => 'nullable|array', // For choices
            'correct_answers' => 'required|array', // List of correct answers (even if just one)
        ]);

        Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => $request->question_text,
            'type' => $request->type,
            'points' => $request->points,
            'options' => $request->options,
            'correct_answers' => $request->correct_answers,
        ]);

        return back()->with('success', 'Question added successfully.');
    }

    public function reorderModules(Request $request, Course $course)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'required|integer|exists:modules,id',
        ]);

        foreach ($request->orders as $index => $moduleId) {
            Module::where('id', $moduleId)
                ->where('course_id', $course->id)
                ->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function reorderLessons(Request $request, Module $module)
    {
        if ($module->course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'required|integer|exists:lessons,id',
        ]);

        foreach ($request->orders as $index => $lessonId) {
            Lesson::where('id', $lessonId)
                ->where('module_id', $module->id)
                ->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
