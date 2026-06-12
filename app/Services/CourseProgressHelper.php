<?php

namespace App\Services;

use App\Models\User;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;

class CourseProgressHelper
{
    /**
     * Calculate progress for a specific module.
     * Returns an array with total lessons, completed lessons, and percentage.
     */
    public static function getModuleProgress(Module $module, User $user): array
    {
        $lessonIds = $module->lessons()->pluck('id');
        $total = $lessonIds->count();

        if ($total === 0) {
            return [
                'total' => 0,
                'completed' => 0,
                'percentage' => 100,
            ];
        }

        // Count completed lessons/passed quizzes in this module
        $completedCount = 0;
        $lessons = $module->lessons()->with(['quiz'])->get();

        foreach ($lessons as $lesson) {
            if ($lesson->type === 'quiz') {
                if ($lesson->quiz) {
                    $bestAttempt = $lesson->quiz->userBestAttempt($user);
                    if ($bestAttempt && $bestAttempt->passed) {
                        $completedCount++;
                    }
                }
            } else {
                if ($lesson->isCompletedBy($user)) {
                    $completedCount++;
                }
            }
        }

        $percentage = round(($completedCount / $total) * 100);

        return [
            'total' => $total,
            'completed' => $completedCount,
            'percentage' => $percentage,
        ];
    }

    /**
     * Calculate overall progress for a course.
     */
    public static function getCourseProgress(Course $course, User $user): array
    {
        $lessons = $course->lessons()->with(['quiz'])->get();
        $total = $lessons->count();

        if ($total === 0) {
            return [
                'total' => 0,
                'completed' => 0,
                'percentage' => 100,
            ];
        }

        $completedCount = 0;
        foreach ($lessons as $lesson) {
            if ($lesson->type === 'quiz') {
                if ($lesson->quiz) {
                    $bestAttempt = $lesson->quiz->userBestAttempt($user);
                    if ($bestAttempt && $bestAttempt->passed) {
                        $completedCount++;
                    }
                }
            } else {
                if ($lesson->isCompletedBy($user)) {
                    $completedCount++;
                }
            }
        }

        $percentage = round(($completedCount / $total) * 100);

        return [
            'total' => $total,
            'completed' => $completedCount,
            'percentage' => $percentage,
        ];
    }

    /**
     * Calculate the weighted quiz score for a user in a course.
     * Formula: Sum(Best Score * Weight) / Sum(Weights)
     */
    public static function getCourseGrade(Course $course, User $user): array
    {
        $lessons = $course->lessons()->where('type', 'quiz')->get();
        $quizzes = [];
        foreach ($lessons as $lesson) {
            if ($lesson->quiz) {
                $quizzes[] = $lesson->quiz;
            }
        }

        if (count($quizzes) === 0) {
            return [
                'score' => 100.0,
                'total_weight' => 0,
                'has_quizzes' => false,
            ];
        }

        $sumWeightedScores = 0.0;
        $sumWeights = 0;

        foreach ($quizzes as $quiz) {
            $bestAttempt = $quiz->userBestAttempt($user);
            $score = $bestAttempt ? (float)$bestAttempt->score : 0.0;
            $weight = (int)$quiz->weight;

            $sumWeightedScores += ($score * $weight);
            $sumWeights += $weight;
        }

        $finalScore = $sumWeights > 0 ? round($sumWeightedScores / $sumWeights, 2) : 0.0;

        return [
            'score' => $finalScore,
            'total_weight' => $sumWeights,
            'has_quizzes' => true,
        ];
    }
}
