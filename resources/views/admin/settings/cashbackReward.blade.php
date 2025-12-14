

<x-layouts.admin>
<div class="container mx-auto p-6 max-w-2xl">
    <h1 class="text-2xl font-semibold mb-4">Edit Reward ({{ ucfirst($reward->for) }})</h1>

<form method="POST" action="{{ route('rewards.update', $reward->for) }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block mb-1">Cashback Amount (₦)</label>
            <input type="number" step="0.01" name="cashback_amount" value="{{ old('cashback_amount', $reward->cashback_amount) }}" class="w-full p-2 border rounded" required>
            @error('cashback_amount') <div class="text-red-600">{{ $message }}</div> @enderror
        </div>

        <div class="mb-4">
            <label class="block mb-1">Voucher Rate (cashback ₦ equivalent for 1 voucher)</label>
            <input type="number" step="0.01" name="voucher_rate" value="{{ old('voucher_rate', $reward->voucher_rate) }}" class="w-full p-2 border rounded" required>
            @error('voucher_rate') <div class="text-red-600">{{ $message }}</div> @enderror
        </div>

        <div class="mb-4">
            <label class="block mb-1">Note (optional)</label>
            <textarea name="note" class="w-full p-2 border rounded">{{ old('note', $reward->note) }}</textarea>
        </div>

        <div class="flex gap-2">
            <button class="px-4 py-2 bg-green-600 text-white rounded">Save</button>
            <a href="{{ route('rewards.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
        </div>
    </form>
</div>
</x-layouts.admin>
