


<x-layouts.app>
    <div class="min-h-screen flex flex-col items-center justify-between"
         style="background-color: var(--bg-main); color: var(--text-1);">

        {{-- Header --}}
        <header class="w-full mt-0 text-center py-4 shadow-md fixed top-0 z-10"
                style="background-color: var(--unique-card-bg); border-bottom: 1px solid var(--border-card);">
            <a href="{{ url()->previous() }}" class="hover:underline flex items-center mt-1 ml-1"
               style="color: var(--accent);">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a>
            <h1 class="text-xl font-semibold tracking-wide" style="color: var(--text-accent2);">
                أذكار الصباح
            </h1>
            <h1 class="text-xl font-semibold tracking-wide" style="color: var(--text-1);">
                Azkar na Safiya
            </h1>
        </header>

        {{-- Main Dua Card --}}
        <main class="flex-grow flex flex-col items-center w-full px-4 py-6 overflow-y-auto space-y-8">
            @foreach ($adhkar as $index => $dua)
                <div class="text-center p-6 rounded-2xl shadow-lg max-w-lg w-full"
                     x-data="{ count: 0, max: {{ $dua['count'] }} }"
                     style="background-color: var(--unique-card-bg); border: 1px solid var(--border-card);">
                    
                    {{-- Arabic --}}
                    <p class="text-2xl leading-relaxed mb-6 text-right font-[Scheherazade]"
                       dir="rtl"
                       style="color: var(--text-accent2);">
                        {{ $dua['arabic'] }}
                    </p>

                    {{-- Ajami --}}
                    <p class="italic mb-4 text-lg text-left"
                       dir="ltr"
                       style="color: var(--accent);">
                        {{ $dua['ajami'] }}
                    </p>

                    {{-- Hausa --}}
                    <p class="text-sm mb-10 text-left"
                       dir="ltr"
                       style="color: var(--text-2);">
                        {{ $dua['hausa'] }}
                    </p>

                    {{-- Counter --}}
                    <button @click="if(count < max) count++; else count = 0"
                            class="mx-auto flex items-center justify-center w-16 h-16 rounded-full text-xl font-bold shadow-lg transition"
                            style="background-color: var(--text-accent2); color: var(--bg-main);"
                            onmouseover="this.style.backgroundColor='var(--accent)'"
                            onmouseout="this.style.backgroundColor='var(--text-accent2)'">
                        <span x-text="count + '/' + max"></span>
                    </button>
                </div>
            @endforeach
        </main>

        {{-- Footer --}}
        <footer class="w-full text-center py-4 text-sm"
                style="color: var(--text-3);">
            © 2025 FAHAX | Morning Adhkar
        </footer>
    </div>

    {{-- Alpine.js for counter logic --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</x-layouts.app>
