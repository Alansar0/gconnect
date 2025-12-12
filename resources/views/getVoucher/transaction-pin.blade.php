<x-layouts.app>
    <div class="bg-[#0B1120] min-h-screen flex flex-col items-center justify-center p-6">
        <div class=" w-full flex justify-start mt-6 mb-4">
            <a href="{{ url()->previous() }}" class="text-[#58a6ff] hover:underline flex items-center">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a>
        </div>
        <!-- Payment Card -->
        <div id="payment-card" class="bg-[#121A2F] rounded-2xl p-4 w-88 shadow-xl border border-[#1C2750] text-white">
            <h2 class="text-xl text-center font-bold mb-2 text-[#58a6ff]">Confirm Payment</h2>
            <p class="text-sm text-gray-400 mb-6">Please confirm your details before proceeding.</p>
            {{-- !-- Details Box --> --}}
            <div class="bg-[#3a75c4] rounded-xl p-4 mb-6">
                <div class="flex justify-between mb-1 text-sm text-[#121A2F]">
                    <span>Product</span>
                    <span class="font-medium text-[#121A2F]">
                        {{ session('purchase_data.profile_name') ?? 'Gconnect Voucher' }}
                    </span>
                </div>
                <div class="flex justify-between mt-3 text-[#121A2F] font-semibold">
                    <span>Price</span>
                    <span class="text-[#121A2F]">
                        ₦{{ number_format(session('purchase_data.amount') ?? 0, 2) }}
                    </span>
                </div>
            </div>
            <!-- Error Message (hidden by default) -->
            <div id="error-message" class="text-red-400 bg-red-900/30 rounded-lg px-3 py-2 mb-4 text-center text-sm hidden"></div>
            <!-- PIN Inputs -->
            <div class="flex justify-around mb-6" id="pin-boxes">
                <input type="password" maxlength="1"
                    class="pin-field w-10 h-10 rounded-lg bg-[#0B1120] border border-[#1E2A55] text-center text-white focus:border-[#58a6ff] outline-none transition" />
                <input type="password" maxlength="1"
                    class="pin-field w-10 h-10 rounded-lg bg-[#0B1120] border border-[#1E2A55] text-center text-white focus:border-[#58a6ff] outline-none transition" />
                <input type="password" maxlength="1"
                    class="pin-field w-10 h-10 rounded-lg bg-[#0B1120] border border-[#1E2A55] text-center text-white focus:border-[#58a6ff] outline-none transition" />
                <input type="password" maxlength="1"
                    class="pin-field w-10 h-10 rounded-lg bg-[#0B1120] border border-[#1E2A55] text-center text-white focus:border-[#58a6ff] outline-none transition" />
            </div>

            <!-- Keypad -->
            <div class="grid grid-cols-3 gap-3">
                @foreach (range(1, 9) as $n)
                    <button data-value="{{ $n }}"
                        class="bg-[#0B1120] rounded-xl py-3 text-xl font-semibold hover:bg-[#121A2F] hover:text-white transition">{{ $n }}</button>
                @endforeach

                <button id="clear-btn"
                    class="bg-[#0B1120] rounded-xl py-3 text-lg text-[#58a6ff] font-semibold hover:bg-[#1E2A55] transition">Clear</button>
                <button data-value="0"
                    class="bg-[#0B1120] rounded-xl py-3 text-xl font-semibold hover:bg-[#121A2F] hover:text-white transition">0</button>
                <button id="backspace-btn"
                    class="bg-[#0B1120] rounded-xl py-3 text-xl text-[#58a6ff] font-semibold hover:bg-[#1E2A55] transition">⌫</button>
            </div>

            <div class="flex justify-center mt-6">
                <div class="w-12 h-12 rounded-full border border-[#58a6ff] flex items-center justify-center hover:bg-[#0F1B33] transition"
                    id="webauthn-btn">
                    <i class="material-icons text-[#58a6ff]">fingerprint</i>
                </div>
            </div>
        </div>
    </div>
    <!-- Add this form inside your view -->
    <form id="final-purchase-form" action="{{ route('voucher.store') }}" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="reseller_id" id="reseller-id" value="{{ session('purchase_data.reseller_id') }}">
        <input type="hidden" name="profile_id" id="profile-id" value="{{ session('purchase_data.profile_id') }}">
        <input type="hidden" name="pin" id="transaction-pin-final">
    </form>


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
            

            // --- Auto Verify PIN ---
            function verifyPin() {
                if (pin.length !== MAX) return;
                // Submit to final purchase, not just pin-verify!
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
                            pin: pin
                        })
                    })
                    .then(r => r.json().then(body => ({
                        status: r.status,
                        body
                    })))
                    .then(({
                        status,
                        body
                    }) => {
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
    <div id="voucher-success-popup"
        class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 hidden">

        <div class="bg-[#121A2F] rounded-2xl p-6 w-80 border border-[#1C2750] text-white text-center">

            <i class="material-icons text-green-400 text-5xl mb-4">check_circle</i>

            <h2 class="text-xl font-bold mb-2 text-[#58a6ff]">Voucher Created</h2>

            <div class="bg-[#0B1120] rounded-xl p-4 mb-6 text-left border border-[#1E2A55]">
                <div class="flex justify-between mb-3">
                    <span class="text-gray-400">Username</span>
                    <span class="flex items-center">
                        <span id="voucher-username" class="text-[#58a6ff] font-semibold mr-2"></span>
                        <button onclick="copyText('voucher-username')">
                            <i class="material-icons text-sm text-gray-300">content_copy</i>
                        </button>
                    </span>
                </div>

                <div class="flex justify-between border-t border-[#1E2A55] pt-3">
                    <span class="text-gray-400">PIN</span>
                    <span class="flex items-center">
                        <span id="voucher-pin" class="text-white font-semibold mr-2"></span>
                        <button onclick="copyText('voucher-pin')">
                            <i class="material-icons text-sm text-gray-300">content_copy</i>
                        </button>
                    </span>
                </div>
            </div>

            <a id="voucher-receipt-btn" class="block w-full bg-[#58a6ff] text-black font-semibold py-3 rounded-full">
                View Receipt
            </a>
        </div>
    </div>

    <script>
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
