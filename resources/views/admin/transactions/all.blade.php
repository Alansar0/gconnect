<x-layouts.admin>
    <div class="p-6 min-h-screen bg-[#0B1620] text-white space-y-10">
         <div class=" w-full flex justify-start mt-6 mb-4">
            <a href="{{ url()->previous() }}" class="text-[#58a6ff] hover:underline flex items-center">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a>
        </div>
        {{-- COMPLETED / FAILED TRANSACTIONS --}}
        <div class="bg-[#101E2B] rounded-2xl mt-15 shadow-[0_0_20px_rgba(0,255,209,0.1)] p-6 border border-[#00FFD1]/20">
            <h2 class="text-lg font-semibold mb-4 text-[#00FFD1]">Registered Users</h2>

            <div class="flex mb-4">
                <input type="text" placeholder="Search by Service or Product"
                    class="bg-[#0B1620] border border-[#00FFD1]/30 rounded-l-lg px-4 py-2 w-full text-gray-200 focus:outline-none focus:ring-1 focus:ring-[#00FFD1]">
                <button
                    class="bg-[#00FFD1] text-[#101E2B] px-5 py-2 rounded-r-lg hover:opacity-80 transition shadow-[0_0_10px_#00FFD1]">
                    Search
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left rounded-xl overflow-hidden border border-[#00FFD1]/10">
                    <thead class="bg-[#142434] text-[#00FFD1] uppercase font-semibold">
                        <tr>
                            <th class="px-4 py-3">Action</th>
                            <th class="px-4 py-3">Transaction ID</th>
                            <th class="px-4 py-3">User Pnumber</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Description</th>
                            <th class="px-4 py-3">Amount (₦)</th>
                            <th class="px-4 py-3">Prev Balance (₦)</th>
                            <th class="px-4 py-3">New Balance (₦)</th>
                            <th class="px-4 py-3">Date</th>
                        </tr>
                    </thead>

                    <tbody>
                    @php
                        $title = match($txn->description) {
                            'voucher_purchase' => 'Voucher Purchased',
                            'manual_deduction' => 'Manually Deducted',
                            'manual_funding' => 'Manually Funded',
                            'wallet_funded' => 'Wallet Funded',
                            default => ucfirst($txn->description)
                        };
                    @endphp


                        @forelse($completed as $txn)
                            @php
                                // --- Calculate previous and new balances dynamically ---
                                // (Fallback to user's balance or default 0 if missing)
                        $userBalance = $txn->user->balance ?? 0;
                        $amount = $txn->amount ?? 0;

                        if ($txn->status === 'completed') {
                            if (in_array($txn->type, ['Bank funding', 'Manually funded'])) {
                                        $prevBalance = $userBalance - $amount;
                                        $newBalance = $userBalance;
                                    } else {
                                        $prevBalance = $userBalance + $amount;
                                        $newBalance = $userBalance;
                                    }
                                } else {
                                    $prevBalance = $userBalance;
                                    $newBalance = $userBalance;
                                }
                            @endphp

                            <tr class="border-b border-[#00FFD1]/10 hover:bg-[#182b3c] transition">
                                <!-- Action -->
                                <td class="px-4 py-3">
                                    <button
                                        class="bg-[#0B1620] text-[#00FFD1] px-4 py-1.5 rounded-lg border border-[#00FFD1]/40 shadow hover:bg-[#00FFD1] hover:text-[#0B1620] transition">
                                        Refund
                                    </button>
                                </td>
                                <!-- Transaction ID -->
                                <td class="px-4 py-3 text-gray-300 font-semibold">{{ $txn->reference }}</td>

                                <!-- User Phone -->
                                <td class="px-4 py-3 text-gray-300">{{ $txn->user->phone ?? 'N/A' }}</td>

                                <!-- Status -->
                                <td
                                    class="px-4 py-3 capitalize font-semibold
                        @if (in_array($txn->type, ['Bank funding', 'Manual funding'])) text-[#00FFD1]
                        @elseif(in_array($txn->type, ['Voucher Purchased', 'Manual deducting'])) text-red-400
                        @elseif($txn->status === 'completed') text-[#00FFD1]
                        @else text-red-400 @endif">
                                    {{ $txn->status }}
                                </td>

                                <!-- Description -->
                                {{-- <td class="px-4 py-3 text-gray-400">{{ $txn->description }}</td> --}}
                                 <td class="px-4 py-3 text-gray-400">{{ $title }}</td>
                                <!-- Amount -->
                                <td class="px-4 py-3 text-gray-200">₦{{ number_format($txn->amount, 2) }}</td>

                                <!-- Previous Balance -->
                                <td class="px-4 py-3 text-gray-400">₦{{ number_format($prevBalance, 2) }}</td>

                                <!-- New Balance -->
                                <td class="px-4 py-3 text-gray-200 font-semibold text-[#00FFD1]">
                                    ₦{{ number_format($newBalance, 2) }}
                                </td>

                                <!-- Date -->
                                <td class="px-4 py-3 text-gray-400">{{ $txn->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-6 text-gray-500">
                                    No completed or failed transactions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


        </div>
</x-layouts.admin>
