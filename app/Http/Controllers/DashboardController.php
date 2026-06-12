<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Certificate;
use App\Services\CourseProgressHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Find courses they have started (have at least one completion or attempt)
        // For simplicity and ease of testing, we will list all published courses,
        // and calculate progress for each, highlighting courses in progress.
        $allCourses = Course::where('is_published', true)
            ->with(['instructor', 'modules.lessons'])
            ->get();

        $enrolledCourses = [];
        $availableCourses = [];

        foreach ($allCourses as $course) {
            $progress = CourseProgressHelper::getCourseProgress($course, $user);
            $gradeInfo = CourseProgressHelper::getCourseGrade($course, $user);
            
            $courseData = [
                'course' => $course,
                'progress' => $progress['percentage'],
                'completed' => $progress['completed'],
                'total' => $progress['total'],
                'grade' => $gradeInfo['score'],
                'has_quizzes' => $gradeInfo['has_quizzes'],
            ];

            // If user has completed at least one lesson/quiz, consider it enrolled/in-progress
            // Otherwise, it is available to start.
            if ($progress['completed'] > 0) {
                $enrolledCourses[] = $courseData;
            } else {
                $availableCourses[] = $courseData;
            }
        }

        $certificates = Certificate::where('user_id', $user->id)
            ->with('course')
            ->get();

        return view('dashboard', compact('enrolledCourses', 'availableCourses', 'certificates'));
    }
}
