<?php

namespace App\Http\Controllers;

use App\Models\Payout;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $tab = $request->query('tab', 'analytics');

        // Platform metrics (always loaded for layout/sidebar or simple panels)
        $totalStudents = User::where('role', 'student')->count();
        $totalInstructors = User::where('role', 'instructor')->count();
        $totalModerators = User::where('role', 'moderator')->count();
        $totalCourses = Course::count();

        // Load tab-specific data
        $data = [
            'totalStudents' => $totalStudents,
            'totalInstructors' => $totalInstructors,
            'totalModerators' => $totalModerators,
            'totalCourses' => $totalCourses,
            'tab' => $tab,
        ];

        switch ($tab) {
            case 'students':
                $data['students'] = User::where('role', 'student')
                    ->withCount(['certificates', 'quizAttempts', 'completions'])
                    ->get();
                break;

            case 'instructors':
                $data['instructors'] = User::where('role', 'instructor')
                    ->withCount('courses')
                    ->get();
                break;

            case 'moderators':
                $data['moderators'] = User::where('role', 'moderator')->get();
                break;

            case 'courses':
                $data['courses'] = Course::with('instructor')
                    ->withCount(['modules', 'lessons'])
                    ->get();
                break;

            case 'payments':
                $data['pendingPayouts'] = Payout::where('status', 'pending')
                    ->with('instructor')
                    ->orderByDesc('created_at')
                    ->get();

                $data['completedPayouts'] = Payout::where('status', '!=', 'pending')
                    ->with('instructor')
                    ->orderByDesc('created_at')
                    ->get();
                break;

            case 'reports':
                $data['avgQuizScore'] = round(\App\Models\QuizAttempt::avg('score') ?? 0, 1);
                $data['certificatesCount'] = \App\Models\Certificate::count();
                $data['totalPayoutVolume'] = Payout::where('status', 'approved')->sum('amount');
                $data['pendingPayoutVolume'] = Payout::where('status', 'pending')->sum('amount');
                $data['lessonCompletions'] = \App\Models\LessonCompletion::count();
                break;

            case 'settings':
                $settingsPath = storage_path('app/settings.json');
                $defaultSettings = [
                    'site_name' => 'Aether LMS',
                    'site_tagline' => 'Premium Interactive Video Learning Platform',
                    'contact_email' => 'support@aetherlms.test',
                    'commission_rate' => '15.00',
                    'enable_registration' => 'true',
                    'maintenance_mode' => 'false',
                ];

                if (file_exists($settingsPath)) {
                    $settings = json_decode(file_get_contents($settingsPath), true);
                    $data['settings'] = array_merge($defaultSettings, $settings);
                } else {
                    file_put_contents($settingsPath, json_encode($defaultSettings, JSON_PRETTY_PRINT));
                    $data['settings'] = $defaultSettings;
                }
                break;

            case 'analytics':
            default:
                // Analytics details
                $data['avgQuizScore'] = round(\App\Models\QuizAttempt::avg('score') ?? 0, 1);
                $data['certificatesCount'] = \App\Models\Certificate::count();
                $data['totalPayoutVolume'] = Payout::where('status', 'approved')->sum('amount');
                $data['lessonCompletions'] = \App\Models\LessonCompletion::count();
                break;
        }

        return view('admin.dashboard', $data);
    }

    public function updateUserRole(Request $request, User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'role' => 'required|string|in:student,instructor,moderator,admin',
        ]);

        // Prevent admin from demoting themselves
        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->with('error', 'You cannot demote yourself from the Administrator role.');
        }

        $user->update(['role' => $request->role]);

        return back()->with('success', "Role for user '{$user->name}' updated to '{$request->role}' successfully.");
    }

    public function updateSettings(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_tagline' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'commission_rate' => 'required|numeric|between:0,100',
            'enable_registration' => 'required|string|in:true,false',
            'maintenance_mode' => 'required|string|in:true,false',
        ]);

        $settings = [
            'site_name' => $request->site_name,
            'site_tagline' => $request->site_tagline,
            'contact_email' => $request->contact_email,
            'commission_rate' => number_format((float)$request->commission_rate, 2, '.', ''),
            'enable_registration' => $request->enable_registration,
            'maintenance_mode' => $request->maintenance_mode,
        ];

        file_put_contents(storage_path('app/settings.json'), json_encode($settings, JSON_PRETTY_PRINT));

        return back()->with('success', 'Site settings updated successfully.');
    }

    public function deleteCourse(Course $course)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $courseName = $course->title;
        $course->delete();

        return back()->with('success', "Course '{$courseName}' has been deleted successfully.");
    }

    public function deleteUser(User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        // Prevent self deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own Administrator account.');
        }

        $userName = $user->name;
        $user->delete();

        return back()->with('success', "User account for '{$userName}' has been deleted successfully.");
    }

    public function approvePayout(Payout $payout)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($payout->status !== 'pending') {
            return back()->with('error', 'This payout has already been processed.');
        }

        $payout->update([
            'status' => 'approved',
            'tx_id' => 'TXN-' . strtoupper(Str::random(12)),
        ]);

        return back()->with('success', 'Payout request approved and processed.');
    }

    public function rejectPayout(Payout $payout)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($payout->status !== 'pending') {
            return back()->with('error', 'This payout has already been processed.');
        }

        // Refund the instructor
        $instructor = $payout->instructor;
        $instructor->payout_balance += $payout->amount;
        $instructor->save();

        $payout->update([
            'status' => 'rejected',
        ]);

        return back()->with('success', 'Payout request rejected. Funds returned to instructor balance.');
    }
}
