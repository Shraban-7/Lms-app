@extends('layouts.app')

@section('title', 'Admin Platform Dashboard')

@section('content')
<header class="mb-12">
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-danger/15 text-danger mb-2">Platform Operations</span>
    <h1 class="text-3xl font-extrabold text-text-primary">Platform Administration</h1>
    <p class="text-text-secondary mt-1">Manage instructor payout requests, review metrics, and audit system activity.</p>
</header>

<!-- Platform Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-1 transition-all duration-300 flex flex-col">
        <span class="text-xs text-text-secondary uppercase font-bold tracking-wider mb-1">Total Students</span>
        <h3 class="text-3xl font-extrabold text-primary">{{ $totalStudents }}</h3>
    </div>

    <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-1 transition-all duration-300 flex flex-col">
        <span class="text-xs text-text-secondary uppercase font-bold tracking-wider mb-1">Total Instructors</span>
        <h3 class="text-3xl font-extrabold text-accent">{{ $totalInstructors }}</h3>
    </div>

    <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-1 transition-all duration-300 flex flex-col">
        <span class="text-xs text-text-secondary uppercase font-bold tracking-wider mb-1">Active Courses</span>
        <h3 class="text-3xl font-extrabold text-text-primary">{{ $totalCourses }}</h3>
    </div>
</div>

<!-- Pending Payout Requests -->
<section class="mb-16">
    <h2 class="text-2xl font-extrabold text-text-primary border-b border-gray-200 dark:border-border pb-3 mb-6">Pending Payout Requests</h2>
    
    <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card overflow-hidden">
        @if($pendingPayouts->isEmpty())
            <div class="text-center py-8 text-text-secondary">
                <p>No pending payout requests at this time.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b-2 border-gray-200 dark:border-border text-text-secondary font-bold text-xs uppercase tracking-wide">
                            <th class="py-3.5 px-3">Instructor</th>
                            <th class="py-3.5 px-3">Requested Amount</th>
                            <th class="py-3.5 px-3">Method</th>
                            <th class="py-3.5 px-3">Date Requested</th>
                            <th class="py-3.5 px-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingPayouts as $payout)
                            <tr class="border-b border-gray-200 dark:border-border hover:bg-gray-50/50 dark:hover:bg-surface/30 transition-colors">
                                <td class="py-4 px-3 align-middle text-text-primary">
                                    <strong class="font-bold text-text-primary">{{ $payout->instructor->name }}</strong><br>
                                    <span class="text-xs text-text-secondary">{{ $payout->instructor->email }}</span>
                                </td>
                                <td class="py-4 px-3 align-middle text-text-primary font-bold">${{ number_format($payout->amount, 2) }}</td>
                                <td class="py-4 px-3 align-middle text-text-primary uppercase text-xs font-semibold">{{ str_replace('_', ' ', $payout->method) }}</td>
                                <td class="py-4 px-3 align-middle text-text-primary text-xs">{{ $payout->created_at->format('M d, Y H:i') }}</td>
                                <td class="py-4 px-3 align-middle text-right">
                                    <div class="inline-flex items-center gap-2.5 justify-end w-full">
                                        <form action="{{ route('admin.payouts.approve', $payout->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 bg-success text-white text-xs font-semibold rounded hover:bg-success-hover hover:-translate-y-0.5 transition-all duration-300 cursor-pointer">Approve</button>
                                        </form>
                                        <form action="{{ route('admin.payouts.reject', $payout->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 bg-surface-solid border border-danger/30 text-danger text-xs font-semibold rounded hover:bg-danger/10 hover:-translate-y-0.5 transition-all duration-300 cursor-pointer">Reject</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</section>

<!-- Completed Payout Ledger -->
<section class="mb-16">
    <h2 class="text-2xl font-extrabold text-text-primary border-b border-gray-200 dark:border-border pb-3 mb-6">Completed Payout Log</h2>
    
    <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card overflow-hidden">
        @if($completedPayouts->isEmpty())
            <div class="text-center py-8 text-text-secondary">
                <p>No completed payouts found in the platform ledger.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b-2 border-gray-200 dark:border-border text-text-secondary font-bold text-xs uppercase tracking-wide">
                            <th class="py-3.5 px-3">Instructor</th>
                            <th class="py-3.5 px-3">Amount</th>
                            <th class="py-3.5 px-3">Method</th>
                            <th class="py-3.5 px-3">Status</th>
                            <th class="py-3.5 px-3">Processed Date</th>
                            <th class="py-3.5 px-3">Transaction ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($completedPayouts as $payout)
                            <tr class="border-b border-gray-200 dark:border-border hover:bg-gray-50/50 dark:hover:bg-surface/30 transition-colors">
                                <td class="py-4 px-3 align-middle text-text-primary"><strong class="font-bold text-text-primary">{{ $payout->instructor->name }}</strong></td>
                                <td class="py-4 px-3 align-middle text-text-primary font-bold">${{ number_format($payout->amount, 2) }}</td>
                                <td class="py-4 px-3 align-middle text-text-primary uppercase text-xs font-semibold">{{ str_replace('_', ' ', $payout->method) }}</td>
                                <td class="py-4 px-3 align-middle text-text-primary text-xs">
                                    @if($payout->status === 'approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-success/15 text-success">Approved</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-danger/15 text-danger">Rejected</span>
                                    @endif
                                </td>
                                <td class="py-4 px-3 align-middle text-text-primary text-xs">{{ $payout->updated_at->format('M d, Y H:i') }}</td>
                                <td class="py-4 px-3 align-middle font-mono text-xs text-text-secondary">
                                    {{ $payout->tx_id ?: 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</section>
@endsection
