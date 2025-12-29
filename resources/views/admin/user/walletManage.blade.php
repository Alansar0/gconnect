{{-- <x-layouts.admin>
    <div class="min-h-screen bg-[#0B141A] text-white p-8">
        <h1 class="text-2xl font-bold mb-6">💰 Wallet Management</h1>

        <!-- Search User -->
        <form action="{{ route('wallets.manage') }}" method="GET" class="flex items-center gap-3 mb-6">
            <input
                type="text"
                name="query"
                value="{{ request('query') }}"
                placeholder="Search by email, phone, or account number"
                class="px-4 py-2 rounded-xl w-96 bg-[#1A242F] border border-[#2A3A4A] text-white focus:outline-none"
            />
            <button class="bg-[#00FFD1] text-black font-semibold px-4 py-2 rounded-xl hover:bg-[#00CCAA]">
                Search
            </button>
        </form>

        @if($wallet)
            <div class="bg-[#101B25] border border-[#1E2A38] rounded-2xl p-6 shadow-xl">
                <h2 class="text-xl font-semibold mb-4">
                    {{ $wallet->user->name ?? 'Unnamed User' }}
                    <span class="text-sm text-gray-400">({{ $wallet->user->email }})</span>
                </h2>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <div class="p-4 bg-[#0E1822] rounded-xl text-center">
                        <p class="text-gray-400 text-sm">Main Balance</p>
                        <p class="text-xl font-bold text-[#00FFD1]">₦{{ number_format($wallet->balance, 2) }}</p>
                    </div>
                    <div class="p-4 bg-[#0E1822] rounded-xl text-center">
                        <p class="text-gray-400 text-sm">Cashback</p>
                        <p class="text-xl font-bold text-[#F5B342]">₦{{ number_format($wallet->cashback_balance, 2) }}</p>
                    </div>
                    <div class="p-4 bg-[#0E1822] rounded-xl text-center">
                        <p class="text-gray-400 text-sm">Voucher</p>
                        <p class="text-xl font-bold text-[#00A2FF]">₦{{ number_format($wallet->voucher_balance, 2) }}</p>
                    </div>
                    <div class="p-4 bg-[#0E1822] rounded-xl text-center">
                        <p class="text-gray-400 text-sm">Account Number</p>
                        <p class="text-lg font-mono">{{ $wallet->account_number }}</p>
                    </div>
                </div>

                <!-- Manual Credit/Debit Form -->
                <form action="{{ route('wallets.updateFund', $wallet->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm mb-1">Action</label>
                            <select name="type" class="w-full px-4 py-2 rounded-xl bg-[#1A242F] border border-[#2A3A4A] text-white">
                                <option value="credit">Credit Wallet</option>
                                <option value="debit">Debit Wallet</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Amount (₦)</label>
                            <input
                                type="number"
                                step="0.01"
                                name="amount"
                                required
                                class="w-full px-4 py-2 rounded-xl bg-[#1A242F] border border-[#2A3A4A] text-white"
                            />
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Description</label>
                            <input
                                type="text"
                                name="description"
                                placeholder="e.g. Manual top-up"
                                class="w-full px-4 py-2 rounded-xl bg-[#1A242F] border border-[#2A3A4A] text-white"
                            />
                        </div>
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            class="bg-[#00FFD1] text-black font-semibold px-6 py-2 rounded-xl hover:bg-[#00CCAA]">
                            Process Transaction
                        </button>
                    </div>
                </form>
            </div>

            <!-- Transaction History -->
            <div class="mt-10">
                <h3 class="text-xl font-semibold mb-4">🧾 Recent Transactions</h3>
                <div class="overflow-x-auto rounded-2xl border border-[#1E2A38]">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-[#16222E] text-gray-400 uppercase">
                            <tr>
                                <th class="px-4 py-3">Ref</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Amount</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Description</th>
                                <th class="px-4 py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#1E2A38]">
                            @forelse($transactions as $t)
                                <tr class="hover:bg-[#0E1822] transition">
                                    <td class="px-4 py-3 text-gray-300">{{ $t->reference ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        @if($t->type == 'credit')
                                            <span class="text-[#00FFD1] font-semibold">+ Credit</span>
                                        @else
                                            <span class="text-red-400 font-semibold">- Debit</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">₦{{ number_format($t->amount, 2) }}</td>
                                    <td class="px-4 py-3">{{ ucfirst($t->status) }}</td>
                                    <td class="px-4 py-3 text-gray-400">{{ $t->description }}</td>
                                    <td class="px-4 py-3 text-gray-400">{{ $t->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-6 text-gray-500">No transactions yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif(request('query'))
            <p class="text-center text-gray-400 mt-6">No wallet found for your search.</p>
        @endif
    </div>
</x-layouts.admin> --}}

<x-layouts.admin>
    <div class="min-h-screen bg-bg1 dark:bg-bg1 text-t1 dark:text-t1 p-8">

        <h1 class="text-2xl font-bold mb-6 text-accent dark:text-accent">💰 Wallet Management</h1>

        <!-- Search User -->
        <form action="{{ route('wallets.manage') }}" method="GET" class="flex items-center gap-3 mb-6">
            <input
                type="text"
                name="query"
                value="{{ request('query') }}"
                placeholder="Search by email, phone, or account number"
                class="px-4 py-2 rounded-xl w-96 bg-bg2 dark:bg-bg2 border border-accent dark:border-accent text-t1 dark:text-t1 focus:outline-none"
            />
            <button class="bg-accent text-bg1 font-semibold px-4 py-2 rounded-xl hover:opacity-90">
                Search
            </button>
        </form>

        @if($wallet)
            <div class="bg-bg2 dark:bg-bg2 border border-accent dark:border-accent rounded-2xl p-6 shadow-xl">

                <h2 class="text-xl font-semibold mb-4 text-t1 dark:text-t1">
                    {{ $wallet->user->name ?? 'Unnamed User' }}
                    <span class="text-t3 dark:text-t3 text-sm">({{ $wallet->user->email }})</span>
                </h2>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <div class="p-4 bg-bg3 dark:bg-bg3 rounded-xl text-center">
                        <p class="text-t3 dark:text-t3 text-sm">Main Balance</p>
                        <p class="text-xl font-bold text-accent">₦{{ number_format($wallet->balance, 2) }}</p>
                    </div>
                    <div class="p-4 bg-bg3 dark:bg-bg3 rounded-xl text-center">
                        <p class="text-t3 dark:text-t3 text-sm">Cashback</p>
                        <p class="text-xl font-bold text-[#F5B342]">₦{{ number_format($wallet->cashback_balance, 2) }}</p>
                    </div>
                    <div class="p-4 bg-bg3 dark:bg-bg3 rounded-xl text-center">
                        <p class="text-t3 dark:text-t3 text-sm">Voucher</p>
                        <p class="text-xl font-bold text-[#00A2FF]">₦{{ number_format($wallet->voucher_balance, 2) }}</p>
                    </div>
                    <div class="p-4 bg-bg3 dark:bg-bg3 rounded-xl text-center">
                        <p class="text-t3 dark:text-t3 text-sm">Account Number</p>
                        <p class="text-lg font-mono text-t1 dark:text-t1">{{ $wallet->account_number }}</p>
                    </div>
                </div>

                <!-- Manual Credit/Debit Form -->
                <form action="{{ route('wallets.updateFund', $wallet->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm mb-1 text-t2 dark:text-t2">Action</label>
                            <select name="type" class="w-full px-4 py-2 rounded-xl bg-bg2 dark:bg-bg2 border border-accent dark:border-accent text-t1 dark:text-t1">
                                <option value="credit">Credit Wallet</option>
                                <option value="debit">Debit Wallet</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm mb-1 text-t2 dark:text-t2">Amount (₦)</label>
                            <input
                                type="number"
                                step="0.01"
                                name="amount"
                                required
                                class="w-full px-4 py-2 rounded-xl bg-bg2 dark:bg-bg2 border border-accent dark:border-accent text-t1 dark:text-t1"
                            />
                        </div>

                        <div>
                            <label class="block text-sm mb-1 text-t2 dark:text-t2">Description</label>
                            <input
                                type="text"
                                name="description"
                                placeholder="e.g. Manual top-up"
                                class="w-full px-4 py-2 rounded-xl bg-bg2 dark:bg-bg2 border border-accent dark:border-accent text-t1 dark:text-t1"
                            />
                        </div>
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            class="bg-accent text-bg1 font-semibold px-6 py-2 rounded-xl hover:opacity-90">
                            Process Transaction
                        </button>
                    </div>
                </form>
            </div>

            <!-- Transaction History -->
            <div class="mt-10">
                <h3 class="text-xl font-semibold mb-4 text-t1 dark:text-t1">🧾 Recent Transactions</h3>
                <div class="overflow-x-auto rounded-2xl border border-accent dark:border-accent">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-bg3 dark:bg-bg3 text-t3 dark:text-t3 uppercase">
                            <tr>
                                <th class="px-4 py-3">Ref</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Amount</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Description</th>
                                <th class="px-4 py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-accent/20">
                            @forelse($transactions as $t)
                                <tr class="hover:bg-bg3 dark:hover:bg-bg3 transition">
                                    <td class="px-4 py-3 text-t2 dark:text-t2">{{ $t->reference ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        @if($t->type == 'credit')
                                            <span class="text-accent font-semibold">+ Credit</span>
                                        @else
                                            <span class="text-red-500 font-semibold">- Debit</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-t1 dark:text-t1">₦{{ number_format($t->amount, 2) }}</td>
                                    <td class="px-4 py-3 text-t1 dark:text-t1">{{ ucfirst($t->status) }}</td>
                                    <td class="px-4 py-3 text-t3 dark:text-t3">{{ $t->description }}</td>
                                    <td class="px-4 py-3 text-t3 dark:text-t3">{{ $t->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-6 text-t3 dark:text-t3">No transactions yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif(request('query'))
            <p class="text-center text-t3 dark:text-t3 mt-6">No wallet found for your search.</p>
        @endif
    </div>
</x-layouts.admin>

