
<x-layouts.app>
    <div class="min-h-screen bg-bg1 text-t1 font-['Roboto'] p-6">

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-accent tracking-wide flex items-center gap-2">
                Transaction History
            </h1>
        </div>

        <!-- Transaction List -->
        @if($transactions->isEmpty())
            <div class="text-center py-10">
                <p class="text-t3 text-sm">No transactions yet.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($transactions as $transaction)
                    <div class="
                        bg-bg2 border border-accent rounded-2xl p-5
                        flex justify-between items-center
                        shadow-accent hover:shadow-[0_0_20px_var(--accent)]
                        transition-all duration-300
                    ">
                        <div class="flex flex-col">
                            @php
                                $title = match($transaction->description) {
                                    'voucher_purchase' => 'Voucher Purchased',
                                    'manual_deduction' => 'Manually Deducted',
                                    'manual_funding'   => 'Manually Funded',
                                    'wallet_funded'    => 'Wallet Funded',
                                    default             => ucfirst($transaction->description)
                                };
                            @endphp

                            <strong class="text-base text-t1 font-medium">
                                {{ $title }}
                            </strong>

                            <span class="text-xs text-t3">
                                {{ $transaction->created_at->format('d M, Y h:i A') }}
                            </span>
                        </div>

                        <div class="text-right">
                            <span class="text-base font-semibold {{ $transaction->type == 'credit' ? 'text-green-400' : 'text-red-400' }}">
                                ₦{{ number_format($transaction->amount, 2) }}
                            </span>
                            <small class="block text-xs mt-1 text-t3 capitalize">
                                {{ $transaction->status }}
                            </small>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>

