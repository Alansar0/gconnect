<x-layouts.admin>
    <div class="min-h-screen bg-bg1 dark:bg-bg1 text-t1 dark:text-t1 flex flex-col items-center font-sans">

        {{-- HEADER --}}
        <header class="fixed top-0 left-0 right-0 z-40 bg-bg2 dark:bg-bg2 border-b border-accent dark:border-accent shadow-[0_0_15px_rgba(88,166,255,0.25)]">
            <div class="text-center py-4 relative">
                <a href="{{ url()->previous() }}"
                class="absolute left-6 top-1/2 -translate-y-1/2 text-accent hover:underline flex items-center">
                    <i class="material-icons mr-1 text-accent">arrow_back</i>
                    Back
                </a>
                <h1 class="text-t1 dark:text-t1 text-xl font-semibold tracking-wide">Change Password</h1>
            </div>

            {{-- Switcher --}}
            <div class="w-[65vw] mx-auto p-1 flex items-center justify-between bg-bg1 dark:bg-bg1 rounded-full border border-accent/50 dark:border-accent/50 shadow-[0_0_20px_rgba(0,255,209,0.4)] mb-3 transition-all">
                <button id="btnSauraro"
                        onclick="window.location.href='{{ route('display.change.password') }}'"
                        class="flex-1 py-2 text-sm font-semibold rounded-full text-t1 dark:text-t1
                        {{ request()->routeIs('display.change.password') ? 'bg-accent/30' : 'hover:bg-accent/20' }} transition-all">
                    Change Password
                </button>

                <button id="btnKaranta"
                        onclick="window.location.href='{{ route('admin.user.changePin') }}'"
                        class="flex-1 py-2 text-sm font-semibold rounded-full text-t1 dark:text-t1
                        {{ request()->routeIs('admin.user.changePin') ? 'bg-accent/30' : 'hover:bg-accent/20' }} transition-all">
                    Change Pin
                </button>
            </div>
        </header>

        {{-- MAIN CONTENT --}}
        <section class="flex-grow w-full px-6 py-4 mt-[110px] max-w-3xl">
            <div id="readerWrapper" class="touch-pan-x">
                <div class="max-w-lg mx-auto bg-bg2 dark:bg-bg2 text-t1 dark:text-t1 p-6 rounded-2xl shadow-[0_0_15px_rgba(0,255,209,0.4)] mt-10 border border-accent/30">

                    <div class="w-full text-center -mt-1 p-4">
                        <span class="text-2xl font-bold text-accent dark:text-accent mb-6">
                            🔐 Update User Password
                        </span>
                    </div>

                    @if(session('success'))
                        <div class="bg-accent text-bg1 p-3 rounded mb-3 font-semibold text-center">
                            {{ session('success') }}
                        </div>
                    @elseif(session('error'))
                        <div class="bg-red-500 text-bg1 p-3 rounded mb-3 font-semibold text-center">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('update.change.Password') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block mb-1 text-sm font-medium text-accent dark:text-accent">Email or Phone Number</label>
                            <input
                                type="text"
                                name="identifier"
                                class="w-full p-2.5 rounded-lg bg-bg3 dark:bg-bg3 border border-accent/40 dark:border-accent/40 focus:ring-2 focus:ring-accent outline-none text-t1 dark:text-t1 placeholder:text-t3 dark:placeholder:text-t3"
                                placeholder="Enter email or phone number"
                                required
                            >
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-accent dark:text-accent">New Password</label>
                            <input
                                type="password"
                                name="new_password"
                                class="w-full p-2.5 rounded-lg bg-bg3 dark:bg-bg3 border border-accent/40 dark:border-accent/40 focus:ring-2 focus:ring-accent outline-none text-t1 dark:text-t1 placeholder:text-t3 dark:placeholder:text-t3"
                                placeholder="Enter new password"
                                required
                            >
                        </div>

                        <button
                            type="submit"
                            class="w-full bg-accent hover:bg-accent/90 text-bg1 dark:text-bg1 font-semibold p-2.5 rounded-xl transition-all duration-300 shadow-[0_0_10px_rgba(0,255,209,0.5)]"
                        >
                            Update Password
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</x-layouts.admin>
