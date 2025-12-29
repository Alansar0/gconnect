    <x-layouts.app>
    <div class="min-h-screen bg-[var(--bg-friday-dark)] text-white flex flex-col items-center font-sans relative">

        {{-- Header --}}
        <header class="fixed top-0 left-0 right-0 z-40 bg-[var(--unique-card-bg)] border-b border-[var(--card-border-friday)] shadow-[0_0_15px_var(--shadow-accent)]">
            <div class="text-center py-4 relative">
                <a href="{{ url()->previous() }}" class="absolute left-6 top-1/2 -translate-y-1/2 text-[var(--accent)] hover:underline flex items-center">
                    <i class="material-icons mr-1">arrow_back</i>
                    Back
                </a>
                <h1 class="text-xl font-semibold tracking-wide">Friday</h1>
            </div>

            {{-- Switcher --}}
            <div class="w-[65vw] mx-auto p-1 flex justify-between bg-[var(--bg-friday-switcher)] rounded-full border border-[var(--text-accent-friday)] mb-3">
                <button id="showQuran" class="flex-1 py-2 text-sm font-semibold rounded-full bg-[var(--text-accent-friday)]/30">
                    Surah Al-Kahf
                </button>
                <button id="showSalawat" class="flex-1 py-2 text-sm font-semibold rounded-full bg-[var(--text-accent-friday)]/30">
                    Salli ‘ala Annabi
                </button>
            </div>
        </header>

        {{-- Reward Modal --}}
        <div id="rewardModal"
            x-data="{ show:false, title:'', message:'', type:'success' }"
            x-show="show"
            x-transition
            class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
            <div :class="`bg-[var(--unique-card-bg)] rounded-2xl p-6 w-10/12 max-w-sm border text-center ${type==='success'?'border-[var(--accent)]':'border-red-600'}`">
                <h3 class="text-xl font-semibold mb-3" x-text="title"></h3>
                <p class="opacity-80 mb-4" x-text="message"></p>
                <button @click="show=false" class="bg-[var(--accent)] px-6 py-2 rounded-lg font-semibold">OK</button>
            </div>
        </div>

        {{-- Surah with Swiper --}}
        <section id="QuranView" class="mt-[100px] w-full">
            <div class="max-w-2xl mx-auto p-6">

                <header class="text-center mb-4 border-b pb-2">
                    <div class="text-sm">Page #{{ $page['page'] }}</div>
                    <div class="text-lg font-semibold text-[var(--text-accent-friday)]">
                        {{ $page['surah_name'] }}
                    </div>
                </header>

                <!-- Swiper Container -->
                <div class="swiper rtl" dir="rtl">
                    <div class="swiper-wrapper">
                        @foreach($pages as $p)
                            <div class="swiper-slide bg-[var(--unique-card-bg)] p-6 rounded-2xl text-right">
                                @if($p['page'] === 1)
                                    <div class="basmala text-center mb-4 text-lg">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ</div>
                                @endif
                                {!! $p['content'] !!}
                            </div>
                        @endforeach
                    </div>
                </div>

                <button id="claimSurahBtn" class="mt-6 w-full py-3 rounded-xl font-bold bg-[var(--accent)]">
                    Claim Surah Reward
                </button>
            </div>
        </section>

        {{-- Salawat --}}
        <section id="SalawatView" class="hidden w-full mt-[200px]">
            <div class="max-w-2xl mx-auto space-y-8 p-6">
                @foreach ($adhkar as $i => $dua)
                    <div x-data="counter({{ $i }}, {{ $dua['count'] }})"
                        class="bg-[var(--unique-card-bg)] p-6 rounded-2xl text-center">
                        <p class="text-2xl text-right mb-4" dir="rtl">{{ $dua['arabic'] }}</p>
                        <p class="italic mb-2">{{ $dua['ajami'] }}</p>
                        <p class="text-sm opacity-70 mb-6">{{ $dua['hausa'] }}</p>

                        <button @click="inc"
                                class="mx-auto w-20 h-20 rounded-full bg-[var(--text-accent-friday)] text-xl font-bold">
                            <span x-text="count + '/' + max"></span>
                        </button>
                    </div>
                @endforeach

                <button id="claimSalawatBtn" class="w-full py-3 rounded-xl font-bold bg-[var(--accent)]">
                    Claim Salawat Reward
                </button>
            </div>
        </section>

        {{-- Logic --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>

        document.addEventListener('alpine:init', () => {
                const isFriday = new  var().getDay() === 5;
                if (!isFriday) {
                    document.getElementById('QuranView').innerHTML =
                        '<div class="text-center mt-40 text-yellow-400 font-semibold">Available on Fridays only.</div>';
                    document.getElementById('SalawatView').innerHTML =
                        '<div class="text-center mt-40 text-yellow-400 font-semibold">Available on Fridays only.</div>';
                    return;
                }


                const TODAY = new Date().toISOString().slice(0,10);
                const SURAH_MINUTES = 1;
                const SALAWAT_MINUTES = 1;
                // ===== Swiper Initialization =====
                const swiper = new Swiper('.swiper', {
                    direction: 'horizontal',
                    rtl: true,
                    slidesPerView: 1,
                    spaceBetween: 20,
                    mousewheel: true,
                    keyboard: true,
                    on: {
                        reachEnd: function () {
                            localStorage.setItem('quran_done', '1');
                        }
                    }
                });

                
                window.counter = (i, max) => {
            const key = `salawat_${TODAY}_count_${i}`;
            const doneKey = `salawat_${TODAY}_type_${i}_done`;

            return {
                count: Number(localStorage.getItem(key)) || 0,
                max,
                inc() {
                    if (this.count < this.max) {
                        this.count++;
                        localStorage.setItem(key, this.count);
                    }

                    if (this.count === this.max) {
                        localStorage.setItem(doneKey, '1');
                    }
                }
            }
        };


                function checkSalawatDone() {
                    let done = true;
                    document.querySelectorAll('[x-data^="counter"]').forEach(el => {
                        const d = Alpine.$data(el);
                        if (d.count < d.max) done = false;
                    });
                    if (done) localStorage.setItem(`salawat_${TODAY}_done`, '1');
                }


                // ===== View Switch =====
                showQuran.onclick = () => {
                    QuranView.classList.remove('hidden');
                    SalawatView.classList.add('hidden');
                };
                showSalawat.onclick = () => {
                    SalawatView.classList.remove('hidden');
                    QuranView.classList.add('hidden');
                };

                // ===== Reward Modal =====
                window.showReward = (type, title, message) => {
                    const m = Alpine.$data(rewardModal);
                    Object.assign(m, { show:true, type, title, message });
                };

                
                function salawatDoneCount() {
                    let count = 0;

                    Object.keys(localStorage).forEach(key => {
                        if (key.startsWith(`salawat_${TODAY}_type_`) && key.endsWith('_done')) {
                            count++;
                        }
                    });

            return count;
        }

                async function claim(type) {

            if (type === 'surah') {

                if (localStorage.getItem('friday_surah_claimed')) {
                    return showReward('error','Error','Surah reward already claimed.');
                }

                if (!localStorage.getItem('quran_done')) {
                    return showReward('error','Incomplete','Finish Surah Al-Kahf first.');
                }

                if ((Date.now() - localStorage.getItem('surah_start')) / 60000 < SURAH_MINUTES) {
                    return showReward('error','Wait','15 minutes not completed.');
                }

            }

            if (type === 'salawat') {

                if (localStorage.getItem('friday_salawat_claimed')) {
                    return showReward('error','Error','Salawat reward already claimed.');
                }

                const completed = salawatDoneCount();

                if (completed === 0) {
                    return showReward('error','Incomplete','Complete at least one Salawat.');
                }

                if ((Date.now() - localStorage.getItem('salawat_start')) / 60000 < SALAWAT_MINUTES) {
                    return showReward('error','Wait','5 minutes not completed.');
                }
            }
            
            const res = await fetch('{{ route("azkar.claim") }}', {
                method:'POST',
                headers:{
                    'X-CSRF-TOKEN':'{{ csrf_token() }}',
                    'Accept':'application/json'
                },
                body: new URLSearchParams({
                    type,
                    multiplier: type === 'salawat' ? salawatDoneCount() : 1
                })
            });

            const data = await res.json();

            if (data.status === 'success') {
                localStorage.setItem(
                    type === 'surah' ? 'friday_surah_claimed' : 'friday_salawat_claimed',
                    '1'
                );

                showReward(
                    'success',
                    'Success',
                    `₦${data.amount} earned`
                );
            }
        }


                claimSurahBtn.onclick = () => claim('surah');
                claimSalawatBtn.onclick = () => claim('salawat');

                if (!localStorage.getItem('surah_start')) localStorage.setItem('surah_start', Date.now());
                if (!localStorage.getItem('salawat_start')) localStorage.setItem('salawat_start', Date.now());

            });

    </script>


        <style>
            .swiper-slide {
                min-height: 60vh; /* Adjust height per page if needed */
            }
            .basmala {
                text-align: center;
                font-size: 1.25rem;
                margin-bottom: 1rem;
            }
        </style>
    </div>
    </x-layouts.app>






