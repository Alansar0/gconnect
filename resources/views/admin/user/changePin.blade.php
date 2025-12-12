
<x-layouts.admin>
<div class="min-h-screen bg-[#0B141A] text-white flex flex-col items-center font-sans">

    {{-- HEADER --}}
    <header
        class="fixed top-0 left-0 right-0 z-40 bg-[#182430] border-b border-[#233044] shadow-[0_0_15px_rgba(88,166,255,0.25)]">
        <div class="text-center py-4 relative">
            <a href="{{ url()->previous() }}"
                class="absolute left-6 top-1/2 -translate-y-1/2 text-[#58a6ff] hover:underline flex items-center">
                <i class="material-icons mr-1 text-[#58a6ff]">arrow_back</i>
                Back
            </a>
            <h1 class="text-white text-xl font-semibold tracking-wide">Change Pin</h1>
        </div>

        {{-- Switcher --}}
        <div
            class="w-[65vw] mx-auto p-1 flex items-center justify-between bg-[#0C141C] rounded-full border border-[#00FFD1]/50 shadow-[0_0_20px_rgba(0,255,209,0.4)] mb-3 transition-all">
            <button id="btnSauraro"
                onclick="window.location.href='{{ route('display.change.password') }}'"
                class="flex-1 py-2 text-sm font-semibold rounded-full text-[#f0f6fc]
                {{ request()->routeIs('display.change.password') ? 'bg-[#00FFD1]/30' : 'hover:bg-[#00FFD1]/20' }} transition-all">
                Change Password
            </button>

            <button id="btnKaranta"
                onclick="window.location.href='{{ route('admin.user.changePin') }}'"
                class="flex-1 py-2 text-sm font-semibold rounded-full text-[#f0f6fc]
                {{ request()->routeIs('admin.user.changePin') ? 'bg-[#00FFD1]/30' : 'hover:bg-[#00FFD1]/20' }} transition-all">
                Change Pin
            </button>
        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <section class="flex-grow w-full px-6 py-4 mt-[110px] max-w-3xl">
        <div id="readerWrapper" class="touch-pan-x">
             <div class="max-w-lg mx-auto bg-[#101E2B] text-white p-6 rounded-2xl shadow-[0_0_15px_rgba(0,255,209,0.4)] mt-10 border border-[#00FFD1]/30">

          
        <div class="w-full text-center -mt-1 p-4">
            <span class="text-2xl font-bold text-[#58a6ff] mb-6">
                🔐 Update User Pin
            </span>
        </div>

        
        @if(session('success'))
            <div class="bg-[#00FFD1] text-[#101E2B] p-3 rounded mb-3 font-semibold text-center">
                {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div class="bg-red-500 text-white p-3 rounded mb-3 font-semibold text-center">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('update.changePin') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block mb-1 text-sm font-medium text-[#00FFD1]">Email or Phone Number</label>
                <input
                    type="text"
                    name="identifier"
                    class="w-full p-2.5 rounded-lg bg-[#0C1621] border border-[#00FFD1]/40 focus:ring-2 focus:ring-[#00FFD1] outline-none text-white placeholder-gray-400"
                    placeholder="Enter email or phone number"
                    required
                >
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-[#00FFD1]">New Pin Code</label>
                <input
                    type="password"
                    name="new_pin"
                    class="w-full p-2.5 rounded-lg bg-[#0C1621] border border-[#00FFD1]/40 focus:ring-2 focus:ring-[#00FFD1] outline-none text-white placeholder-gray-400"
                    placeholder="Enter new password"
                    required
                >
            </div>

            <button
                type="submit"
                class="w-full bg-[#00FFD1] hover:bg-[#00e0b8] text-[#101E2B] font-semibold p-2.5 rounded-xl transition-all duration-300 shadow-[0_0_10px_rgba(0,255,209,0.5)]"
            >
                Update Pin Code
            </button>
        </form>
    </div>
        </div>
    </section>
</div>
</x-layouts.admin>

