<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\LessonCompletion;
use App\Services\CertificateGenerator;
use App\Services\CourseProgressHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    /**
     * Show quiz page.
     */
    public function show(Course $course, Quiz $quiz)
    {
        $user = Auth::user();
        
        // Safety check
        if ($quiz->lesson->module->course_id !== $course->id) {
            abort(404);
        }

        $quiz->load('questions');
        $bestAttempt = $quiz->userBestAttempt($user);

        return view('quizzes.show', compact('course', 'quiz', 'bestAttempt'));
    }

    /**
     * Grade quiz submission.
     */
    public function submit(Request $request, Course $course, Quiz $quiz)
    {
        $user = Auth::user();
        
        if ($quiz->lesson->module->course_id !== $course->id) {
            abort(404);
        }

        $quiz->load('questions');
        
        $submittedAnswers = $request->input('answers', []); // question_id => answer value
        $totalPoints = 0;
        $earnedPoints = 0;
        $questionDetails = [];

        foreach ($quiz->questions as $question) {
            $totalPoints += $question->points;
            $correct = $question->correct_answers; // array of correct options/text
            $submitted = $submittedAnswers[$question->id] ?? null;

            $isCorrect = false;

            if ($question->type === 'single_choice' || $question->type === 'true_false') {
                // Submitted is a single string/value
                if ($submitted !== null && count($correct) > 0) {
                    $isCorrect = (strtolower(trim($submitted)) === strtolower(trim($correct[0])));
                }
            } elseif ($question->type === 'multiple_choice') {
                // Submitted is an array of checked options
                $submittedArray = is_array($submitted) ? $submitted : [$submitted];
                
                // Sort both to compare easily
                $cArray = array_map('strtolower', array_map('trim', $correct));
                $sArray = array_map('strtolower', array_map('trim', array_filter($submittedArray)));

                sort($cArray);
                sort($sArray);

                $isCorrect = ($cArray === $sArray);
            } elseif ($question->type === 'short_answer') {
                // Submitted is text. Check case-insensitive trim match with any valid answers
                if ($submitted !== null) {
                    $subText = strtolower(trim($submitted));
                    foreach ($correct as $correctVal) {
                        if ($subText === strtolower(trim($correctVal))) {
                            $isCorrect = true;
                            break;
                        }
                    }
                }
            }

            if ($isCorrect) {
                $earnedPoints += $question->points;
            }

            $questionDetails[$question->id] = [
                'question_text' => $question->question_text,
                'submitted' => $submitted,
                'correct' => $correct,
                'is_correct' => $isCorrect,
                'points' => $question->points,
            ];
        }

        $scorePercentage = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 100.00;
        $passed = $scorePercentage >= $quiz->passing_score;

        // Save Attempt
        $attempt = QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score' => $scorePercentage,
            'answers' => $submittedAnswers,
            'passed' => $passed,
            'created_at' => now(),
        ]);

        if ($passed) {
            // Mark the corresponding lesson as completed
            LessonCompletion::firstOrCreate([
                'user_id' => $user->id,
                'lesson_id' => $quiz->lesson_id,
            ], [
                'completed_at' => now(),
            ]);

            // Try to generate certificate if course is fully done
            CertificateGenerator::checkAndGenerate($course, $user);
        }

        return redirect()->route('quizzes.result', [$course->id, $quiz->id, $attempt->id]);
    }

    /**
     * Show attempt result.
     */
    public function result(Course $course, Quiz $quiz, QuizAttempt $attempt)
    {
        $user = Auth::user();

        if ($attempt->user_id !== $user->id || $quiz->id !== $attempt->quiz_id) {
            abort(403);
        }

        $quiz->load('questions');
        
        // Let's re-calculate details for display
        $submittedAnswers = $attempt->answers;
        $details = [];
        
        foreach ($quiz->questions as $question) {
            $correct = $question->correct_answers;
            $submitted = $submittedAnswers[$question->id] ?? null;
            $isCorrect = false;

            if ($question->type === 'single_choice' || $question->type === 'true_false') {
                if ($submitted !== null && count($correct) > 0) {
                    $isCorrect = (strtolower(trim($submitted)) === strtolower(trim($correct[0])));
                }
            } elseif ($question->type === 'multiple_choice') {
                $submittedArray = is_array($submitted) ? $submitted : [$submitted];
                $cArray = array_map('strtolower', array_map('trim', $correct));
                $sArray = array_map('strtolower', array_map('trim', array_filter($submittedArray)));
                sort($cArray);
                sort($sArray);
                $isCorrect = ($cArray === $sArray);
            } elseif ($question->type === 'short_answer') {
                if ($submitted !== null) {
                    $subText = strtolower(trim($submitted));
                    foreach ($correct as $correctVal) {
                        if ($subText === strtolower(trim($correctVal))) {
                            $isCorrect = true;
                            break;
                        }
                    }
                }
            }

            $details[] = [
                'question' => $question,
                'submitted' => $submitted,
                'is_correct' => $isCorrect,
            ];
        }

        $courseProgress = CourseProgressHelper::getCourseProgress($course, $user);
        $hasCertificate = $course->certificates()->where('user_id', $user->id)->exists();

        return view('quizzes.result', compact('course', 'quiz', 'attempt', 'details', 'courseProgress', 'hasCertificate'));
    }
}
