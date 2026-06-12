@extends('layouts.app')

@section('title', 'Earnings & Payout Ledger - Instructor')

@section('content')
<header class="mb-12">
    <h1 class="text-3xl font-extrabold text-text-primary mb-2">Earnings & Payout Ledger</h1>
    <p class="text-text-secondary text-sm md:text-base">Request payouts, view earnings summary, and track transactions history.</p>
</header>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-1 transition-all duration-300 flex flex-col">
        <span class="text-xs text-text-secondary uppercase font-bold tracking-wider mb-1">Current Balance</span>
        <h3 class="text-3xl font-extrabold text-primary">${{ number_format($user->payout_balance, 2) }}</h3>
        <p class="text-xs text-text-muted mt-2">Available for immediate payout request.</p>
    </div>

    <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-1 transition-all duration-300 flex flex-col">
        <span class="text-xs text-text-secondary uppercase font-bold tracking-wider mb-1">Approved Payouts</span>
        <h3 class="text-3xl font-extrabold text-success">${{ number_format($totalApproved, 2) }}</h3>
        <p class="text-xs text-text-muted mt-2">Successfully transferred to your accounts.</p>
    </div>

    <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-1 transition-all duration-300 flex flex-col">
        <span class="text-xs text-text-secondary uppercase font-bold tracking-wider mb-1">Lifetime Earnings</span>
        <h3 class="text-3xl font-extrabold text-text-primary">${{ number_format($lifetimeEarnings, 2) }}</h3>
        <p class="text-xs text-text-muted mt-2">Gross revenue earned on the platform.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-8">
    
    <!-- Payout Transaction History Ledger -->
    <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card overflow-hidden">
        <h2 class="text-xl font-extrabold text-text-primary border-b border-gray-200 dark:border-border pb-3 mb-6">Payout History</h2>

        @if($payouts->isEmpty())
            <div class="text-center py-8 text-text-secondary text-sm">
                <p>No payout requests found.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b-2 border-gray-200 dark:border-border text-text-secondary font-bold text-xs uppercase tracking-wide">
                            <th class="py-3.5 px-3">Date Requested</th>
                            <th class="py-3.5 px-3">Amount</th>
                            <th class="py-3.5 px-3">Method</th>
                            <th class="py-3.5 px-3">Status</th>
                            <th class="py-3.5 px-3">Transaction ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payouts as $payout)
                            <tr class="border-b border-gray-200 dark:border-border hover:bg-gray-50/50 dark:hover:bg-surface/30 transition-colors">
                                <td class="py-4 px-3 align-middle text-text-primary text-xs">{{ $payout->created_at->format('M d, Y H:i') }}</td>
                                <td class="py-4 px-3 align-middle text-text-primary font-bold">${{ number_format($payout->amount, 2) }}</td>
                                <td class="py-4 px-3 align-middle text-text-primary uppercase text-xs font-semibold">{{ str_replace('_', ' ', $payout->method) }}</td>
                                <td class="py-4 px-3 align-middle text-text-primary">
                                    @if($payout->status === 'approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-success/15 text-success">{{ $payout->status }}</span>
                                    @elseif($payout->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-warning/15 text-warning">{{ $payout->status }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-danger/15 text-danger">{{ $payout->status }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-3 align-middle font-mono text-xs text-text-secondary">
                                    {{ $payout->tx_id ?: 'Processing...' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Request Payout Card Form -->
    <div>
        <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-0.5 transition-all duration-300">
            <h3 class="text-lg font-extrabold text-text-primary mb-4">Request a Payout</h3>
            
            <form action="{{ route('instructor.request-payout') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block font-semibold mb-1.5 text-xs uppercase tracking-wide text-text-secondary" for="amount">Payout Amount (USD)</label>
                    <input class="w-full py-2.5 px-3.5 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" type="number" step="0.01" name="amount" id="amount" value="{{ old('amount', $user->payout_balance) }}" min="10" required>
                    @error('amount')
                        <span class="text-danger text-xs mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block font-semibold mb-1.5 text-xs uppercase tracking-wide text-text-secondary" for="method">Transfer Method</label>
                    <select class="w-full py-2.5 px-3.5 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)] cursor-pointer" name="method" id="method" required>
                        <option value="bank_transfer">Direct Bank Transfer</option>
                        <option value="paypal">PayPal Account</option>
                    </select>
                    @error('method')
                        <span class="text-danger text-xs mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="w-full mt-4 inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-primary text-white rounded-lg font-semibold cursor-pointer shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:bg-primary-hover hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-none transition-all duration-300" {{ $user->payout_balance < 10 ? 'disabled' : '' }}>
                    Request Transfer
                </button>

                @if($user->payout_balance < 10)
                    <p class="text-xs text-text-muted text-center mt-3">Minimum payout request is $10.00</p>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
