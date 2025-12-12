<x-layouts.app>
    <div class="bg-[#0d0f0e] text-white min-h-screen font-sans p-5 flex flex-col">
        <div class=" w-full flex justify-start mt-6 mb-4">
            <a href="{{ url()->previous() }}" class="text-[#58a6ff] hover:underline flex items-center">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a>
        </div>
        <!-- Header -->
        <div class="flex items-center justify-center mb-6">
            <h1 class="text-lg font-semibold">Transaction Details</h1>
        </div>

        <!-- Transaction Summary Card -->
        <div class="bg-[#1b1b1f] rounded-2xl shadow-lg p-5 mb-5 border border-[#2b5da4]">
            <div class="flex flex-col items-center text-center">
                <h2 class="text-lg font-semibold mb-1">
                    {{ $voucher->profile->name ?? 'Voucher' }}
                </h2>
                <h3 class="text-3xl font-bold mb-2 text-white">
                        ₦{{ number_format(session('purchase_data.amount') ?? 0, 2) }}
                </h3>
                <div class="flex items-center justify-center text-[#58a6ff] font-medium mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ $transaction && $transaction->status === 'success' ? 'Successful' : 'Failed' }}
                </div>
            </div>
        </div>

        <!-- Transaction Details Card -->
        <div class="bg-[#1b1b1f] rounded-2xl shadow-md border border-[#2b5da4] p-5 mb-8">
            <h3 class="text-[#58a6ff] font-semibold mb-4">Transaction Details</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-400">Voucher Username</span>
                    <span class="font-medium text-white">{{ $voucher->code }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Voucher Pin</span>
                    <span class="font-medium text-white">{{ $voucher->password }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Transaction Type</span>
                    <span class="font-medium text-white">Voucher</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Payment Method</span>
                    <span class="font-medium flex items-center text-white">
                        Wallet
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 text-[#58a6ff]" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Transaction No.</span>
                    <span class="text-gray-300 text-xs">{{ $transaction->reference ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Transaction Date</span>
                    <span class="font-medium text-gray-200">
                        {{ $transaction ? $transaction->created_at->format('M d, Y H:i:s') : '-' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-4 mt-auto">
            <button onclick="window.location.href='{{route('help.index')}}'"  class="flex-1 border border-[#58a6ff] text-[#58a6ff] font-semibold py-3 rounded-full hover:bg-[#1b1b1f] hover:border-[#3a75c4] transition-all">
                Report Issue
            </button>
            <button onclick="copyReceipt()" class="flex-1 bg-[#58a6ff] hover:bg-[#3a75c4] text-black font-semibold py-3 rounded-full transition-all">
                Share Receipt
            </button>
        </div>
    </div>
    <script>
    function copyReceipt() {
        const text = `Voucher: {{ $voucher->code }} / Pin: {{ $voucher->password }} / Value: ₦{{ number_format($voucher->profile->price ?? 0, 2) }} / Ref: {{ $transaction->reference ?? '-' }}`;
        navigator.clipboard.writeText(text).then(function() {
            alert('Receipt copied to clipboard!');
        });
    }
    </script>
</x-layouts.app>
