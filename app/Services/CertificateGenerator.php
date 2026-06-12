<?php

namespace App\Services;

use App\Models\User;
use App\Models\Course;
use App\Models\Certificate;
use Illuminate\Support\Str;

class CertificateGenerator
{
    /**
     * Check eligibility and generate certificate if eligible.
     */
    public static function checkAndGenerate(Course $course, User $user): ?Certificate
    {
        // 1. Check if already exists
        $existing = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        // 2. Check course progress
        $progress = CourseProgressHelper::getCourseProgress($course, $user);
        if ($progress['percentage'] < 100) {
            return null;
        }

        // 3. Optional: check final grade threshold (e.g. 70%)
        $gradeInfo = CourseProgressHelper::getCourseGrade($course, $user);
        if ($gradeInfo['has_quizzes'] && $gradeInfo['score'] < 70) {
            return null; // Must score 70% or more on average if quizzes exist
        }

        // 4. Create and issue certificate
        $code = 'LMS-' . str_pad($course->id, 4, '0', STR_PAD_LEFT) . '-' . str_pad($user->id, 5, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(6));

        return Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'verification_code' => $code,
            'issued_at' => now(),
        ]);
    }
}
