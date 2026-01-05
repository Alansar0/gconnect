<x-layouts.app>

<style>
/* ===============================
   SCROLL MASTERY CORE
=============================== */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
    overscroll-behavior: contain;
    scroll-behavior: smooth;
}

/* Fade hint */
.scroll-fade {
    pointer-events: none;
    transition: opacity .3s ease;
}
.scroll-fade.hidden {
    opacity: 0;
}

/* Logout arming */
.logout-btn {
    opacity: .6;
    filter: grayscale(40%);
    transition: all .25s ease;
}
.logout-btn.armed {
    opacity: 1;
    filter: none;
}
</style>

<div class="bg-bg1 text-t1 font-sans flex flex-col w-full h-[100vh]">

    <div class="relative bg-bg2 rounded-xl shadow-accent
                w-[95%] h-[90vh] p-4 sm:p-6 md:p-8 mt-10 mx-auto
                flex flex-col">

        <!-- Header -->
        <h2 class="text-lg font-semibold mb-4 text-accent ">
            User Details
        </h2>

        <!-- ===============================
             SCROLL CONTAINER (ONE ONLY)
        =============================== -->
        <div id="scroll-area"
             class="relative flex-1 overflow-y-auto no-scrollbar pr-1">

             <!-- Profile Picture -->
        <div class="w-full flex justify-center mb-4">
            <img
                src="{{ Vite::asset('resources/images/logo.png') }}"
                class="w-17 h-17 rounded-full border-2 border-accent"
            >
        </div>
            <!-- Details -->
            <div class="space-y-0">

                @php
                    $rows = [
                        ['Full Name', Auth::user()->full_name],
                        ['Email', Auth::user()->email],
                        ['Phone Number', Auth::user()->phone_number],
                        ['User Type', Auth::user()->role],
                        ['Gconnect Area', Auth::user()->reseller?->name ?? 'N/A'],
                    ];
                @endphp

                @foreach($rows as [$label, $value])
                    <div class="flex justify-between py-4 px-2 border-b border-accent text-sm">
                        <span class="text-t2 font-medium">{{ $label }}</span>
                        <span>{{ $value }}</span>
                    </div>
                @endforeach

                <!-- Theme -->
                <div class="flex justify-between py-4 px-2 border-b border-accent text-sm">
                    <span class="text-t2 font-medium">Switch Theme</span>
                    <button onclick="toggleTheme()"
                            class="px-4 py-2 border border-accent rounded-lg text-accent">
                        Toggle Theme
                    </button>
                </div>

                <!-- Biometric -->
                <div class="flex justify-between py-4 px-2 border-b border-accent text-sm">
                    <span class="text-t2 font-medium">Biometric Unlock</span>
                    <form method="POST" action="{{ route('biometric.toggle') }}">
                        @csrf
                        <button class="px-4 py-2 border border-accent rounded-lg text-accent">
                            {{ Auth::user()->has_biometric ? 'Disable' : 'Enable' }}
                        </button>
                    </form>
                </div>

                <div class="flex justify-between py-4 px-2 border-b border-accent text-sm">
                    <span class="text-t2 font-medium">Date Joined</span>
                    <span>{{ Auth::user()->created_at->format('D M d Y H:i:s') }}</span>
                </div>
            </div>
        </div>
         <!-- ===============================
                     LOGOUT (INSIDE SCROLL)
                =============================== -->
                <div class="pt-8 pb-[calc(2rem+env(safe-area-inset-bottom))] mb-10">

                    <form id="logout-form"
                          action="{{ route('logout') }}"
                          method="POST" class="hidden">
                        @csrf
                    </form>

                    <button id="logout-btn"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="logout-btn w-full bg-[#da3633] text-white rounded-lg py-2 text-sm
                               transition hover:bg-[#f85149]">
                        <i class="fas fa-power-off"></i> Log Out
                    </button>

                </div>

        <!-- Fade Hint -->
        <div id="scroll-fade"
             class="scroll-fade absolute bottom-24 left-0 right-0 h-10
                    bg-gradient-to-t from-bg2 to-transparent">
        </div>

    </div>
</div>

<!-- ===============================
     SCROLL INTELLIGENCE
=============================== -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const area = document.getElementById('scroll-area');
    const fade = document.getElementById('scroll-fade');
    const logout = document.getElementById('logout-btn');

    function onScroll() {
        const atBottom =
            area.scrollTop + area.clientHeight >= area.scrollHeight - 10;

        fade.classList.toggle('hidden', atBottom);
        logout.classList.toggle('armed', atBottom);
    }

    area.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
});
</script>

<!-- ===============================
     THEME (UNCHANGED)
=============================== -->
<script>
(function () {
    const html = document.documentElement;
    const KEY = 'theme';

    function apply(theme) {
        html.setAttribute('data-theme', theme);
        html.classList.toggle('dark', theme === 'dark');
        localStorage.setItem(KEY, theme);
    }

    document.addEventListener('DOMContentLoaded', () => {
        apply(localStorage.getItem(KEY) ??
            (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'));
    });

    window.toggleTheme = () =>
        apply(html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
})();
</script>

</x-layouts.app>


