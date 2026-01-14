<x-layouts.admin>
    <div class="container mx-auto p-6 max-w-xl bg-bg1 rounded-2xl text-t1">

        <h1 class="text-2xl font-semibold mb-4 text-accent">
            Edit Reseller Commission
        </h1>

        <form method="POST" action="{{ route('admin.Commission.update', $reseller) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block mb-1 text-t3">
                    Commission Percentage (%)
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

            <div class="flex gap-2">
                <button class="px-4 py-2 bg-green-600 text-white rounded-xl">
                    Save
                </button>
                <a href="{{ route('admin.Commission.index') }}"
                   class="px-4 py-2 bg-bg3 rounded-xl">
                    Cancel
                </a>
            </div>
        </form>

    </div>
</x-layouts.admin>
