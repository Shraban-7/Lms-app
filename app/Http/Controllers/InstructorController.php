<?php

namespace App\Http\Controllers;

use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        if (!$user->isInstructor()) {
            abort(403);
        }

        $payouts = Payout::where('instructor_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        // Simulate some sales metrics
        // Sum of all approved payouts + current balance = Lifetime earnings
        $totalApproved = Payout::where('instructor_id', $user->id)
            ->where('status', 'approved')
            ->sum('amount');
            
        $lifetimeEarnings = $totalApproved + $user->payout_balance;

        return view('instructor.dashboard', compact('user', 'payouts', 'lifetimeEarnings', 'totalApproved'));
    }

    public function requestPayout(Request $request)
    {
        $user = Auth::user();
        if (!$user->isInstructor()) {
            abort(403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:10',
            'method' => 'required|string|in:bank_transfer,paypal',
        ]);

        $amount = (float)$request->amount;

        if ($user->payout_balance < $amount) {
            return back()->withErrors(['amount' => 'Insufficient payout balance.'])->withInput();
        }

        // Deduct and create pending payout record
        $user->payout_balance -= $amount;
        $user->save();

        Payout::create([
            'instructor_id' => $user->id,
            'amount' => $amount,
            'status' => 'pending',
            'method' => $request->method,
        ]);

        return redirect()->route('instructor.dashboard')->with('success', 'Payout request submitted successfully!');
    }
}
