
 <x-layouts.app>
    <div class="min-h-screen font-sans p-5 flex flex-col bg-bg1 text-t1">

        <!-- Back -->
        <div class="w-full flex justify-start mt-6 mb-4">
            <a href="{{ url()->previous() }}" class="text-accent hover:underline flex items-center">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a>
        </div>

        <!-- ================= RECEIPT AREA ================= -->
        <div id="receipt-area">

        <!-- Header -->
        <div class="flex items-center justify-center mb-6">
            <h1 class="text-lg font-semibold text-t1">
                Transaction Details
            </h1>
        </div>


            <!-- Transaction Summary Card -->
            <div class="bg-bg2 rounded-2xl shadow-lg p-5 mb-5 border border-accent/40">
                <div class="flex flex-col items-center text-center">

                    <h2 class="text-lg font-semibold mb-1 text-t1">
                        {{ $voucher->profile->name ?? 'Voucher' }}
                    </h2>

                    <h3 class="text-3xl font-bold mb-2 text-t1">
                        ₦{{ number_format(session('purchase_data.amount') ?? 0, 2) }}
                    </h3>

                    <div class="flex items-center justify-center text-accent font-medium mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 13l4 4L19 7" />
                        </svg>

                        {{ $transaction && $transaction->status === 'success' ? 'Successful' : 'Failed' }}
                    </div>

                </div>
            </div>

            <!-- Transaction Details Card -->
            <div class="bg-bg2 rounded-2xl shadow-md border border-accent/40 p-5 mb-8">

                <h3 class="text-accent font-semibold mb-4">
                    Transaction Details
                </h3>

                <div class="space-y-3 text-sm">

                    <div class="flex justify-between">
                        <span class="text-t3">Voucher Username</span>
                        <span class="font-medium text-t1">{{ $voucher->code }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-t3">Voucher Pin</span>
                        <span class="font-medium text-t1">{{ $voucher->password }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-t3">Transaction Type</span>
                        <span class="font-medium text-t1">Voucher</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-t3">Payment Method</span>
                        <span class="font-medium flex items-center text-t1">
                            Wallet
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-4 w-4 ml-1 text-accent"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-t3">Transaction No.</span>
                        <span class="text-t2 text-xs">
                            {{ $transaction->reference ?? '-' }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-t3">Transaction Date</span>
                        <span class="font-medium text-t2">
                            {{ $transaction ? $transaction->created_at->format('M d, Y H:i:s') : '-' }}
                        </span>
                    </div>

                </div>
            </div>

        </div>
        <!-- ================= END RECEIPT AREA ================= -->

        <!-- Action Buttons -->
        <div class="flex space-x-4 mt-auto">

            <button
                onclick="window.location.href='{{ route('help.index') }}'"
                class="flex-1 border border-accent text-accent font-semibold py-3 rounded-full
                       hover:bg-bg3 hover:border-accent/70 transition-all">
                Report Issue
            </button>

            <button
                onclick="shareReceipt()"
                class="flex-1 bg-accent hover:bg-accent/80 text-black
                       font-semibold py-3 rounded-full transition-all">
                Share Receipt
            </button>

        </div>

    </div>

    <!-- html2canvas -->
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

    <script>
        async function shareReceipt() {
            const receipt = document.getElementById('receipt-area');

            if (!receipt) {
                alert('Receipt not found');
                return;
            }

            try {
                const canvas = await html2canvas(receipt, {
                    scale: 2,
                    backgroundColor: null,
                    useCORS: true
                });

                canvas.toBlob(async (blob) => {
                    const file = new File([blob], 'transaction-receipt.png', {
                        type: 'image/png'
                    });

                    if (navigator.canShare && navigator.canShare({ files: [file] })) {
                        await navigator.share({
                            title: 'Transaction Receipt',
                            text: 'Here is my transaction receipt',
                            files: [file]
                        });
                    } else {
                        const link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.download = 'transaction-receipt.png';
                        link.click();
                    }
                });

            } catch (e) {
                console.error(e);
                alert('Failed to generate receipt image');
            }
        }
    </script>
</x-layouts.app>
