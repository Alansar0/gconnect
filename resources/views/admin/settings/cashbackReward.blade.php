<x-layouts.admin>
    <div class="container mx-auto p-6 max-w-2xl bg-bg1 text-t1 font-sans rounded-2xl">
        <h1 class="text-2xl font-semibold mb-4 text-accent">Edit Reward ({{ ucfirst($reward->for) }})</h1>

        <form method="POST" action="{{ route('rewards.update', $reward->for) }}">
            @csrf
            @method('PUT')

            <!-- Cashback Amount -->
            <div class="mb-4">
                <label class="block mb-1 text-t3">Cashback Amount (₦)</label>
                <input type="number" step="0.01" name="cashback_amount" 
                       value="{{ old('cashback_amount', $reward->cashback_amount) }}"
                       class="w-full p-2 rounded-xl border border-accent-border bg-bg2 text-t1" required>
                @error('cashback_amount') 
                    <div class="text-red-600">{{ $message }}</div> 
                @enderror
            </div>

            <!-- Voucher Rate -->
            <div class="mb-4">
                <label class="block mb-1 text-t3">Voucher Rate (cashback ₦ equivalent for 1 voucher)</label>
                <input type="number" step="0.01" name="voucher_rate" 
                       value="{{ old('voucher_rate', $reward->voucher_rate) }}"
                       class="w-full p-2 rounded-xl border border-accent-border bg-bg2 text-t1" required>
                @error('voucher_rate') 
                    <div class="text-red-600">{{ $message }}</div> 
                @enderror
            </div>
            <!-- Admin Commission -->
        <div class="mb-4">
            <label class="block mb-1 text-t3">
                Admin Commission (%) for this reseller
            </label>
            <input
                type="number"
                step="0.01"
                min="0"
                max="100"
                name="commission_percent"
                value="{{ old('commission_percent', $reseller->commission_percent) }}"
                class="w-full p-2 rounded-xl border border-accent-border bg-bg2 text-t1"
                required
            >
        </div>

            <!-- Note -->
            <div class="mb-4">
                <label class="block mb-1 text-t3">Note (optional)</label>
                <textarea name="note" 
                          class="w-full p-2 rounded-xl border border-accent-border bg-bg2 text-t1">{{ old('note', $reward->note) }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex gap-2">
                <button class="px-4 py-2 bg-green-600 text-white rounded-xl hover:opacity-90 transition">
                    Save
                </button>
                <a href="{{ route('rewards.index') }}" 
                   class="px-4 py-2 bg-bg3 text-t1 rounded-xl hover:opacity-90 transition">
                   Cancel
                </a>
            </div>
        </form>
    </div>
</x-layouts.admin>

