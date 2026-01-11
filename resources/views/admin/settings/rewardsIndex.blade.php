
<x-layouts.admin>
    <div class="container mx-auto p-6 bg-bg1 text-t1 font-sans rounded-2xl">

        <!-- Page Title -->
        <h1 class="text-2xl font-semibold mb-4 text-accent">Reward Settings</h1>

        <!-- Success Message -->
        @if(session('success'))
            <div class="p-3 bg-accent text-bg1 rounded mb-4 font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <!-- Rewards List -->
        <div class="grid grid-cols-1 gap-4">
            @foreach($rewards as $reward)
                <div class="p-4 rounded-xl flex justify-between items-center border border-accent-border bg-bg2">
                    <div>
                        <div class="font-semibold text-t1">{{ ucfirst($reward->for) }}</div>
                        <div class="text-sm text-t3">
                            Cashback: ₦{{ number_format($reward->cashback_amount,2) }} • 
                            Voucher rate: ₦{{ number_format($reward->voucher_rate,2) }}
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('rewards.edit', $reward->for) }}" 
                           class="px-4 py-2 bg-accent text-bg1 rounded-xl font-semibold hover:opacity-90 transition">
                           Edit
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.admin>
