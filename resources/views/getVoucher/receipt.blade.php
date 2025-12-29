{{-- <x-layouts.app>
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
</x-layouts.app> --}}

<x-layouts.app>
    <div class="min-h-screen flex flex-col items-center justify-center bg-bg2 text-t1 font-sans py-2 px-4">

        <!-- Back Button -->
        <a href="{{ url()->previous() }}"
            class="absolute left-6 top-6 text-accent hover:underline flex items-center">
            <i class="material-icons mr-1">arrow_back</i> Back
        </a>

        <!-- Header -->
        <div class="text-center mt-6 mb-6">
            <div class="flex justify-center mb-4">
                <img src="https://cdn-icons-png.flaticon.com/512/2331/2331942.png"
                     alt="Help Icon"
                     class="w-20 h-20">
            </div>

            <h1 class="text-2xl font-bold text-accent mb-2">
                Get Your Voucher
            </h1>

            <p class="text-t2 text-sm leading-relaxed max-w-sm mx-auto">
                Purchase a data or Wi-Fi voucher easily and get your access code instantly.
                Stay connected — anytime, anywhere.
            </p>
        </div>

        <!-- Flash Sales Section -->
        <h2 class="font-semibold text-lg mb-2 text-t1">
            Flash Sales <span class="text-t3 text-sm">ℹ️</span>
        </h2>

        <!-- Flash Sales Carousel -->
        <div class="relative max-w-md mx-auto overflow-hidden rounded-2xl mb-8 w-full">
            <div id="flash-track" class="flex transition-transform duration-700 ease-in-out">

                <!-- Flash Card 1 -->
                <div class="min-w-full p-3">
                    <div class="bg-bg1 rounded-2xl border border-accent p-4">
                        <div class="flex justify-between items-center mb-1">
                            <p class="font-semibold text-t1">1GB / 1 DAY</p>
                            <span class="bg-bg3 text-xs px-2 py-1 rounded-md text-t2">Sold Out</span>
                        </div>

                        <div class="text-t3 text-sm line-through">₦500</div>
                        <div class="text-accent text-sm">₦10 | 98% OFF</div>
                        <div class="h-1 bg-accent mt-3 rounded-full"></div>
                    </div>
                </div>

                <!-- Flash Card 2 -->
                <div class="min-w-full p-3">
                    <div class="bg-bg1 rounded-2xl border border-accent p-4">
                        <div class="flex justify-between items-center mb-1">
                            <p class="font-semibold text-t1">2GB / 3 DAYS</p>
                            <span class="bg-accent/20 text-xs px-2 py-1 rounded-md text-accent">Active</span>
                        </div>

                        <div class="text-t3 text-sm line-through">₦1000</div>
                        <div class="text-accent text-sm">₦20 | 97% OFF</div>
                        <div class="h-1 bg-accent mt-3 rounded-full"></div>
                    </div>
                </div>

                <!-- Flash Card 3 -->
                <div class="min-w-full p-3">
                    <div class="bg-bg1 rounded-2xl border border-accent p-4">
                        <div class="flex justify-between items-center mb-1">
                            <p class="font-semibold text-t1">500MB / 6 HRS</p>
                            <span class="bg-accent/20 text-xs px-2 py-1 rounded-md text-accent">Hot 🔥</span>
                        </div>

                        <div class="text-t3 text-sm line-through">₦300</div>
                        <div class="text-accent text-sm">₦5 | 99% OFF</div>
                        <div class="h-1 bg-accent mt-3 rounded-full"></div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Carousel Script -->
        <script>
            const flashTrack = document.getElementById('flash-track');
            let flashIndex = 0;
            const flashTotal = 3;

            function updateFlashCarousel() {
                flashTrack.style.transform = `translateX(-${flashIndex * 100}%)`;
            }

            setInterval(() => {
                flashIndex = (flashIndex + 1) % flashTotal;
                updateFlashCarousel();
            }, 3000);
        </script>

        @if (session('success'))
            <div class="bg-green-600/20 text-green-600 p-4 rounded-xl mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-600/20 text-red-600 p-4 rounded-xl mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('voucher.select') }}" class="space-y-5 w-full max-w-md">
            @csrf

            <div>
                <label class="block mb-2 text-sm font-semibold text-t1">
                    Select Reseller
                </label>

                <select name="reseller_id"
                        class="w-full bg-bg1 text-t1 p-3 rounded-xl border border-accent">
                    @foreach ($resellers as $reseller)
                        <option value="{{ $reseller->id }}">{{ $reseller->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Data Plans Grid -->
            <div class="grid grid-cols-3 gap-5">
                @foreach ($profiles as $profile)
                    <div
                        class="bg-bg1 rounded-xl p-4 flex flex-col text-center border border-accent min-h-[150px]">
                        <p class="text-t1 font-bold text-base mb-1">
                            {{ $profile->name }}
                        </p>

                        <p class="text-t2 text-sm mb-3">
                            ₦{{ number_format($profile->price, 2) }}
                        </p>

                        <button type="submit"
                                name="profile_id"
                                value="{{ $profile->id }}"
                                class="mt-auto flex justify-between items-center border border-accent rounded-lg px-3 py-2 text-sm text-accent font-medium hover:bg-accent/10 transition">
                            <span>Get</span>
                            <i class="material-icons text-base">chevron_right</i>
                        </button>
                    </div>
                @endforeach

                <input type="hidden" name="pin" id="transaction-pin-final">
            </div>
        </form>

    </div>
</x-layouts.app>



