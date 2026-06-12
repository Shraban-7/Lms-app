<?php

namespace App\Http\Controllers;

use App\Models\Payout;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Platform metrics
        $totalStudents = User::where('role', 'student')->count();
        $totalInstructors = User::where('role', 'instructor')->count();
        $totalCourses = Course::count();
        
        $pendingPayouts = Payout::where('status', 'pending')
            ->with('instructor')
            ->orderByDesc('created_at')
            ->get();

        $completedPayouts = Payout::where('status', '!=', 'pending')
            ->with('instructor')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents', 
            'totalInstructors', 
            'totalCourses', 
            'pendingPayouts', 
            'completedPayouts'
        ));
    }

    public function approvePayout(Payout $payout)
    {
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
