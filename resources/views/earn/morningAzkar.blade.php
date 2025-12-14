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
                {{ $type === 'morning' ? 'أذكار الصباح' : 'أذكار المساء' }}
            </h1>
            <p class="text-sm opacity-80" style="color: var(--text-1);">
                {{ ucfirst($type) }} Azkar
            </p>
        </header>

        {{-- Main Dua Card --}}
        <main class="flex-grow flex flex-col items-center w-full px-4 py-24 overflow-y-auto space-y-8">
            @foreach ($adhkar as $i => $dua)
                <div x-data="counter({{ $i }}, {{ $dua['count'] }})"
                     class="text-center p-6 rounded-2xl shadow-lg max-w-lg w-full"
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
                    <button @click="inc"
                            class="mx-auto flex items-center justify-center w-16 h-16 rounded-full text-xl font-bold shadow-lg transition"
                            style="background-color: var(--text-accent2); color: var(--bg-main);"
                            onmouseover="this.style.backgroundColor='var(--accent)'"
                            onmouseout="this.style.backgroundColor='var(--text-accent2)'">
                        <span x-text="count + '/' + max"></span>
                    </button>
                </div>
            @endforeach

            {{-- Claim Button --}}
            <button id="claimBtn"
                    class="fixed bottom-6 left-1/2 -translate-x-1/2 px-8 py-3 rounded-xl font-bold"
                    style="background-color: var(--accent); color: var(--bg-main);">
                Claim Reward
            </button>
        </main>

        {{-- Footer --}}
        <footer class="w-full text-center py-4 text-sm"
                style="color: var(--text-3);">
            © 2025 FAHAX | {{ ucfirst($type) }} Azkar
        </footer>
    </div>

    {{-- Reward Modal --}}
    <div id="rewardModal"
         x-data="{ show: false, title: '', message: '', type: 'success' }"
         x-show="show"
         x-transition
         class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50">
        <div :class="`bg-[var(--unique-card-bg)] rounded-2xl p-6 w-10/12 max-w-sm border text-center 
                    ${type === 'success' ? 'border-[var(--accent)]' : type === 'error' ? 'border-red-600' : 'border-yellow-500'}`">
            <h3 class="text-xl font-semibold mb-3" 
                :class="type === 'success' ? 'text-[var(--accent)]' : type === 'error' ? 'text-red-600' : 'text-yellow-500'" 
                x-text="title">🎉 Congratulations!</h3>
            <p class="text-[var(--text-1)]/80 mb-4" x-text="message">You earned ₦50 reward 🎁</p>
            <button @click="show=false"
                    class="bg-[var(--accent)] text-[var(--bg-2)] px-6 py-2 rounded-lg font-semibold hover:scale-105 transition">
                OK
            </button>
        </div>
    </div>

    {{-- Alpine.js --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <script>
        document.addEventListener('alpine:init', () => {

            const TYPE = "{{ $type }}";
            const MINUTES_REQUIRED = 10;

            // Counter component
            window.counter = function(i, max) {
                const key = `azkar_${TYPE}_count_${i}`;
                return {
                    count: Number(localStorage.getItem(key)) || 0,
                    max,
                    inc() {
                        if (this.count < this.max) {
                            this.count++;
                            localStorage.setItem(key, this.count);
                            checkDone();
                        }
                    }
                }
            }

            // Check if all done
            function checkDone() {
                let ok = true;
                document.querySelectorAll('[x-data]').forEach(el => {
                    const d = Alpine.$data(el);
                    if(d.count < d.max) ok = false;
                });
                if(ok) localStorage.setItem(`azkar_${TYPE}_done`, '1');
            }

            // Initialize start time
            if (!localStorage.getItem(`azkar_${TYPE}_start`)) {
                localStorage.setItem(`azkar_${TYPE}_start`, Date.now());
            }

            // Show reward modal
            window.showReward = function(type='success', title='🎉 Congratulations!', message='You earned ₦50 reward 🎁') {
                const modal = document.getElementById('rewardModal');
                const modalData = Alpine.$data(modal);
                modalData.type = type;
                modalData.title = title;
                modalData.message = message;
                modalData.show = true;
            }

            // Claim button
            const claimBtn = document.getElementById('claimBtn');
            claimBtn.addEventListener('click', async () => {
                if(localStorage.getItem(`azkar_${TYPE}_claimed`)) {
                    showReward('error', '❌ Error', 'Reward already claimed');
                    return;
                }

                if(!localStorage.getItem(`azkar_${TYPE}_done`)) {
                    showReward('warning', '⚠️ Warning', 'Finish all Azkar first');
                    return;
                }

                const elapsed = (Date.now() - localStorage.getItem(`azkar_${TYPE}_start`)) / 60000;
                if(elapsed < MINUTES_REQUIRED) {
                    showReward('warning', '⚠️ Warning', `Wait ${Math.ceil(MINUTES_REQUIRED - elapsed)} more minutes`);
                    return;
                }

                try {
                    const res = await fetch('{{ route("azkar.claim") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: new URLSearchParams({ type: TYPE })
                    });

                    const data = await res.json();

                    if(data.status === 'success') {
                        localStorage.setItem(`azkar_${TYPE}_claimed`, '1');
                        showReward('success', '🎉 Congratulations!', `Reward claimed ₦${data.amount}`);
                    } else {
                        showReward('error', '❌ Error', data.message);
                    }

                } catch(e) {
                    showReward('error', '❌ Error', 'Something went wrong, please try again.');
                }
            });

        });
    </script>
</x-layouts.app>