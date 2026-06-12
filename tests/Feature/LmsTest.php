<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\LessonCompletion;
use App\Services\CourseProgressHelper;
use App\Services\CertificateGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_progress_calculation_and_weighted_grading()
    {
        // 1. Setup entities
        $student = User::create([
            'name' => 'Alex Student',
            'email' => 'student@example.com',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        $instructor = User::create([
            'name' => 'Sarah Instructor',
            'email' => 'instructor@example.com',
            'password' => bcrypt('password'),
            'role' => 'instructor',
        ]);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Test Course',
            'slug' => 'test-course',
            'description' => 'A test course',
            'price' => 0.00,
            'is_published' => true,
        ]);

        $module = Module::create([
            'course_id' => $course->id,
            'title' => 'Module 1',
            'order' => 0,
        ]);

        $lessonText = Lesson::create([
            'module_id' => $module->id,
            'title' => 'Text Lesson',
            'type' => 'text',
            'duration_minutes' => 10,
            'order' => 0,
        ]);

        $lessonQuiz1 = Lesson::create([
            'module_id' => $module->id,
            'title' => 'Quiz 1 Lesson',
            'type' => 'quiz',
            'duration_minutes' => 15,
            'order' => 1,
        ]);

        $quiz1 = Quiz::create([
            'lesson_id' => $lessonQuiz1->id,
            'title' => 'Quiz 1',
            'passing_score' => 70,
            'weight' => 1, // weight 1
        ]);

        $lessonQuiz2 = Lesson::create([
            'module_id' => $module->id,
            'title' => 'Quiz 2 Lesson',
            'type' => 'quiz',
            'duration_minutes' => 20,
            'order' => 2,
        ]);

        $quiz2 = Quiz::create([
            'lesson_id' => $lessonQuiz2->id,
            'title' => 'Quiz 2',
            'passing_score' => 80,
            'weight' => 3, // weight 3
        ]);

        // 2. Initial state asserts
        $progress = CourseProgressHelper::getCourseProgress($course, $student);
        $this->assertEquals(0, $progress['percentage']);
        $this->assertEquals(3, $progress['total']);
        $this->assertEquals(0, $progress['completed']);

        $gradeInfo = CourseProgressHelper::getCourseGrade($course, $student);
        $this->assertEquals(0.0, $gradeInfo['score']);
        $this->assertEquals(4, $gradeInfo['total_weight']); // 1 + 3

        // 3. Student completes text lesson
        LessonCompletion::create([
            'user_id' => $student->id,
            'lesson_id' => $lessonText->id,
            'completed_at' => now(),
        ]);

        $progress = CourseProgressHelper::getCourseProgress($course, $student);
        $this->assertEquals(33, $progress['percentage']); // 1/3
        $this->assertEquals(1, $progress['completed']);

        // 4. Student attempts Quiz 1 (scores 80%, passes)
        $attempt1 = QuizAttempt::create([
            'user_id' => $student->id,
            'quiz_id' => $quiz1->id,
            'score' => 80.00,
            'answers' => [],
            'passed' => true,
            'created_at' => now(),
        ]);

        // Mark corresponding lesson as completed since they passed
        LessonCompletion::create([
            'user_id' => $student->id,
            'lesson_id' => $lessonQuiz1->id,
            'completed_at' => now(),
        ]);

        $progress = CourseProgressHelper::getCourseProgress($course, $student);
        $this->assertEquals(67, $progress['percentage']); // 2/3
        $this->assertEquals(2, $progress['completed']);

        // Check weighted grade at this point:
        // Quiz 1 score = 80 (weight 1)
        // Quiz 2 score = 0 (weight 3)
        // Expected grade = (80*1 + 0*3) / 4 = 20.0
        $gradeInfo = CourseProgressHelper::getCourseGrade($course, $student);
        $this->assertEquals(20.0, $gradeInfo['score']);

        // 5. Student attempts Quiz 2 (scores 90%, passes)
        $attempt2 = QuizAttempt::create([
            'user_id' => $student->id,
            'quiz_id' => $quiz2->id,
            'score' => 90.00,
            'answers' => [],
            'passed' => true,
            'created_at' => now(),
        ]);

        LessonCompletion::create([
            'user_id' => $student->id,
            'lesson_id' => $lessonQuiz2->id,
            'completed_at' => now(),
        ]);

        // Progress should now be 100%
        $progress = CourseProgressHelper::getCourseProgress($course, $student);
        $this->assertEquals(100, $progress['percentage']);

        // Check weighted grade now:
        // Quiz 1 score = 80 (weight 1)
        // Quiz 2 score = 90 (weight 3)
        // Expected grade = (80*1 + 90*3) / 4 = (80 + 270) / 4 = 350 / 4 = 87.5
        $gradeInfo = CourseProgressHelper::getCourseGrade($course, $student);
        $this->assertEquals(87.5, $gradeInfo['score']);

        // 6. Test certificate generation eligibility
        $cert = CertificateGenerator::checkAndGenerate($course, $student);
        $this->assertNotNull($cert);
        $this->assertDatabaseHas('certificates', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'verification_code' => $cert->verification_code,
        ]);
    }
}
