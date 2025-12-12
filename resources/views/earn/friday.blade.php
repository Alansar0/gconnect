<x-layouts.app>

    <div class="min-h-screen bg-[var(--bg-friday-dark)] text-white flex flex-col items-center font-sans relative">

        {{-- Header --}}
        <header class="fixed top-0 left-0 right-0 z-40 bg-[var(--unique-card-bg)] border-b border-[var(--card-border-friday)] shadow-[0_0_15px_var(--shadow-accent)]">
            <div class="text-center py-4 relative">
                <a href="{{ url()->previous() }}" class="absolute left-6 top-1/2 -translate-y-1/2 text-[var(--accent)] hover:underline flex items-center">
                    <i class="material-icons mr-1 text-[var(--accent)]">arrow_back</i>
                    Back
                </a>
                <h1 class="text-white text-xl font-semibold tracking-wide">Friday</h1>
            </div>

            {{-- Switcher --}}
            <div class="w-[65vw] mx-auto p-1 flex items-center justify-between bg-[var(--bg-friday-switcher)] rounded-full border border-[var(--text-accent-friday)] shadow-[0_0_20px_var(--shadow-friday-switcher)] mb-3 transition-all">
                <button id="showQuran" class="flex-1 py-2 text-sm font-semibold rounded-full text-[var(--text-1)] bg-[var(--text-accent-friday)]/30 hover:bg-[var(--text-accent-friday)]/20 transition-all">
                    Surah Al-Kahf
                </button>

                <button id="showSalawat" class="flex-1 py-2 text-sm font-semibold rounded-full text-[var(--text-1)] bg-[var(--text-accent-friday)]/30 hover:bg-[var(--text-accent-friday)]/20 transition-all">
                    Salli 'ala Annabi.
                </button>
            </div>
        </header>

        {{-- Surah List --}}
        <section id="QuranView" class="mt-[100px]">
            <div class="min-h-screen bg-[var(--bg-friday-dark)] text-white flex flex-col items-center py-8 px-4 font-sans">

                {{-- Header --}}
                <header class="w-full max-w-2xl flex items-center justify-between mb-4 border-b border-[var(--card-border-friday)] pb-2 relative">
                    <div class="absolute left-0 text-sm">Page #{{ $page['page'] }}</div>
                    <div class="mx-auto text-lg font-semibold text-[var(--text-accent-friday)] text-center">
                        {{ $page['surah_name'] }}
                    </div>
                </header>

                {{-- Content --}}
                <div class="w-full max-w-2xl bg-[var(--unique-card-bg)] border border-[var(--card-border-friday)] rounded-2xl shadow-lg p-6 text-center text-[var(--text-1)]">
                    {!! $page['content'] !!}
                </div>
            </div>
        </section>

        {{-- Salawat Section --}}
        <section id="SalawatView" class="mt-[200px]">
            <div class="min-h-screen bg-[var(--bg-friday-alt)] flex flex-col items-center justify-between text-white">

                {{-- Header --}}
                <header class="w-full bg-[var(--unique-card-bg)] mt-0 text-center py-4 shadow-md fixed top-25 z-10">
                    <span class="block text-center leading-relaxed">
                        <p dir="rtl" class="text-2xl font-arabic text-[var(--text-1)]">
                            قال رسول الله ﷺ: «أكثروا من الصلاة علي يوم الجمعة وليلة الجمعة، فإن صلاتكم معروضة عليّ»
                        </p>
                        <p class="mt-2 text-xs text-[var(--text-3)]">— Hadith: Ibn Majah 1085</p>
                    </span>
                </header>

                {{-- Main Dua Card --}}
                <main class="flex-grow flex flex-col items-center w-full px-4 py-6 overflow-y-auto space-y-8">
                    @foreach ($adhkar as $index => $dua)
                        <div class="bg-[var(--unique-card-bg)] text-center p-6 rounded-2xl shadow-lg border border-[var(--card-border-friday)] max-w-lg w-full" x-data="{ count: 0, max: {{ $dua['count'] }} }">
                            
                            {{-- Arabic --}}
                            <p class="text-2xl leading-relaxed text-right font-[Scheherazade] mb-6 text-[var(--text-accent-friday)]" dir="rtl">
                                {{ $dua['arabic'] }}
                            </p>

                            {{-- Ajami --}}
                            <p class="text-[var(--accent)] italic mb-4 text-lg text-left" dir="ltr">
                                {{ $dua['ajami'] }}
                            </p>

                            {{-- Hausa --}}
                            <p class="text-[var(--text-3)] text-sm mb-10 text-left" dir="ltr">
                                {{ $dua['hausa'] }}
                            </p>

                            {{-- Counter --}}
                            <button @click="if(count < max) count++; else count = 0" class="mx-auto flex items-center justify-center bg-[var(--text-accent-friday)] text-[var(--text-counter-friday)] w-18 h-18 rounded-full text-xl font-bold shadow-lg hover:bg-[var(--accent)] transition">
                                <span x-text="count + '/' + max"></span>
                            </button>
                        </div>
                    @endforeach
                </main>

                {{-- Footer --}}
                <div class="w-full bg-[var(--unique-card-bg)] text-center py-4 border-t border-[var(--card-border-friday)] mt-4">
                    <span class="block text-center leading-relaxed">
                        <p dir="rtl" class="text-2xl font-arabic text-[var(--text-1)]">
                            فضل الصلاة على النبي ﷺ يوم الجمعة عظيم، فهي سبب لمغفرة الذنوب ورفع الدرجات.
                        </p>
                        <p class="mt-3 text-sm text-[var(--text-accent-friday)] italic">
                            Yin salati ga Annabi (ﷺ) a ranar Jumma’a yana kawo albarka, gafara, da ƙarin lada daga Allah.
                        </p>
                    </span>
                </div>

            </div>
        </section>

        {{-- Alpine.js --}}
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

        {{-- Toggle Quran/Salawat --}}
        <script>
            (function() {
                const qBtn = document.getElementById('showQuran');
                const sBtn = document.getElementById('showSalawat');
                const qView = document.getElementById('QuranView');
                const sView = document.getElementById('SalawatView');

                function show(target) {
                    if (!qView || !sView) return;
                    if (target === 'quran') {
                        qView.style.display = '';
                        sView.style.display = 'none';
                        qBtn.classList.add('bg-[var(--text-accent-friday)]');
                        sBtn.classList.remove('bg-[var(--text-accent-friday)]');
                    } else {
                        qView.style.display = 'none';
                        sView.style.display = '';
                        sBtn.classList.add('bg-[var(--text-accent-friday)]');
                        qBtn.classList.remove('bg-[var(--text-accent-friday)]');
                    }
                }

                show('quran');

                if (qBtn) qBtn.addEventListener('click', e => { e.preventDefault(); show('quran'); });
                if (sBtn) sBtn.addEventListener('click', e => { e.preventDefault(); show('salawat'); });
            })();
        </script>

        {{-- Swipe navigation --}}
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const wrapper = document.getElementById('quranPageWrapper');
                const content = document.getElementById('quranPageContent');
                let startX = 0;
                const currentPage = {{ $page['page'] ?? 1 }};
                const baseUrl = '{{ route('earn.friday', ['id' => 1]) }}';

                wrapper.addEventListener('touchstart', e => startX = e.touches[0].clientX);

                wrapper.addEventListener('touchend', e => {
                    const diff = startX - e.changedTouches[0].clientX;
                    if (Math.abs(diff) > 50) {
                        if (diff > 0) slideTo('next');
                        else if (currentPage > 1) slideTo('prev');
                    }
                });

                function slideTo(direction) {
                    if (direction === 'next') {
                        content.classList.add('-translate-x-full');
                        setTimeout(() => window.location.href = baseUrl.replace('/1', '/' + (currentPage + 1)), 300);
                    } else if (direction === 'prev') {
                        content.classList.add('translate-x-full');
                        setTimeout(() => window.location.href = baseUrl.replace('/1', '/' + (currentPage - 1)), 300);
                    }
                }
            });
        </script>

</x-layouts.app>

