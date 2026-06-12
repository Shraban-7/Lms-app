<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Services\CourseProgressHelper;
use App\Services\CertificateGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * View course landing page or overview.
     */
    public function show(Course $course)
    {
        $course->load(['instructor', 'modules.lessons']);
        
        $user = Auth::user();
        $progress = null;
        $hasCertificate = false;

        if ($user) {
            $progress = CourseProgressHelper::getCourseProgress($course, $user);
            $hasCertificate = $course->certificates()->where('user_id', $user->id)->exists();
        }

        return view('courses.show', compact('course', 'progress', 'hasCertificate'));
    }

    /**
     * Start/Play course lessons.
     */
    public function play(Course $course, Lesson $lesson = null)
    {
        $user = Auth::user();
        $course->load(['modules.lessons.quiz']);

        // Check if course has any lessons
        $firstModule = $course->modules->first();
        if (!$firstModule || $firstModule->lessons->isEmpty()) {
            return redirect()->route('courses.show', $course->id)
                ->with('error', 'This course does not have any lessons yet.');
        }

        if (!$lesson) {
            $lesson = $firstModule->lessons->first();
        }

        // Verify lesson belongs to course
        if ($lesson->module->course_id !== $course->id) {
            abort(404);
        }

        // Get course progress info
        $progressInfo = CourseProgressHelper::getCourseProgress($course, $user);
        
        // Get playback position if exists in session
        $playbackPosition = session("playback_{$user->id}_{$lesson->id}", 0);

        return view('courses.player', compact('course', 'lesson', 'progressInfo', 'playbackPosition'));
    }

    /**
     * Complete a lesson.
     */
    public function completeLesson(Request $request, Course $course, Lesson $lesson)
    {
        $user = Auth::user();

        // Safety check
        if ($lesson->module->course_id !== $course->id) {
            abort(404);
        }

        // Quizzes are completed when passed, not toggled manually
        if ($lesson->type === 'quiz') {
            return response()->json(['error' => 'Quizzes cannot be completed manually.'], 400);
        }

        $completed = LessonCompletion::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        if ($completed) {
            // Toggle off
            $completed->delete();
            $status = 'incomplete';
        } else {
            // Toggle on
            LessonCompletion::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'completed_at' => now(),
            ]);
            $status = 'completed';

            // Check if user has now completed the course and is eligible for a certificate
            $certificate = CertificateGenerator::checkAndGenerate($course, $user);
            if ($certificate) {
                return response()->json([
                    'status' => $status,
                    'certificate_earned' => true,
                    'code' => $certificate->verification_code,
                    'message' => 'Congratulations! You have completed the course and earned a certificate!',
                ]);
            }
        }

        $progressInfo = CourseProgressHelper::getCourseProgress($course, $user);

        return response()->json([
            'status' => $status,
            'certificate_earned' => false,
            'progress' => $progressInfo['percentage'],
        ]);
    }

    /**
     * Save playback position (API call from custom video player).
     */
    public function savePlaybackPosition(Request $request, Lesson $lesson)
    {
        $request->validate([
            'position' => 'required|numeric',
        ]);

        $user = Auth::user();
        session()->put("playback_{$user->id}_{$lesson->id}", $request->position);

        return response()->json(['success' => true]);
    }

    /**
     * View Certificate
     */
    public function certificate(Course $course)
    {
        $user = Auth::user();
        $certificate = $course->certificates()->where('user_id', $user->id)->first();

        if (!$certificate) {
            return redirect()->route('courses.show', $course->id)
                ->with('error', 'You have not earned a certificate for this course yet.');
        }

        return view('courses.certificate', compact('course', 'certificate', 'user'));
    }
}
