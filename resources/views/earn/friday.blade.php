

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
            <button id="showQuran" class="flex-1 py-2 text-sm font-semibold rounded-full text-[var(--text-1)] bg-[var(--text-accent-friday)]/30 hover:bg-[var(--text-accent-friday)]/20 transition-all">Surah Al-Kahf</button>
            <button id="showSalawat" class="flex-1 py-2 text-sm font-semibold rounded-full text-[var(--text-1)] bg-[var(--text-accent-friday)]/30 hover:bg-[var(--text-accent-friday)]/20 transition-all">Salli 'ala Annabi</button>
        </div>
    </header>

    {{-- Reward Modal --}}
    <div id="rewardModal"
         x-data="{ show:false, title:'', message:'', type:'success' }"
         x-show="show"
         x-transition
         class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50">
        <div :class="`bg-[var(--unique-card-bg)] rounded-2xl p-6 w-10/12 max-w-sm border text-center ${type==='success'?'border-[var(--accent)]': type==='error'?'border-red-600':'border-yellow-500'}`">
            <h3 class="text-xl font-semibold mb-3" :class="type==='success'?'text-[var(--accent)]': type==='error'?'text-red-600':'text-yellow-500'" x-text="title">🎉 Congratulations!</h3>
            <p class="text-[var(--text-1)]/80 mb-4" x-text="message">You earned ₦50 reward 🎁</p>
            <button @click="show=false" class="bg-[var(--accent)] text-[var(--bg-2)] px-6 py-2 rounded-lg font-semibold hover:scale-105 transition">OK</button>
        </div>
    </div>

    {{-- Surah List --}}
    <section id="QuranView" class="mt-[100px] w-full">
        <div class="min-h-screen bg-[var(--bg-friday-dark)] text-white flex flex-col items-center py-8 px-4 font-sans">
            <header class="w-full max-w-2xl flex items-center justify-between mb-4 border-b border-[var(--card-border-friday)] pb-2 relative">
                <div class="absolute left-0 text-sm">Page #{{ $page['page'] }}</div>
                <div class="mx-auto text-lg font-semibold text-[var(--text-accent-friday)] text-center">{{ $page['surah_name'] }}</div>
            </header>

            <div id="quranPageContent" class="w-full max-w-2xl bg-[var(--unique-card-bg)] border border-[var(--card-border-friday)] rounded-2xl shadow-lg p-6 text-center text-[var(--text-1)]">
                {!! $page['content'] !!}
            </div>

            <button id="claimSurahBtn" class="mt-6 px-6 py-3 rounded-xl font-bold bg-[var(--accent)] text-[var(--bg-main)]">
                Claim Surah Reward
            </button>
        </div>
    </section>

    {{-- Salawat Section --}}
    <section id="SalawatView" class="mt-[200px] w-full">
        <div class="min-h-screen bg-[var(--bg-friday-alt)] flex flex-col items-center justify-between text-white">
            <main class="flex-grow flex flex-col items-center w-full px-4 py-6 overflow-y-auto space-y-8">
                @foreach ($adhkar as $i => $dua)
                    <div x-data="counter({{ $i }}, {{ $dua['count'] }})" class="bg-[var(--unique-card-bg)] text-center p-6 rounded-2xl shadow-lg border border-[var(--card-border-friday)] max-w-lg w-full">
                        <p class="text-2xl leading-relaxed text-right font-[Scheherazade] mb-6 text-[var(--text-accent-friday)]" dir="rtl">{{ $dua['arabic'] }}</p>
                        <p class="text-[var(--accent)] italic mb-4 text-lg text-left" dir="ltr">{{ $dua['ajami'] }}</p>
                        <p class="text-[var(--text-3)] text-sm mb-10 text-left" dir="ltr">{{ $dua['hausa'] }}</p>

                        <button @click="inc" class="mx-auto flex items-center justify-center bg-[var(--text-accent-friday)] text-[var(--text-counter-friday)] w-18 h-18 rounded-full text-xl font-bold shadow-lg hover:bg-[var(--accent)] transition">
                            <span x-text="count + '/' + max"></span>
                        </button>
                    </div>
                @endforeach

                <button id="claimSalawatBtn" class="mt-6 px-6 py-3 rounded-xl font-bold bg-[var(--accent)] text-[var(--bg-main)]">
                    Claim Salawat Reward
                </button>
            </main>
        </div>
    </section>

    {{-- Alpine.js & Claim Logic --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        document.addEventListener('alpine:init', () => {
            const SURAH_MINUTES = 15;
            const SALAWAT_MINUTES = 1;

            // Counter for Salawat
            window.counter = function(i, max) {
                const key = `salawat_count_${i}`;
                return {
                    count: Number(localStorage.getItem(key)) || 0,
                    max,
                    inc() {
                        if(this.count < this.max) this.count++;
                        localStorage.setItem(key, this.count);
                        checkSalawatDone();
                    }
                }
            }

            function checkSalawatDone() {
                let done = true;
                document.querySelectorAll('[x-data]').forEach(el=>{
                    const d = Alpine.$data(el);
                    if(d.count < d.max) done = false;
                });
                if(done) localStorage.setItem('salawat_done', '1');
            }

            // Toggle views
            (function(){
                const qBtn = document.getElementById('showQuran');
                const sBtn = document.getElementById('showSalawat');
                const qView = document.getElementById('QuranView');
                const sView = document.getElementById('SalawatView');

                function show(target){
                    if(!qView || !sView) return;
                    if(target==='quran'){
                        qView.style.display='';
                        sView.style.display='none';
                        qBtn.classList.add('bg-[var(--text-accent-friday)]/30');
                        sBtn.classList.remove('bg-[var(--text-accent-friday)]/30');
                    } else {
                        qView.style.display='none';
                        sView.style.display='';
                        sBtn.classList.add('bg-[var(--text-accent-friday)]/30');
                        qBtn.classList.remove('bg-[var(--text-accent-friday)]/30');
                    }
                }

                show('quran');
                qBtn.addEventListener('click', e=>{ e.preventDefault(); show('quran'); });
                sBtn.addEventListener('click', e=>{ e.preventDefault(); show('salawat'); });
            })();

            // Reward modal
            window.showReward = function(type='success', title='🎉 Congratulations!', message='You earned ₦50 reward 🎁') {
                const modal = document.getElementById('rewardModal');
                const modalData = Alpine.$data(modal);
                modalData.type = type;
                modalData.title = title;
                modalData.message = message;
                modalData.show = true;
            }

            // Claim functions
            async function claimReward(type){
                if(localStorage.getItem('friday_claimed')){
                    showReward('error','Error','Reward already claimed!');
                    return;
                }

                let done=false, requiredMinutes=0;
                if(type==='surah'){
                    done = localStorage.getItem('quran_done'); // set after last page
                    requiredMinutes = SURAH_MINUTES;
                } else {
                    done = localStorage.getItem('salawat_done');
                    requiredMinutes = SALAWAT_MINUTES;
                }

                if(!done){
                    showReward('warning','Incomplete','Finish reading/reciting first!');
                    return;
                }

                const startTime = localStorage.getItem(type+'_start');
                const elapsed = (Date.now() - startTime)/60000;
                if(elapsed < requiredMinutes){
                    showReward('warning','Wait',`Wait ${Math.ceil(requiredMinutes - elapsed)} more minutes.`);
                    return;
                }

                try {
                    const res = await fetch('{{ route("azkar.claim") }}',{
                        method:'POST',
                        headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
                        body: new URLSearchParams({type:type})
                    });
                    const data = await res.json();
                    if(data.status==='success'){
                        localStorage.setItem('friday_claimed','1');
                        showReward('success','Congratulations!',`You earned ₦${data.amount} reward!`);
                    } else {
                        showReward('error','Error',data.message);
                    }
                } catch(e){
                    showReward('error','Error','Something went wrong.');
                }
            }

            document.getElementById('claimSurahBtn').onclick = ()=>claimReward('surah');
            document.getElementById('claimSalawatBtn').onclick = ()=>claimReward('salawat');

            // Initialize start times
            if(!localStorage.getItem('surah_start')) localStorage.setItem('surah_start', Date.now());
            if(!localStorage.getItem('salawat_start')) localStorage.setItem('salawat_start', Date.now());
        });
    </script>
</div>
</x-layouts.app>


