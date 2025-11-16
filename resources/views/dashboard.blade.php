<x-layouts.app>

    <!-- Header -->
    <div
        class="w-full bg-[#182430] rounded-b-2xl p-4 sticky top-0 z-50 shadow-[0_0_15px_rgba(88,166,255,0.4)] flex flex-col">
        <div>
            <p class="mt-8 text-sm text-[#f0f6fc]/80">Hello,</p>
            <h1 class="text-xl font-semibold text-[#58a6ff] -mt-1">John Doe</h1>
        </div>

        <!-- Admin Panel Button -->
        @if (auth()->user()->isAdmin())
            <div class="flex justify-center absolute top-8 left-10 transform translate-x-1/2 -translate-y-1/2">
                <a href="{{ route('admin.dashboard') }}"
                    class="inline-flex items-center px-4 py-2 bg-[#58a6ff]/10 border border-[#58a6ff]/40 text-[#f0f6fc] text-sm font-semibold rounded-md shadow hover:bg-[#58a6ff]/20 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-[#58a6ff]" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Admin Panel
                </a>
            </div>
        @endif

        <!-- Notifications -->
        <div>
            <a href="{{ route('notifications.index') }}"
                class="absolute top-12 right-8 bg-transparent text-[#f0f6fc] text-xl flex items-center">
                <span class="material-icons">notifications</span>
                <span
                    class="absolute -top-2 -right-2 bg-[#58a6ff] rounded-full text-xs font-bold w-4 h-4 flex items-center justify-center">3</span>
            </a>
        </div>
    </div>

    <!-- Wallet Card -->
    <div
        class="mx-5 my-5 bg-gradient-to-br from-[#161b22] to-[#0f172a] rounded-2xl p-8 text-[#f0f6fc] shadow-[0_0_15px_rgba(88,166,255,0.3)] flex justify-between">
        <div>
            <span class="text-lg text-[#f0f6fc]/70">Wallet Balance</span>
            <div class="text-2xl font-bold mt-1">₦{{ number_format($wallet->balance, 2) }}</div>
        </div>
        <div>
            <span class="text-lg text-[#58a6ff] flex items-center gap-1">Palmpay <i
                    class="material-icons text-sm">content_copy</i></span>
            <div class="mt-1 text-lg text-[#f0f6fc]">{{ $wallet->account_number }}</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 gap-3 mx-5 my-5">
        <a href="{{ route('user.accno') }}"
            class="bg-[#161b22] border border-[#58a6ff]/40 p-4 rounded-2xl text-center transition hover:bg-[#182430] cursor-pointer shadow-md">
            <div class="bg-[#182430] border border-[#58a6ff]/30 rounded-xl inline-block py-3 px-4.5 text-[#58a6ff]">
                <i class="fas fa-wallet text-3xl"></i>
            </div>
            <span class="block mt-2 text-sm text-[#f0f6fc]">Fund Wallet</span>
        </a>
        <a href="{{ route('getVoucher.buy') }}"
            class="bg-[#161b22] border border-[#58a6ff]/40 p-4 rounded-2xl text-center transition hover:bg-[#182430] cursor-pointer shadow-md">
            <div class="bg-[#182430] border border-[#58a6ff]/30 rounded-xl inline-block py-3 px-4 text-[#58a6ff]">
                <img src="{{ Vite::asset('resources/images/voucher-icon.png') }}" alt="Voucher Icon" class="w-8 h-8" />
            </div>
            <span class="block mt-2 text-sm text-[#f0f6fc]">Get Vouchers</span>
        </a>
    </div>

    <!-- Voucher Carousel -->
    <div class="max-w-md mx-5 my-5 mt-4">
        <div id="voucher-carousel" class="overflow-hidden">
            <div class="flex transition-transform duration-700" style="transform: translateX(0%)" id="voucher-track">

                <!-- Voucher Card Template -->
                @foreach ([['title' => 'Wi-Fi', 'code' => 'ABC123', 'amount' => '₦2,000', 'icon' => 'wifi', 'expires' => '3 days'], ['title' => 'Data', 'code' => 'XYZ789', 'amount' => '₦5,000', 'icon' => 'signal_cellular_alt', 'expires' => '7 days'], ['title' => 'Airtime', 'code' => 'LMN456', 'amount' => '₦1,000', 'icon' => 'phone_android', 'expires' => '1 day']] as $v)
                    <div class="min-w-full px-2">
                        <div
                            class="rounded-2xl bg-[#161b22] border border-[#58a6ff]/20 p-4 shadow-[0_0_15px_rgba(88,166,255,0.3)]">
                            <div class="flex justify-between text-xs text-[#f0f6fc]/60">
                                <span>System: {{ $v['title'] }}</span>
                                <span><i class="material-icons text-[#58a6ff]">{{ $v['icon'] }}</i></span>
                            </div>
                            <div class="mt-3">
                                <div>Get a <span class="text-[#58a6ff] font-semibold">{{ $v['code'] }}</span></div>
                                <div class="text-sm text-[#f0f6fc]/70">Voucher for {{ $v['amount'] }}</div>
                                <div class="mt-2 h-px bg-[#58a6ff]/20"></div>
                            </div>
                            <div class="mt-2 text-xs text-[#f0f6fc]/60">Expires in {{ $v['expires'] }}</div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
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
    </script>

    <!-- Recent Transactions -->
    <div class="mx-5 my-5">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-lg text-[#58a6ff] font-semibold">Recent Transactions</h2>
            <a href="{{ route('transactions.index') }}" class="text-sm text-[#58a6ff]/80 no-underline hover:text-[#58a6ff]">View All</a>
        </div>

        @foreach ([['title' => 'Voucher Purchased', 'date' => '20.01.2025', 'amount' => '-₦96', 'status' => 'Successful', 'color' => 'red-400'], ['title' => 'Wallet Funded', 'date' => '20.01.2025', 'amount' => '+₦190', 'status' => 'Failed', 'color' => 'green-400'], ['title' => 'Dribbble', 'date' => '01.01.2025', 'amount' => '-₦29.99', 'status' => 'Merchant Payment', 'color' => 'red-400']] as $t)
            <div
                class="bg-[#161b22] border border-[#58a6ff]/20 rounded-xl p-4 mb-3 shadow-[0_0_12px_rgba(88,166,255,0.3)] flex justify-between items-center">
                <div class="flex flex-col">
                    <strong class="text-base text-[#f0f6fc]">{{ $t['title'] }}</strong>
                    <span class="text-xs text-[#f0f6fc]/60">{{ $t['status'] }}</span>
                </div>
                <div class="text-right text-base font-bold text-{{ $t['color'] }}">
                    {{ $t['amount'] }}
                    <small class="block text-xs text-[#f0f6fc]/50">{{ $t['date'] }}</small>
                </div>
            </div>
        @endforeach
    </div>

</x-layouts.app>
