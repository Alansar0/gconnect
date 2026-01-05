    <x-layouts.app>

        <!-- Header -->
        <div class="w-full bg-bg1 rounded-b-2xl p-4 sticky top-0 z-50 shadow-accent flex flex-col">
            <div>
                <p class="mt-8 text-sm text-t2">Hello,</p>
                <h1 class="text-xl font-semibold text-accent -mt-1">John Doe</h1>
            </div>

            <!-- Admin Panel Button -->
            @if (auth()->user()->isAdmin())
                <div class="flex justify-center absolute top-8 left-10 transform translate-x-1/2 -translate-y-1/2">
                    <a href="{{ route('admin.dashboard') }}"
                        class="inline-flex items-center px-4 py-2 bg-accent/10 border border-accent text-t1 text-sm font-semibold rounded-md shadow-accent hover:bg-accent/20 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-accent" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Admin Panel
                    </a>
                </div>
            @endif

            <!-- Notifications -->

                <div>
                    <a href="{{ route('notifications.index') }}" class="absolute top-12 right-8 text-t1 text-xl flex items-center">
                        <span class="material-icons">notifications</span>
                        @php
                            $unreadCount = auth()->user()->unreadNotifications()->count();
                        @endphp

                        @if($unreadCount > 0)
                            <span
                                class="absolute -top-2 -right-2 bg-[red]  rounded-full text-xs font-bold w-4 h-4 flex items-center justify-center">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </a>
                </div>

        </div>

        <!-- Wallet Card -->
        <div class="mx-5 my-5 bg-bg2 rounded-2xl p-8 text-t1 shadow-accent flex justify-between">
            <div>
                <span class="text-lg text-t3">Wallet Balance</span>
                <div class="text-2xl font-bold mt-1 text-t1">₦{{ number_format($wallet->balance, 2) }}</div>
            </div>

            <div>
                <span class="text-lg text-accent flex items-center gap-1">
                    Palmpay
                    <button onclick="copyText('voucher-username','Bank Account')">
                        <i class="material-icons !text-[17px] text-t3">content_copy</i>
                    </button>
                </span>
                <div id="voucher-username" class="mt-1 text-lg text-t1">{{ $virtualAccount?->account_number ?? 'No account set' }}</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-2 gap-3 mx-5 my-5">
            <a href="{{ route('user.accno') }}"
                class="bg-bg2 border border-accent p-4 rounded-2xl text-center transition hover:bg-bg1 cursor-pointer shadow-accent">
                <div class="bg-bg1 border border-accent rounded-xl inline-block py-3 px-4.5 text-accent">
                    <i class="fas fa-wallet text-3xl"></i>
                </div>
                <span class="block mt-2 text-sm text-t1">Fund Wallet</span>
            </a>

            <a href="{{ route('getVoucher.buy') }}"
                class="bg-bg2 border border-accent p-4 rounded-2xl text-center transition hover:bg-bg1 cursor-pointer shadow-accent">
                <div class="bg-bg1 border border-accent rounded-xl inline-block py-3 px-4 text-accent">
                    <img src="{{ Vite::asset('resources/images/voucher-icon.png') }}" alt="Voucher Icon" class="w-8 h-8" />
                </div>
                <span class="block mt-2 text-sm text-t1">Get Vouchers</span>
            </a>
        </div>

        <!-- Voucher Carousel -->
        <div class="max-w-md mx-5 my-5 mt-4">
            <div id="voucher-carousel" class="overflow-hidden">
                <div class="flex transition-transform duration-700" style="transform: translateX(0%)" id="voucher-track">

                    @foreach ([['title' => 'Wi-Fi', 'code' => 'ABC123', 'amount' => '₦2,000', 'icon' => 'wifi', 'expires' => '3 days'],
                            ['title' => 'Data', 'code' => 'XYZ789', 'amount' => '₦5,000', 'icon' => 'signal_cellular_alt', 'expires' => '7 days'],
                            ['title' => 'Airtime', 'code' => 'LMN456', 'amount' => '₦1,000', 'icon' => 'phone_android', 'expires' => '1 day']] as $v)
                        <div class="min-w-full px-2">
                            <div class="rounded-2xl bg-bg2 border border-accent p-4 shadow-accent">
                                <div class="flex justify-between text-xs text-t3">
                                    <span>System: {{ $v['title'] }}</span>
                                    <i class="material-icons text-accent">{{ $v['icon'] }}</i>
                                </div>

                                <div class="mt-3">
                                    <div>Get a <span class="text-accent font-semibold">{{ $v['code'] }}</span></div>
                                    <div class="text-sm text-t2">Voucher for {{ $v['amount'] }}</div>
                                    <div class="mt-2 h-px bg-accent"></div>
                                </div>

                                <div class="mt-2 text-xs text-t3">Expires in {{ $v['expires'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    {{-- Recent Transactions --}}
    <div class="mx-5 my-5">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-lg text-accent font-semibold">Recent Transactions</h2>
                <a href="{{ route('transactions.index') }}" class="text-sm text-accent no-underline hover:text-accent">View All</a>
            </div>
            @foreach ($transactions->take(4) as $transaction)
        <div class="
            bg-bg2 border border-accent rounded-2xl p-3
            flex justify-between items-center mb-1.5
            shadow-accent hover:shadow-[0_0_20px_var(--accent)]
            transition-all duration-300
        ">
            <div class="flex flex-col">
                @php
                    $title = match($transaction->description) {
                        'voucher_purchase' => 'Voucher Purchased',
                        'manual_deduction' => 'Manually Deducted',
                        'manual_funding'   => 'Manually Funded',
                        'wallet_funded'    => 'Wallet Funded',
                        default             => ucfirst($transaction->description)
                    };
                @endphp

                <strong class="text-base text-t1 font-medium">
                    {{ $title }}
                </strong>

                <span class="text-xs text-t3 mt-1">
                    {{ $transaction->created_at->format('d M, Y h:i A') }}
                </span>
            </div>

            <div class="text-right">
                <span class="text-base font-semibold {{ $transaction->type == 'credit' ? 'text-green-400' : 'text-red-400' }}">
                    ₦{{ number_format($transaction->amount, 2) }}
                </span>
                <small class="block text-xs mt-1 text-t3 capitalize">
                    {{ $transaction->status }}
                </small>
            </div>
        </div>
    @endforeach
    <!-- Toast -->
        <div id="copy-toast"
            class="fixed bottom-120 left-1/2 -translate-x-1/2
                    bg-bg2 border border-accent/40 text-t1
                    px-4 py-2 rounded-xl shadow-lg
                    text-sm font-medium
                    opacity-0 pointer-events-none
                    transition-all duration-300
                    z-50">
        </div>

        <!-- Auto Carousel JS -->
        <script>
            const track = document.getElementById('voucher-track');
            let current = 0;
            const total = 3;

            function updateCarousel() {
                track.style.transform = `translateX(-${current * 100}%)`;
            }
            setInterval(() => {
                current = (current + 1) % total;
                updateCarousel();
            }, 3000);

        
            // ---------- COPY TOAST LOGIC ----------
            function copyText(id, label = 'Text') {
                const el = document.getElementById(id);
                if (!el) {
                    showCopyToast('Nothing to copy');
                    return;
                }

                const text = el.textContent.trim();

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text)
                        .then(() => showCopyToast(`${label} copied: ${text}`))
                        .catch(() => fallbackCopy(text, label));
                } else {
                    fallbackCopy(text, label);
                }
            }

            function fallbackCopy(text, label) {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();

                try {
                    document.execCommand('copy');
                    showCopyToast(`${label} copied: ${text}`);
                } catch {
                    showCopyToast('Failed to copy');
                }

                document.body.removeChild(textarea);
            }

            function showCopyToast(message) {
                const toast = document.getElementById('copy-toast');
                toast.textContent = message;

                toast.classList.remove('opacity-0');
                toast.classList.add('opacity-100');

                clearTimeout(window.__copyToastTimer);
                window.__copyToastTimer = setTimeout(() => {
                    toast.classList.remove('opacity-100');
                    toast.classList.add('opacity-0');
                }, 2200);
            }
        </script>
    </x-layouts.app>

