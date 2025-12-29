


<x-layouts.admin>
    <div class="p-6 min-h-screen bg-bg1 dark:bg-bg1 text-t1 dark:text-t1 space-y-10">

        <div class="w-full flex justify-start mt-6 mb-4">
            <a href="{{ url()->previous() }}" class="text-accent hover:underline flex items-center">
                <i class="material-icons mr-1 text-accent">arrow_back</i> Back
            </a>
        </div>

        {{-- TRANSACTIONS TABLE --}}
        <div class="bg-bg2 dark:bg-bg2 rounded-2xl mt-6 shadow p-6 border border-accent/20">

                    <h2 class="text-lg font-semibold mb-4 text-accent">
                    Registered Users Transactions
                    </h2>

                <form method="GET" action="{{ route('T.all') }}" class="flex mb-4">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search by phone or description"
                        class="w-full px-4 py-2 rounded-l-lg
                            bg-bg2 border border-accent
                            text-t1 placeholder:text-t3
                            focus:outline-none focus:ring-2 focus:ring-accent"
                    >

                    <button
                        type="submit"
                        class="px-5 py-2 rounded-r-lg
                            bg-accent text-bg1 font-semibold
                            hover:opacity-90 transition
                            shadow-accent"
                    >
                        Search
                    </button>
                </form>

        </div>


            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left rounded-xl overflow-hidden border border-accent/10">
                    <thead class="bg-bg3 dark:bg-bg3 text-accent uppercase font-semibold">
                        <tr>
                            <th class="px-4 py-3">Action</th>
                            <th class="px-4 py-3">Transaction ID</th>
                            <th class="px-4 py-3">User Phone</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Description</th>
                            <th class="px-4 py-3">Amount (₦)</th>
                            <th class="px-4 py-3">Prev Balance (₦)</th>
                            <th class="px-4 py-3">New Balance (₦)</th>
                            <th class="px-4 py-3">Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($completed as $txn)
                            @php
                                // Map description to readable title
                                $title = match($txn->description) {
                                    'voucher_purchase' => 'Voucher Purchased',
                                    'manual_deduction' => 'Manually Deducted',
                                    'manual_funding' => 'Manually Funded',
                                    'wallet_funded' => 'Wallet Funded',
                                    default => ucfirst($txn->description)
                                };

                                // Status color
                                $statusColor = $txn->status === 'success' ? 'text-green-500' : 'text-red-500';

                                // Amount color
                                $amountColor = in_array($txn->description, ['voucher_purchase', 'manual_deduction']) 
                                    ? 'text-red-500' 
                                    : (in_array($txn->description, ['wallet_funded', 'manual_funding']) 
                                        ? 'text-green-500' 
                                        : 'text-gray-500');

                                // Previous & New balance
                                $prevBalance = $txn->prev_balance ?? 0;
                                $newBalance  = $txn->new_balance ?? 0;

                                // User phone
                                $userPhone = $txn->user->phone_number ?? 'N/A';
                            @endphp

                            <tr class="border-b border-accent/10 hover:bg-bg3 dark:hover:bg-bg3/50 transition">
                                <td class="px-4 py-3">
                                    <button
                                        class="bg-bg1 dark:bg-bg1 text-accent px-4 py-1.5 rounded-lg border border-accent/40 shadow hover:bg-accent hover:text-bg1 dark:hover:text-bg1 transition">
                                        Refund
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-t1 dark:text-t1 font-semibold">{{ $txn->reference }}</td>
                                <td class="px-4 py-3 text-t1 dark:text-t1">{{ $userPhone }}</td>
                                <td class="px-4 py-3 capitalize font-semibold {{ $statusColor }}">
                                    {{ ucfirst($txn->status) }}
                                </td>
                                <td class="px-4 py-3 text-t3 dark:text-t3">{{ $title }}</td>
                                <td class="px-4 py-3 {{ $amountColor }}">₦{{ number_format($txn->amount, 2) }}</td>
                                <td class="px-4 py-3 text-t3 dark:text-t3">₦{{ number_format($prevBalance, 2) }}</td>
                                <td class="px-4 py-3 text-t1 dark:text-accent font-semibold">₦{{ number_format($newBalance, 2) }}</td>
                                <td class="px-4 py-3 text-t3 dark:text-t3">{{ $txn->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-6 text-t3 dark:text-t3">
                                    No completed or failed transactions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>

