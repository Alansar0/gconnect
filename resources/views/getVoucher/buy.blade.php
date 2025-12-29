<x-layouts.app>
    <div class="min-h-screen voucher-page text-t1 font-sans py-2 px-4">

        <!-- Back -->
        <div class="w-full flex justify-start mt-6 mb-4">
            <a href="{{ url()->previous() }}" class="text-accent hover:underline flex items-center">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a>
        </div>

        <!-- Header -->
        <div class="text-center mt-6 mb-6">
            <div class="flex justify-center mb-4">
                <img src="https://cdn-icons-png.flaticon.com/512/2331/2331942.png" class="w-20 h-20">
            </div>

            <h1 class="text-2xl font-bold text-accent mb-2">
                Get Your Voucher
            </h1>

            <p class="text-t2 text-sm leading-relaxed max-w-sm mx-auto">
                Purchase a data or Wi-Fi voucher easily and get your access code instantly.
                Stay connected — anytime, anywhere.
            </p>
        </div>

        <!-- Flash Sales -->
        <h2 class="font-semibold text-lg mb-2 text-t1">
            Flash Sales <span class="text-t3 text-sm">ℹ️</span>
        </h2>

        <!-- Carousel -->
        <div class="relative max-w-md mx-auto overflow-hidden rounded-2xl mb-8">
            <div id="flash-track" class="flex transition-transform duration-700 ease-in-out">

                @foreach ([1,2,3] as $i)
                <div class="min-w-full p-3">
                    <div class="bg-bg2 rounded-2xl border border-neutral p-4">

                        <div class="flex justify-between items-center mb-1">
                            <p class="font-semibold text-t1">
                                {{ $i === 1 ? '1GB / 1 DAY' : ($i === 2 ? '2GB / 3 DAYS' : '500MB / 6 HRS') }}
                            </p>

                            <span class="text-xs px-2 py-1 rounded-md
                                {{ $i === 1 ? 'bg-bg3 text-t3'
                                : ($i === 2 ? 'bg-green-600 text-white'
                                : 'bg-accent/40 text-accent') }}">
                                {{ $i === 1 ? 'Sold Out' : ($i === 2 ? 'Active' : 'Hot 🔥') }}
                            </span>
                        </div>

                        <div class="text-t3 text-sm line-through">
                            ₦{{ $i === 1 ? 500 : ($i === 2 ? 1000 : 300) }}
                        </div>

                        <div class="text-accent text-sm">
                            ₦{{ $i === 1 ? 10 : ($i === 2 ? 20 : 5) }} | 98% OFF
                        </div>

                        <div class="h-1 bg-accent mt-3 rounded-full"></div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('voucher.select') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block mb-2 text-sm font-semibold text-t1">
                    Select Reseller
                </label>

                <select name="reseller_id"
                    class="w-full bg-bg3 text-t1 p-3 rounded-xl border border-neutral">
                    @foreach ($resellers as $reseller)
                        <option value="{{ $reseller->id }}">{{ $reseller->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Plans -->
            <div class="grid grid-cols-3 gap-5">
                @foreach ($profiles as $profile)
                    <div class="bg-bg2 border border-neutral rounded-xl p-4 flex flex-col text-center min-h-[150px]">

                        <p class="font-bold text-base mb-1 text-t1">
                            {{ $profile->name }}
                        </p>

                        <p class="text-t2 text-sm mb-3">
                            ₦{{ number_format($profile->price, 2) }}
                        </p>

                        <button type="submit" name="profile_id" value="{{ $profile->id }}"
                            class="mt-auto flex justify-between items-center
                                border border-accent rounded-lg
                                px-3 py-2 text-sm font-medium
                                text-accent hover:bg-accent/10 transition-all">
                            <span>Get</span>
                            <i class="material-icons text-base">chevron_right</i>
                        </button>
                    </div>
                @endforeach
            </div>
        </form>

    </div>
</x-layouts.app>

