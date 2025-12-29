<x-layouts.admin>
<div class="p-6 min-h-screen bg-bg1 dark:bg-bg1 text-t1 dark:text-t1 space-y-10">

    <div class="w-full flex justify-start mt-6 mb-4">
        <a href="{{ url()->previous() }}" class="text-accent hover:underline flex items-center">
            <i class="material-icons mr-1 text-accent">arrow_back</i> Back
        </a>
    </div>

    {{-- PROCESSING ORDERS --}}
    <div class="bg-bg2 dark:bg-bg2 rounded-2xl mt-15 shadow-[0_0_20px_rgba(0,255,209,0.1)] p-6 border border-accent/20">
        <h2 class="text-lg font-semibold mb-4 text-accent">Processing Orders</h2>

        <button
            class="bg-accent text-bg1 dark:text-bg1 px-5 py-2 rounded-xl font-medium mb-5 shadow-[0_0_10px_accent] hover:opacity-80 transition">
            Mark All Processing as Successful
        </button>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left rounded-xl overflow-hidden border border-accent/10">
                <thead class="bg-bg3 dark:bg-bg3 text-accent uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3">Action</th>
                        <th class="px-4 py-3">Reseller No</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Product</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($processings as $txn)
                        <tr class="border-b border-accent/10 hover:bg-bg3 dark:hover:bg-bg3/50 transition">
                            <td class="px-4 py-3 flex gap-3">
                                <button
                                    class="bg-bg1 dark:bg-bg1 text-accent px-4 py-1.5 rounded-lg border border-accent/40 shadow hover:bg-accent hover:text-bg1 dark:hover:text-bg1 transition">
                                    Refund
                                </button>
                                <button
                                    class="bg-accent text-bg1 dark:text-bg1 px-4 py-1.5 rounded-lg shadow-[0_0_10px_accent] hover:opacity-80 transition">
                                    Success
                                </button>
                            </td>
                            <td class="px-4 py-3 text-t1 dark:text-t1">{{ $txn->user->phone ?? 'N/A' }}</td>
                            <td class="px-4 py-3 capitalize text-yellow-400 dark:text-yellow-400">{{ $txn->status }}</td>
                            <td class="px-4 py-3 text-t3 dark:text-t3">{{ $txn->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-t3 dark:text-t3">No processing orders</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
</x-layouts.admin>
