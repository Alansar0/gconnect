<x-layouts.app>
    <div class="min-h-screen bg-[var(--bg-3)] text-[var(--text-1)] flex flex-col items-center font-sans relative">

        {{-- Header --}}
        <header class="fixed top-0 left-0 right-0 z-40 bg-[var(--unique-card-bg)] border-b border-[var(--unique-card-border)] shadow-[0_0_15px_var(--shadow-accent)]">
            <div class="text-center py-4 relative">
                <a href="{{ url()->previous() }}"
                   class="absolute left-6 top-1/2 -translate-y-1/2 text-[var(--accent)] hover:underline flex items-center">
                    <i class="material-icons mr-1 text-[var(--accent)]">arrow_back</i>
                    Back
                </a>
                <h1 class="text-[var(--text-1)] text-xl font-semibold tracking-wide">
                    {{ $displayName }}
                </h1>
            </div>

            {{-- Switcher --}}
            <div class="w-[65vw] mx-auto p-1 flex items-center justify-between bg-[var(--bg-2)] rounded-full border border-[var(--accent)]/50 shadow-[0_0_20px_rgba(0,255,209,0.4)] mb-3 transition-all">
                <button onclick="window.location.href='{{ route('makaranta.darasi') }}'"
                        class="flex-1 py-2 text-sm font-semibold rounded-full text-[var(--text-1)]
                        {{ request()->routeIs('makaranta.darasi') ? 'bg-[var(--accent)]/30' : 'hover:bg-[var(--accent)]/20' }} transition-all">
                    🎧 Sauraro
                </button>

                @php session(['current_course' => $course ?? 'sharrindajjal']); @endphp

                <button onclick="window.location.href='{{ route('makaranta.karanta', ['pageId' => 1]) }}'"
                        class="flex-1 py-2 text-sm font-semibold rounded-full text-[var(--text-1)]
                        {{ Route::currentRouteName() === 'makaranta.karanta' ? 'bg-[var(--accent)]/30' : 'hover:bg-[var(--accent)]/20' }} transition-all">
                    📖 Karanta
                </button>
            </div>
        </header>

        {{-- 🔊 SAURARO SECTION --}}
        <section id="sauraroView"
                 class="flex-grow w-full px-6 py-4 overflow-y-auto mt-[110px] max-w-3xl transition-all duration-500">

            {{-- Course Banner --}}
            @php
                $courses = [
                    'kurakurai100' => 'kurakurai100.png',
                    'sharrindajjal' => 'sharrindajjal.png',
                    'auretayya' => 'auretayya.png',
                    'kimiyya' => 'kimiyya.png',
                    'fatawowi30' => 'fatawowi30.png',
                    'hukunci' => 'hukunci.png',
                ];

                $courseKey   = $course ?? 'sharrindajjal';
                $courseImage = $courses[$courseKey] ?? 'sharrindajjal.png';
            @endphp

            <div class="flex justify-center mb-4">
                <div class="relative w-full h-48 md:h-56 rounded-2xl overflow-hidden
                            bg-[var(--bg-2)] border border-[var(--accent)]/50
                            shadow-[0_0_15px_var(--shadow-accent)]
                            hover:shadow-[0_0_25px_var(--shadow-accent)] transition">

                    <img
                        src="{{ Vite::asset('resources/images/courses/' . $courseImage) }}"
                        alt="{{ $displayName }}"
                        class="absolute inset-0 w-full h-full object-cover"
                    >

                    {{-- Optional overlay for readability --}}
                    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-[var(--bg-3)]/40"></div>
                </div>
            </div>

            {{-- Audio Files List --}}
            @if (empty($files))
                <p class="text-center text-[var(--text-2)] mt-8">
                    No audio files found for this course.
                </p>
            @else
                <ul class="space-y-3">
                    @foreach ($files as $index => $file)
                        <a href="{{ route('makaranta.sauraro', ['course' => $courseKey, 'file' => $file]) }}"
                           class="bg-gradient-to-r from-[var(--bg-2)] to-[var(--unique-card-bg)]
                                  flex items-center justify-between rounded-2xl p-3 shadow-lg
                                  border border-[var(--accent)]/20">

                            <div class="flex items-center gap-3">
                                <button class="playButton w-10 h-10 rounded-full bg-[var(--accent)]
                                               flex items-center justify-center
                                               shadow-[0_0_12px_var(--shadow-accent)]
                                               hover:scale-105 transition">
                                    <i class="fas fa-play text-[var(--bg-3)]"></i>
                                </button>

                                <div class="text-left">
                                    <p class="text-[var(--text-1)] font-semibold text-base">
                                        Karatu {{ $index + 1 }}
                                    </p>
                                    <p class="text-[var(--accent)] text-sm">
                                        {{ $displayName }}
                                    </p>
                                </div>
                            </div>

                            <i class="material-icons text-[var(--accent)] !text-[40px]">
                                chevron_right
                            </i>
                        </a>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
</x-layouts.app>
