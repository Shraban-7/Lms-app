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

    public function test_admin_dashboard_and_controls()
    {
        // Backup settings.json if it exists
        $settingsPath = storage_path('app/settings.json');
        $originalSettings = null;
        if (file_exists($settingsPath)) {
            $originalSettings = file_get_contents($settingsPath);
        }

        try {
            // 1. Setup entities
            $admin = User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);

            $student = User::create([
                'name' => 'Student User',
                'email' => 'student@example.com',
                'password' => bcrypt('password'),
                'role' => 'student',
            ]);

            $instructor = User::create([
                'name' => 'Instructor User',
                'email' => 'instructor@example.com',
                'password' => bcrypt('password'),
                'role' => 'instructor',
                'payout_balance' => 200.00,
            ]);

            $course = Course::create([
                'instructor_id' => $instructor->id,
                'title' => 'Test Admin Course',
                'slug' => 'test-admin-course',
                'description' => 'Course description',
                'price' => 49.99,
                'is_published' => true,
            ]);

            $payout = \App\Models\Payout::create([
                'instructor_id' => $instructor->id,
                'amount' => 150.00,
                'status' => 'pending',
                'method' => 'paypal',
            ]);

            // --- TEST UNAUTHORIZED USER (Student) ---
            $this->actingAs($student);

            // Dashboard access blocks
            $response = $this->get('/admin/dashboard');
            $response->assertStatus(403);

            // Settings update blocks
            $response = $this->post('/admin/settings', [
                'site_name' => 'Hacked Name',
                'site_tagline' => 'Hacked Tagline',
                'contact_email' => 'hacker@example.com',
                'commission_rate' => '50.00',
                'enable_registration' => 'false',
                'maintenance_mode' => 'true',
            ]);
            $response->assertStatus(403);

            // Role update blocks
            $response = $this->post("/admin/users/{$student->id}/update-role", [
                'role' => 'admin',
            ]);
            $response->assertStatus(403);

            // Course deletion blocks
            $response = $this->delete("/admin/courses/{$course->id}");
            $response->assertStatus(403);
            $this->assertDatabaseHas('courses', ['id' => $course->id]);

            // User deletion blocks
            $response = $this->delete("/admin/users/{$instructor->id}");
            $response->assertStatus(403);
            $this->assertDatabaseHas('users', ['id' => $instructor->id]);

            // Payout approve blocks
            $response = $this->post("/admin/payouts/{$payout->id}/approve");
            $response->assertStatus(403);
            $this->assertEquals('pending', $payout->fresh()->status);

            // Payout reject blocks
            $response = $this->post("/admin/payouts/{$payout->id}/reject");
            $response->assertStatus(403);
            $this->assertEquals('pending', $payout->fresh()->status);


            // --- TEST AUTHORIZED USER (Admin) ---
            $this->actingAs($admin);

            // Dashboard analytics (default) tab
            $response = $this->get('/admin/dashboard');
            $response->assertStatus(200);
            $response->assertViewHas('tab', 'analytics');

            // Dashboard tab=students
            $response = $this->get('/admin/dashboard?tab=students');
            $response->assertStatus(200);
            $response->assertViewHas('tab', 'students');
            $response->assertViewHas('students');

            // Dashboard tab=instructors
            $response = $this->get('/admin/dashboard?tab=instructors');
            $response->assertStatus(200);
            $response->assertViewHas('tab', 'instructors');
            $response->assertViewHas('instructors');

            // Dashboard tab=moderators
            $response = $this->get('/admin/dashboard?tab=moderators');
            $response->assertStatus(200);
            $response->assertViewHas('tab', 'moderators');
            $response->assertViewHas('moderators');

            // Dashboard tab=courses
            $response = $this->get('/admin/dashboard?tab=courses');
            $response->assertStatus(200);
            $response->assertViewHas('tab', 'courses');
            $response->assertViewHas('courses');

            // Dashboard tab=payments
            $response = $this->get('/admin/dashboard?tab=payments');
            $response->assertStatus(200);
            $response->assertViewHas('tab', 'payments');
            $response->assertViewHas('pendingPayouts');

            // Dashboard tab=reports
            $response = $this->get('/admin/dashboard?tab=reports');
            $response->assertStatus(200);
            $response->assertViewHas('tab', 'reports');

            // Dashboard tab=settings
            $response = $this->get('/admin/dashboard?tab=settings');
            $response->assertStatus(200);
            $response->assertViewHas('tab', 'settings');
            $response->assertViewHas('settings');


            // 2. Settings update
            $response = $this->post('/admin/settings', [
                'site_name' => 'New Site Name',
                'site_tagline' => 'New Site Tagline',
                'contact_email' => 'new-admin@example.com',
                'commission_rate' => '12.50',
                'enable_registration' => 'true',
                'maintenance_mode' => 'false',
            ]);
            $response->assertRedirect();
            
            // Verify file written correctly
            $this->assertTrue(file_exists($settingsPath));
            $savedSettings = json_decode(file_get_contents($settingsPath), true);
            $this->assertEquals('New Site Name', $savedSettings['site_name']);
            $this->assertEquals('New Site Tagline', $savedSettings['site_tagline']);
            $this->assertEquals('new-admin@example.com', $savedSettings['contact_email']);
            $this->assertEquals('12.50', $savedSettings['commission_rate']);
            $this->assertEquals('true', $savedSettings['enable_registration']);
            $this->assertEquals('false', $savedSettings['maintenance_mode']);


            // 3. User role update (promote student to moderator)
            $response = $this->post("/admin/users/{$student->id}/update-role", [
                'role' => 'moderator',
            ]);
            $response->assertRedirect();
            $this->assertEquals('moderator', $student->fresh()->role);

            // User role update (demote student back)
            $response = $this->post("/admin/users/{$student->id}/update-role", [
                'role' => 'student',
            ]);
            $response->assertRedirect();
            $this->assertEquals('student', $student->fresh()->role);

            // Prevent self-demotion
            $response = $this->post("/admin/users/{$admin->id}/update-role", [
                'role' => 'student',
            ]);
            $response->assertRedirect();
            $this->assertEquals('admin', $admin->fresh()->role);
            $response->assertSessionHas('error');


            // 4. Payout approvals and rejections
            // Approve payout
            $response = $this->post("/admin/payouts/{$payout->id}/approve");
            $response->assertRedirect();
            $this->assertEquals('approved', $payout->fresh()->status);
            $this->assertNotNull($payout->fresh()->tx_id);

            // Create another payout for testing reject
            $payout2 = \App\Models\Payout::create([
                'instructor_id' => $instructor->id,
                'amount' => 50.00,
                'status' => 'pending',
                'method' => 'paypal',
            ]);

            // Reject payout
            $response = $this->post("/admin/payouts/{$payout2->id}/reject");
            $response->assertRedirect();
            $this->assertEquals('rejected', $payout2->fresh()->status);
            // Instructor payout_balance was 200.00, refunded 50.00 -> 250.00
            $this->assertEquals(250.00, (float)$instructor->fresh()->payout_balance);


            // 5. Deleting Course
            $response = $this->delete("/admin/courses/{$course->id}");
            $response->assertRedirect();
            $this->assertDatabaseMissing('courses', ['id' => $course->id]);


            // 6. Deleting User
            $response = $this->delete("/admin/users/{$student->id}");
            $response->assertRedirect();
            $this->assertDatabaseMissing('users', ['id' => $student->id]);

            // Prevent self deletion
            $response = $this->delete("/admin/users/{$admin->id}");
            $response->assertRedirect();
            $this->assertDatabaseHas('users', ['id' => $admin->id]);
            $response->assertSessionHas('error');

        } finally {
            // Restore original settings.json
            if ($originalSettings !== null) {
                file_put_contents($settingsPath, $originalSettings);
            } else if (file_exists($settingsPath)) {
                unlink($settingsPath);
            }
        }
    }
}

