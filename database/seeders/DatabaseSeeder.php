<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\ForumTopic;
use App\Models\ForumReply;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Core Users
        $student = User::create([
            'name' => 'Alex Student',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $instructor = User::create([
            'name' => 'Sarah Instructor',
            'email' => 'instructor@example.com',
            'password' => Hash::make('password'),
            'role' => 'instructor',
            'payout_balance' => 320.50,
        ]);

        $admin = User::create([
            'name' => 'Chief Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 2. Create Web Design Course
        $designCourse = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Creative Web Design Masterclass',
            'slug' => 'creative-web-design-masterclass',
            'description' => 'Master layout spacing, modern typography, HSL colors, responsive design systems, custom video players, and high-performance CSS animations. This is a practical, project-oriented course.',
            'thumbnail' => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?q=80&w=600',
            'price' => 49.00,
            'is_published' => true,
        ]);

        // Modules
        $module1 = Module::create([
            'course_id' => $designCourse->id,
            'title' => 'Fundamentals of CSS Layouts',
            'description' => 'Dive deep into CSS Flexbox, Grid, container queries, and layout structures.',
            'order' => 0,
        ]);

        // Lessons
        $lesson1 = Lesson::create([
            'module_id' => $module1->id,
            'title' => 'Introduction to CSS Grid & Flexbox',
            'content_text' => 'CSS Grid and Flexbox are the twin pillars of modern layout architecture. In this lesson, we cover the main alignment principles, grid areas, auto-fit/auto-fill columns, and media-less responsive patterns.',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
            'type' => 'video',
            'duration_minutes' => 12,
            'order' => 0,
        ]);

        $lesson2 = Lesson::create([
            'module_id' => $module1->id,
            'title' => 'Designing with CSS Variables (Custom Properties)',
            'content_text' => "CSS Custom Properties enable dynamic runtime alterations to page branding, themes, and values. Declare variables on the :root element:\n\n:root {\n  --primary-color: #6366F1;\n}\n\nTo consume, use the var() function:\n\nbody {\n  background-color: var(--primary-color);\n}",
            'video_url' => null,
            'type' => 'text',
            'duration_minutes' => 8,
            'order' => 1,
        ]);

        $lesson3 = Lesson::create([
            'module_id' => $module1->id,
            'title' => 'CSS Layout Graded Quiz',
            'content_text' => null,
            'video_url' => null,
            'type' => 'quiz',
            'duration_minutes' => 15,
            'order' => 2,
        ]);

        // Quiz details
        $quiz1 = Quiz::create([
            'lesson_id' => $lesson3->id,
            'title' => 'CSS Layout Assessment',
            'passing_score' => 70,
            'weight' => 1,
        ]);

        // Questions
        Question::create([
            'quiz_id' => $quiz1->id,
            'question_text' => 'Which CSS property establishes a grid container context on an element?',
            'type' => 'single_choice',
            'points' => 1,
            'options' => ['display: grid', 'display: flex', 'grid-template: columns', 'float: left'],
            'correct_answers' => ['display: grid'],
        ]);

        Question::create([
            'quiz_id' => $quiz1->id,
            'question_text' => 'Which of the following are valid flexbox properties? (Select all that apply)',
            'type' => 'multiple_choice',
            'points' => 2,
            'options' => ['justify-content', 'align-items', 'grid-area', 'flex-wrap'],
            'correct_answers' => ['justify-content', 'align-items', 'flex-wrap'],
        ]);

        Question::create([
            'quiz_id' => $quiz1->id,
            'question_text' => 'CSS Grid layout is primarily one-dimensional while Flexbox is two-dimensional.',
            'type' => 'true_false',
            'points' => 1,
            'options' => null,
            'correct_answers' => ['false'],
        ]);

        Question::create([
            'quiz_id' => $quiz1->id,
            'question_text' => 'What CSS property controls the spacing between grid tracks directly (e.g. gap)?',
            'type' => 'short_answer',
            'points' => 2,
            'options' => null,
            'correct_answers' => ['gap'],
        ]);


        // 3. Create PHP/Laravel Course
        $phpCourse = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Advanced PHP & Laravel Development',
            'slug' => 'advanced-php-laravel-development',
            'description' => 'Unlocking advanced Eloquent ORM performance, complex query relationships, service provider architectures, testing workflows, and custom backend API operations.',
            'thumbnail' => 'https://images.unsplash.com/photo-1599507593499-a3f7f7d9a224?q=80&w=600',
            'price' => 0.00,
            'is_published' => true,
        ]);

        $module2 = Module::create([
            'course_id' => $phpCourse->id,
            'title' => 'Eloquent ORM Deep Dive',
            'description' => 'Optimize queries, avoid N+1 issues, build custom relationship types.',
            'order' => 0,
        ]);

        $lesson4 = Lesson::create([
            'module_id' => $module2->id,
            'title' => 'Advanced Query Scopes & Relationships',
            'content_text' => 'Learn how to construct local query scopes to encapsulate query logic and eager load relational datasets using the with() helper to prevent database thrashing.',
            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4',
            'type' => 'video',
            'duration_minutes' => 20,
            'order' => 0,
        ]);

        $lesson5 = Lesson::create([
            'module_id' => $module2->id,
            'title' => 'Eloquent Assessment',
            'content_text' => null,
            'video_url' => null,
            'type' => 'quiz',
            'duration_minutes' => 10,
            'order' => 1,
        ]);

        $quiz2 = Quiz::create([
            'lesson_id' => $lesson5->id,
            'title' => 'Eloquent ORM Quiz',
            'passing_score' => 80,
            'weight' => 2,
        ]);

        Question::create([
            'quiz_id' => $quiz2->id,
            'question_text' => 'What Artisan command is used to execute database seeders in Laravel?',
            'type' => 'single_choice',
            'points' => 1,
            'options' => ['db:seed', 'migrate:fresh', 'make:seeder', 'db:status'],
            'correct_answers' => ['db:seed'],
        ]);

        Question::create([
            'quiz_id' => $quiz2->id,
            'question_text' => 'Laravel Eloquent ORM implements the Data Mapper architectural design pattern.',
            'type' => 'true_false',
            'points' => 1,
            'options' => null,
            'correct_answers' => ['false'], // Active Record
        ]);

        // 4. Seed Forum Topics
        $topic1 = ForumTopic::create([
            'course_id' => $designCourse->id,
            'user_id' => $student->id,
            'title' => 'Flexbox alignment is off on Safari mobile',
            'content' => "I am testing my layout using flexbox row container. Everything looks perfect on Chrome and Firefox, but on Safari mobile, the items collapse and overflow the screen. Any ideas? Here is my css:\n\n.flex-container {\n  display: flex;\n  justify-content: space-around;\n}",
        ]);

        ForumReply::create([
            'forum_topic_id' => $topic1->id,
            'user_id' => $instructor->id,
            'parent_id' => null,
            'content' => "This is a common issue with older Safari engines not scaling correctly without explicit width. Try adding flex-shrink: 0; to your child elements, or check if the parent container has a max-width configured.",
        ]);
    }
}
