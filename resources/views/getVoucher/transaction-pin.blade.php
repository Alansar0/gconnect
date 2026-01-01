
<x-layouts.app>
    <div class="bg-bg1 min-h-screen flex flex-col items-center justify-center p-6">
        <div class="w-full flex justify-start mt-6 mb-4">
            <a href="{{ url()->previous() }}" class="text-accent hover:underline flex items-center">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a>
        </div>
        
           <!-- Payment Card -->
        <div id="payment-card"
            class="rounded-2xl p-4 w-88 shadow-accent border text-t1"
            style="
                background-color: var(--payment-card-bg);
                border-color: var(--payment-card-border);
                color: var(--payment-card-text);
            ">
            <h2 class="text-xl text-center font-bold mb-2 text-accent">Confirm Payment</h2>
            <p class="text-sm text-t2 mb-6">Please confirm your details before proceeding.</p>

            <!-- Details Box -->
            <div class="bg-accent rounded-xl p-4 mb-6">
                <div class="flex justify-between mb-1 text-sm text-bg1">
                    <span>Product</span>
                    <span class="font-medium text-bg1">
                        {{ session('purchase_data.profile_name') ?? 'Gconnect Voucher' }}
                    </span>
                </div>
                <div class="flex justify-between mt-3 text-bg1 font-semibold">
                    <span>Price</span>
                    <span class="text-bg1">
                        ₦{{ number_format(session('purchase_data.amount') ?? 0, 2) }}
                    </span>
                </div>
                <div class="flex justify-between mt-3 text-bg1 font-semibold">
                    <span>Cashback</span>
                    <span class="text-bg1">
                        <input type="checkbox" id="use-cashback-toggle" class="h-5 w-5 rounded-lg" />
                    </span>
                </div>
            </div>

             <!-- Error Message -->
            <div id="error-message" class="text-red-400 bg-red-900/30 rounded-lg px-3 py-2 mb-4 text-center text-sm hidden"></div>

            <!-- PIN Inputs -->
            <div class="flex justify-around mb-6" id="pin-boxes">
                @foreach(range(1,4) as $i)
                <input type="password" maxlength="1"
                    class="pin-field w-10 h-10 rounded-lg bg-bg1 border border-card text-center text-t1 focus:border-accent outline-none transition" />
                @endforeach
            </div>

            <!-- Keypad -->
            <div class="grid grid-cols-3 gap-3">
                @foreach(range(1,9) as $n)
                    <button data-value="{{ $n }}"
                        class="bg-bg1 rounded-xl py-3 text-xl font-semibold hover:bg-bg2 hover:text-t1 transition">{{ $n }}</button>
                @endforeach

                <button id="clear-btn"
                    class="bg-bg1 rounded-xl py-3 text-lg text-accent font-semibold hover:bg-card transition">Clear</button>
                <button data-value="0"
                    class="bg-bg1 rounded-xl py-3 text-xl font-semibold hover:bg-bg2 hover:text-t1 transition">0</button>
                <button id="backspace-btn"
                    class="bg-bg1 rounded-xl py-3 text-xl text-accent font-semibold hover:bg-card transition">⌫</button>
            </div>

            <div class="flex justify-center mt-6">
                <div class="w-12 h-12 rounded-full border border-accent flex items-center justify-center hover:bg-bg2 transition"
                    id="biometric-auth">
                    <i class="material-icons text-accent">fingerprint</i>
                </div>
            </div>
        </div>

<!-- Add this to your theme.css or inside a <style> -->
<style>
:root {
    /* Default dark mode for payment card */
    --payment-card-bg: #121A2F;
    --payment-card-border: #1C2750;
    --payment-card-text: #ffffff;
}

[data-theme="light"] {
    --payment-card-bg: #ffffff; /* light mode bg */
    --payment-card-border: #e5e7eb; /* light mode border */
    --payment-card-text: #111827; /* light mode text */
}
</style>

    </div>

    <!-- Hidden Form -->
    <form id="final-purchase-form" action="{{ route('voucher.store') }}" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="reseller_id" id="reseller-id" value="{{ session('purchase_data.reseller_id') }}">
        <input type="hidden" name="profile_id" id="profile-id" value="{{ session('purchase_data.profile_id') }}">
        <input type="hidden" name="pin" id="transaction-pin-final">
    </form>

    <!-- Success Popup -->
    <div id="voucher-success-popup"
        class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 hidden">
        <div class="rounded-2xl p-6 w-80 border border-card bg-bg2 text-t1 text-center">
            <i class="material-icons text-green-400 text-5xl mb-4">check_circle</i>
            <h2 class="text-xl font-bold mb-2 text-accent">Voucher Created</h2>
            <div class="bg-bg1 rounded-xl p-4 mb-6 text-left border border-card">
                <div class="flex justify-between mb-3">
                    <span class="text-t2">Username</span>
                    <span class="flex items-center">
                        <span id="voucher-username" class="text-accent font-semibold mr-2"></span>
                        <button onclick="copyText('voucher-username')">
                            <i class="material-icons text-sm text-t2">content_copy</i>
                        </button>
                    </span>
                </div>
                <div class="flex justify-between border-t border-card pt-3">
                    <span class="text-t2">PIN</span>
                    <span class="flex items-center">
                        <span id="voucher-pin" class="text-t1 font-semibold mr-2"></span>
                        <button onclick="copyText('voucher-pin')">
                            <i class="material-icons text-sm text-t2">content_copy</i>
                        </button>
                    </span>
                </div>
            </div>
            <a id="voucher-receipt-btn" class="block w-full bg-accent text-black font-semibold py-3 rounded-full">
                View Receipt
            </a>
        </div>
    </div>

    <!-- Shake Animation -->
      <style>
        /* Add the Tailwind shake utility classes */
        @keyframes shakeAnim {
            0% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-10px);
            }

            50% {
                transform: translateX(10px);
            }

            75% {
                transform: translateX(-5px);
            }

            100% {
                transform: translateX(0);
            }
        }

        .animate-shake {
            animation: shakeAnim 0.45s ease;
        }
    </style>
    <script>
            document.addEventListener('DOMContentLoaded', () => {
                const MAX = 4;
                let pin = '';

                const pinFields = document.querySelectorAll('.pin-field');
                const keypad = document.querySelectorAll('[data-value]');
                const clearBtn = document.getElementById('clear-btn');
                const backspaceBtn = document.getElementById('backspace-btn');

                // --- UI Update ---
                function renderFields() {
                    pinFields.forEach((f, i) => {
                        f.value = pin[i] ? '*' : '';
                    });
                }

                // --- Error Shake ---

                function shakeError(msg = 'Incorrect PIN') {
                    const box = document.getElementById('payment-card');
                    if (box) {
                        box.classList.add('animate-shake');
                        setTimeout(() => box.classList.remove('animate-shake'), 500);
                    }
                    // Show error
                    const errorBox = document.getElementById('error-message');
                    if (errorBox) {
                        errorBox.textContent = msg;
                        errorBox.classList.remove('hidden');
                    }
                    // reset PIN fields
                    pin = '';
                    renderFields();
                }
                
                function verifyPin() {
        if (pin.length !== MAX) return;

        const useCashback = document.getElementById('use-cashback-toggle').checked;
        fetch("{{ route('voucher.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: JSON.stringify({
                reseller_id: "{{ session('purchase_data.reseller_id') }}",
                profile_id: "{{ session('purchase_data.profile_id') }}",
                pin: pin,
                use_cashback: useCashback
            })
        })
        .then(r => r.json().then(body => ({
            status: r.status,
            body
        })))
        .then(({status, body}) => {
            if (status === 200 && body.success) {
                openSuccessPopup(body.code, body.password, body.receipt_url);
            } else {
                shakeError(body.message || "Incorrect PIN");
            }
        })
        .catch(err => {
            console.error(err);
            shakeError("Network error");
        });
        }


                // --- Handle Keypad Click ---
                keypad.forEach(k => {
                    k.addEventListener('click', () => {
                        const v = k.dataset.value;
                        if (pin.length < MAX) {
                            pin += v;
                            renderFields();
                            if (pin.length === MAX) verifyPin();
                        }
                    });
                });

                // --- Backspace ---
                backspaceBtn.addEventListener('click', () => {
                    pin = pin.slice(0, -1);
                    renderFields();
                });

                // --- Clear ---
                clearBtn.addEventListener('click', () => {
                    pin = '';
                    renderFields();
                });

                // --- Keyboard Support ---
                document.addEventListener('keydown', e => {
                    if (e.key >= '0' && e.key <= '9') {
                        if (pin.length < MAX) {
                            pin += e.key;
                            renderFields();
                            if (pin.length === MAX) verifyPin();
                        }
                    } else if (e.key === 'Backspace') {
                        pin = pin.slice(0, -1);
                        renderFields();
                    }
                });
            });
    </script>
    
    <script>
        document.getElementById('biometric-auth')?.addEventListener('click', async () => {
            const options = await fetch('/biometric/auth/options', {method:'POST'}).then(r=>r.json());
            const assertion = await navigator.credentials.get({ publicKey: options });

            const res = await fetch('/biometric/auth/verify', {
                method:'POST',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                body: JSON.stringify(assertion)
            });

            const data = await res.json();
            if (data.success) window.location.href = '/dashboard';
        });

        function openSuccessPopup(username, pin, receiptUrl) {
            document.getElementById('voucher-username').textContent = username;
            document.getElementById('voucher-pin').textContent = pin;
            document.getElementById('voucher-receipt-btn').href = receiptUrl;

            document.getElementById('voucher-success-popup').classList.remove('hidden');
        }

        function copyText(id) {
            const text = document.getElementById(id).textContent;
            navigator.clipboard.writeText(text);
        }

        function hideError() {
            const errorBox = document.getElementById('error-message');
            if (errorBox) errorBox.classList.add('hidden');
        }

        // In ALL PIN changing actions:
        function renderFields() {
            hideError();
            pinFields.forEach((f, i) => {
                f.value = pin[i] ? '*' : '';
            });
        }
    </script>


</x-layouts.app>


