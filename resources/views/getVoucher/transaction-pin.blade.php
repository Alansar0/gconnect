<x-layouts.app>
    <div class="bg-[#0B1120] min-h-screen flex flex-col items-center justify-center p-6">
        <div class=" w-full flex justify-start mt-6 mb-4">
            <a href="{{ url()->previous() }}" class="text-[#58a6ff] hover:underline flex items-center">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a>
        </div>
        <!-- Payment Card -->
        <div class="bg-[#121A2F] rounded-2xl p-4 w-88 shadow-xl border border-[#1C2750] text-white">
            <h2 class="text-xl text-center font-bold mb-2 text-[#58a6ff]">Confirm Payment</h2>
            <p class="text-sm text-gray-400 mb-6">Please confirm your details before proceeding.</p>

            <!-- Details Box -->
            <div class="bg-[#3a75c4] rounded-xl p-4 mb-6">
                <div class="flex justify-between mb-1 text-sm text-[#121A2F]">
                    <span>Product</span>
                    <span class="font-medium text-[#121A2F]">MTN Data</span>
                </div>
                <div class="flex justify-between mb-1 text-sm text-[#121A2F]">
                    <span>Discount</span>
                    <span class="font-medium text-[#121A2F]">—</span>
                </div>
                <div class="flex justify-between mb-1 text-sm text-[#121A2F]">
                    <span>Number</span>
                    <span class="font-medium text-[#121A2F]">07044834946</span>
                </div>
                <div class="flex justify-between mt-3 text-[#121A2F] font-semibold">
                    <span>Total</span>
                    <span class="text-[#121A2F]">₦450</span>
                </div>
            </div>

            <!-- PIN Inputs -->
            <div class="flex justify-around mb-6" id="pin-boxes">
                <input type="password" maxlength="1" class="pin-field w-10 h-10 rounded-lg bg-[#0B1120] border border-[#1E2A55] text-center text-white focus:border-[#58a6ff] outline-none transition" />
                <input type="password" maxlength="1" class="pin-field w-10 h-10 rounded-lg bg-[#0B1120] border border-[#1E2A55] text-center text-white focus:border-[#58a6ff] outline-none transition" />
                <input type="password" maxlength="1" class="pin-field w-10 h-10 rounded-lg bg-[#0B1120] border border-[#1E2A55] text-center text-white focus:border-[#58a6ff] outline-none transition" />
                <input type="password" maxlength="1" class="pin-field w-10 h-10 rounded-lg bg-[#0B1120] border border-[#1E2A55] text-center text-white focus:border-[#58a6ff] outline-none transition" />
            </div>

            <!-- Keypad -->
            <div class="grid grid-cols-3 gap-3">
                @foreach(range(1,9) as $n)
                    <button data-value="{{ $n }}" class="bg-[#0B1120] rounded-xl py-3 text-xl font-semibold hover:bg-[#121A2F] hover:text-white transition">{{ $n }}</button>
                @endforeach

                <button id="clear-btn" class="bg-[#0B1120] rounded-xl py-3 text-lg text-[#58a6ff] font-semibold hover:bg-[#1E2A55] transition">Clear</button>
                <button data-value="0" class="bg-[#0B1120] rounded-xl py-3 text-xl font-semibold hover:bg-[#121A2F] hover:text-white transition">0</button>
                <button id="backspace-btn" class="bg-[#0B1120] rounded-xl py-3 text-xl text-[#58a6ff] font-semibold hover:bg-[#1E2A55] transition">⌫</button>
            </div>

            <div class="flex justify-center mt-6">
                <div class="w-12 h-12 rounded-full border border-[#58a6ff] flex items-center justify-center hover:bg-[#0F1B33] transition" id="webauthn-btn">
                    <i class="material-icons text-[#58a6ff]">fingerprint</i>
                </div>
            </div>
        </div>
    </div>
    <!-- Add this form inside your view -->
<form id="purchase-form" method="POST" action="{{ route('getVoucher.store') }}">
    @csrf
    <input type="hidden" name="reseller_id" id="purchase-reseller-id">
    <input type="hidden" name="profile_id" id="purchase-profile-id">
    <input type="hidden" name="pin" id="purchase-pin">
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const MAX = 4;
    let pin = '';
    const pinFields = document.querySelectorAll('.pin-field');
    const keypad = document.querySelectorAll('[data-value]');
    const clearBtn = document.getElementById('clear-btn');
    const backspaceBtn = document.getElementById('backspace-btn');
    const webauthnBtn = document.getElementById('webauthn-btn');

    function renderFields() {
        pinFields.forEach((f, i) => {
            f.value = pin[i] ? '*' : '';
        });
    }

    function sendVerify() {
    if (pin.length !== MAX) return;

    fetch("{{ route('transaction.pin.verify') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        },
        body: JSON.stringify({ pin })
    })
    .then(r => r.json().then(body => ({status: r.status, body})))
    .then(({status, body}) => {

        if (status === 200 && body.verified) {
            // SUCCESS → auto-submit final purchase form
            document.getElementById('pin-input').value = pin;
            document.getElementById('purchase-form').submit();
            return;
        }

        // FAILED
        shake();
        pin = '';
        renderFields();
    })
    .catch(err => {
        console.error(err);
        shake();
        pin = '';
        renderFields();
    });
}


    function shake() {
        const container = document.querySelector('.bg-[#121A2F]');
        container.classList.add('animate-shake');
        setTimeout(() => container.classList.remove('animate-shake'), 500);
    }

    keypad.forEach(k => {
        k.addEventListener('click', () => {
            const v = k.getAttribute('data-value');
            if (!v) return;
            if (pin.length < MAX) {
                pin += v;
                renderFields();
                if (pin.length === MAX) sendVerify();
            }
        });
    });

    clearBtn.addEventListener('click', () => {
        pin = '';
        renderFields();
    });

    backspaceBtn.addEventListener('click', () => {
        pin = pin.slice(0, -1);
        renderFields();
    });

    // keyboard support
    document.addEventListener('keydown', (e) => {
        if (e.key >= '0' && e.key <= '9') {
            if (pin.length < MAX) {
                pin += e.key;
                renderFields();
                if (pin.length === MAX) sendVerify();
            }
        } else if (e.key === 'Backspace') {
            pin = pin.slice(0, -1);
            renderFields();
        }
    });

    // WebAuthn placeholder (requires server-side integration)
    webauthnBtn.addEventListener('click', async () => {
        alert('Fingerprint auth placeholder — implement WebAuthn to enable.');
        // When ready implement navigator.credentials.get() flow and call /webauthn/authenticate
    });

});
</script>

<style>
@keyframes shakeAnim {
  0% { transform: translateX(0); }
  25% { transform: translateX(-6px); }
  50% { transform: translateX(6px); }
  75% { transform: translateX(-4px); }
  100% { transform: translateX(0); }
}
.animate-shake {
  animation: shakeAnim 0.45s ease;
}
</style>
</x-layouts.app>
