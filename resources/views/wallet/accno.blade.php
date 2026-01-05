<x-layouts.app>
    <div class="min-h-screen p-4 font-sans bg-bg1 text-t1">

        <!-- Back Button -->
        <div class="w-full flex justify-start mt-6 mb-4">
            <a href="{{ url()->previous() }}" class="text-accent hover:underline flex items-center">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a>
        </div>

        <!-- Header -->
        <div class="flex items-center justify-center mb-6">
            <h1 class="text-lg font-semibold">Wallet Funding</h1>
        </div>

        {{-- IF NO VIRTUAL ACCOUNT --}}
        @if ($virtualAccounts->isEmpty())
            <div class="bg-bg2 border border-accent-border rounded-xl p-6 text-center">
                <p class="text-sm text-t2 mb-4">
                    You don’t have a bank account for wallet funding yet.
                </p>

                <form method="POST" action="{{ route('wallet.createVirtualAccount') }}">
                    @csrf
                    <button
                        type="submit"
                        class="px-4 py-2 rounded-lg bg-accent text-white text-sm font-medium hover:opacity-90 transition">
                        Generate Bank Account
                    </button>
                </form>
            </div>
        @else
            <!-- Bank Cards -->
            <div class="space-y-4">
                @foreach ($virtualAccounts as $bank)
                    <div class="border border-accent-border rounded-xl p-4 bg-bg2">
                        <p class="text-t2 mb-2 font-medium">
                            Bank {{ $bank->bank_name }}
                        </p>

                        <div class="flex items-center mb-2">
                            <div
                                class="flex items-center bg-accent-soft/10 text-accent px-3 py-1 rounded-lg text-sm font-semibold">
                                <span id="acc-{{ $bank->id }}">{{ $bank->account_number }}</span>

                                <button
                                    onclick="copyText('acc-{{ $bank->id }}', 'Account Number')"
                                    class="ml-2">
                                    <i class="material-icons !text-[17px] text-t3">content_copy</i>
                                </button>
                            </div>
                        </div>

                        <p class="text-t3 text-sm">
                            {{ $bank->account_name }}
                        </p>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Toast -->
        <div id="copy-toast"
             class="fixed bottom-20 left-1/2 -translate-x-1/2
                    bg-bg2 border border-accent/40 text-t1
                    px-4 py-2 rounded-xl shadow-lg
                    text-sm font-medium
                    opacity-0 pointer-events-none
                    transition-all duration-300
                    z-50">
        </div>
    </div>

    <!-- Copy JS -->
    <script>
        function copyText(id, label = 'Text') {
            const el = document.getElementById(id);
            if (!el) return;

            const text = el.textContent.trim();
            navigator.clipboard.writeText(text).then(() => {
                showCopyToast(`${label} copied`);
            });
        }

        function showCopyToast(message) {
            const toast = document.getElementById('copy-toast');
            toast.textContent = message;
            toast.classList.remove('opacity-0');
            toast.classList.add('opacity-100');

            clearTimeout(window.__toast);
            window.__toast = setTimeout(() => {
                toast.classList.remove('opacity-100');
                toast.classList.add('opacity-0');
            }, 2000);
        }
    </script>
</x-layouts.app>
