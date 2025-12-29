


<x-layouts.app>
<div class="min-h-screen flex flex-col items-center justify-between"
     style="background-color: var(--bg-main); color: var(--text-1);">

    {{-- Header --}}
    <header class="w-full text-center py-4 shadow-md fixed top-0 z-10"
            style="background-color: var(--unique-card-bg); border-bottom: 1px solid var(--border-card);">
        <a href="{{ url()->previous() }}" class="absolute left-2 top-2 flex items-center"
           style="color: var(--accent);">
            <i class="material-icons mr-1">arrow_back</i> Back
        </a>

        <h1 class="text-xl font-semibold tracking-wide" style="color: var(--text-accent2);">
            {{ $type === 'morning' ? 'أذكار الصباح' : 'أذكار المساء' }}
        </h1>
        <p class="text-sm opacity-80">{{ ucfirst($type) }} Azkar</p>
    </header>

    {{-- Main --}}
    <main class="flex-grow w-full px-4 py-24 space-y-8 max-w-xl">

        @foreach ($adhkar as $i => $dua)
            <div x-data="counter({{ $i }}, {{ $dua['count'] }})"
                 class="p-6 rounded-2xl shadow-lg text-center"
                 style="background-color: var(--unique-card-bg); border: 1px solid var(--border-card);">

                <p class="text-2xl mb-6 text-right font-[Scheherazade]"
                   dir="rtl" style="color: var(--text-accent2);">
                    {{ $dua['arabic'] }}
                </p>

                <p class="italic mb-4 text-left" style="color: var(--accent);">
                    {{ $dua['ajami'] }}
                </p>

                <p class="text-sm mb-10 text-left" style="color: var(--text-2);">
                    {{ $dua['hausa'] }}
                </p>

                <button @click="inc"
                        class="mx-auto w-16 h-16 rounded-full text-xl font-bold shadow-lg transition"
                        style="background-color: var(--text-accent2); color: var(--bg-main);">
                    <span x-text="count + '/' + max"></span>
                </button>
            </div>
        @endforeach

        <button id="claimBtn"
                class="fixed bottom-6 left-1/2 -translate-x-1/2 px-8 py-3 rounded-xl font-bold"
                style="background-color: var(--accent); color: var(--bg-main);">
            Claim Reward
        </button>
    </main>

    <footer class="py-4 text-sm opacity-60">
        © 2025 FAHAX | {{ ucfirst($type) }} Azkar
    </footer>
</div>

{{-- Reward Modal --}}
<div id="rewardModal"
     x-data="{ show:false, title:'', message:'', type:'success' }"
     x-show="show"
     x-transition
     class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
    <div class="bg-[var(--unique-card-bg)] p-6 rounded-2xl w-10/12 max-w-sm border text-center"
         :class="type==='success'?'border-[var(--accent)]':'border-red-600'">
        <h3 class="text-xl font-semibold mb-3" x-text="title"></h3>
        <p class="opacity-80 mb-4" x-text="message"></p>
        <button @click="show=false"
                class="bg-[var(--accent)] px-6 py-2 rounded-lg font-semibold">
            OK
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
document.addEventListener('alpine:init', () => {

    const TYPE = "{{ $type }}";
    const TODAY = new Date().toISOString().slice(0,10); // YYYY-MM-DD
    const DAY_KEY = `azkar_${TYPE}_day`;
    const MINUTES_REQUIRED = 10;

    /* ===== RESET AT 12:00 AM ===== */
    if (localStorage.getItem(DAY_KEY) !== TODAY) {
        Object.keys(localStorage).forEach(k => {
            if (k.startsWith(`azkar_${TYPE}_`)) {
                localStorage.removeItem(k);
            }
        });
        localStorage.setItem(DAY_KEY, TODAY);
    }

    /* ===== COUNTER ===== */
    window.counter = (i, max) => {
        const key = `azkar_${TYPE}_${TODAY}_count_${i}`;
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
    };

    function checkDone() {
        let done = true;
        document.querySelectorAll('[x-data^="counter"]').forEach(el => {
            const d = Alpine.$data(el);
            if (d.count < d.max) done = false;
        });
        if (done) localStorage.setItem(`azkar_${TYPE}_${TODAY}_done`, '1');
    }

    if (!localStorage.getItem(`azkar_${TYPE}_${TODAY}_start`)) {
        localStorage.setItem(`azkar_${TYPE}_${TODAY}_start`, Date.now());
    }

    window.showReward = (type, title, message) => {
        Object.assign(Alpine.$data(rewardModal), {
            show:true, type, title, message
        });
    };

    claimBtn.onclick = async () => {

        if (localStorage.getItem(`azkar_${TYPE}_${TODAY}_claimed`)) {
            return showReward('error','Error','Already claimed today');
        }

        if (!localStorage.getItem(`azkar_${TYPE}_${TODAY}_done`)) {
            return showReward('error','Incomplete','Finish all Azkar first');
        }

        const elapsed =
            (Date.now() - localStorage.getItem(`azkar_${TYPE}_${TODAY}_start`)) / 60000;

        if (elapsed < MINUTES_REQUIRED) {
            return showReward(
                'error',
                'Wait',
                `Wait ${Math.ceil(MINUTES_REQUIRED - elapsed)} more minutes`
            );
        }

        const res = await fetch('{{ route("azkar.claim") }}', {
            method:'POST',
            headers:{
                'X-CSRF-TOKEN':'{{ csrf_token() }}',
                'Accept':'application/json'
            },
            body:new URLSearchParams({ type: TYPE })
        });

        const data = await res.json();

        if (data.status === 'success') {
            localStorage.setItem(`azkar_${TYPE}_${TODAY}_claimed`, '1');
            showReward('success','Success',`₦${data.amount} earned`);
        } else {
            showReward('error','Error',data.message);
        }
    };
});
</script>
</x-layouts.app>
