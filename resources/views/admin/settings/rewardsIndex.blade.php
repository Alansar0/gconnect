
<x-layouts.admin>
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Reward Settings</h1>

    @if(session('success'))
        <div class="p-3 bg-green-100 text-green-800 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 gap-4">
        @foreach($rewards as $reward)
            <div class="p-4 border rounded flex justify-between items-center">
                <div>
                    <div class="font-semibold">{{ ucfirst($reward->for) }}</div>
                    <div class="text-sm text-gray-600">Cashback: ₦{{ number_format($reward->cashback_amount,2) }} • Voucher rate: ₦{{ number_format($reward->voucher_rate,2) }}</div>
                </div>
                <div>
                    <a href="{{ route('rewards.edit', $reward->for) }}" class="px-4 py-2 bg-indigo-600 text-white rounded">Edit</a>
                </div>
            </div>
        @endforeach
    </div>
</div>

</x-layouts.admin>