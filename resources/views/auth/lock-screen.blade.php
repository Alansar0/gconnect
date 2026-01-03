<x-layouts.app>
  <div class="bg-[#121212] text-[#f0f0f0] font-sans flex justify-center items-center h-screen m-0 overflow-hidden">
    <div class="w-full max-w-sm p-4 flex flex-col items-center mb-8">
      <div class="mb-2">
        <i class="fas fa-lock text-4xl text-white "></i>
      </div>

      <div class="mb-8 mt-12 text-center w-full">
        <div id="pin-display-container" class="h-12 flex flex-col justify-center items-center">
          <p class="text-white text-xl font-light">Unlock</p>
          <p class="text-gray-400 text-base mt-1">Enter your 4-digit PIN</p>

          <div id="pin-dots" class="hidden flex space-x-4 mt-4">
            <div class="pin-dot w-[12px] h-[12px] rounded-full border-2 border-[#555555] transition duration-200"></div>
            <div class="pin-dot w-[12px] h-[12px] rounded-full border-2 border-[#555555] transition duration-200"></div>
            <div class="pin-dot w-[12px] h-[12px] rounded-full border-2 border-[#555555] transition duration-200"></div>
            <div class="pin-dot w-[12px] h-[12px] rounded-full border-2 border-[#555555] transition duration-200"></div>
          </div>

          <p id="error-message" class="hidden text-red-500 text-base font-semibold mt-3"></p>
          @if ($errors->any())
            <div class="text-red-500 text-sm mt-3">
              {{ $errors->first() }}
            </div>
          @endif
        </div>
      </div>

      <form id="lock-form" method="POST" action="{{ route('pin.authorize.check') }}">
        @csrf
        <input type="hidden" name="pin" id="pin-input" />
      </form>

      <div class="grid grid-cols-3 gap-6 w-full max-w-xs">
        @foreach(range(1,9) as $n)
          <button data-value="{{ $n }}" class="pin-button w-[70px] h-[70px] bg-[#2c2c2c] text-white rounded-full flex flex-col justify-center items-center text-2xl font-medium shadow-lg transition duration-100 ease-in-out cursor-pointer active:bg-[#404040]">
            {{ $n }}
          </button>
        @endforeach

        <div class="flex justify-center items-center h-full">
          <i class="fas fa-fingerprint text-xl text-gray-500 cursor-pointer hover:text-white transition" id="biometric-auth"></i>
        </div>

        <button data-value="0" class="pin-button w-[70px] h-[70px] bg-[#2c2c2c] text-white rounded-full flex flex-col justify-center items-center text-2xl font-medium shadow-lg transition duration-100 ease-in-out cursor-pointer active:bg-[#404040]">0</button>

        <button id="backspace-button" class="flex justify-center items-center h-full text-gray-400 cursor-pointer hover:text-white transition">
          <i class="fas fa-backspace text-xl"></i>
        </button>
      </div>

    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const MAX = 4;
      let pin = '';
      const pinInput = document.getElementById('pin-input');
      const lockForm = document.getElementById('lock-form');
      const pinDotsEl = document.getElementById('pin-dots');
      const pinDots = pinDotsEl.querySelectorAll('.pin-dot');
      const buttons = document.querySelectorAll('.pin-button');
      const backspace = document.getElementById('backspace-button');
      const errorEl = document.getElementById('error-message');

      function updateDots() {
        if (pin.length > 0) pinDotsEl.classList.remove('hidden');
        else pinDotsEl.classList.add('hidden');

        pinDots.forEach((d, i) => {
          if (i < pin.length) {
            d.classList.add('bg-white', 'border-white');
          } else {
            d.classList.remove('bg-white', 'border-white');
          }
        });
      }

      function submitPinIfComplete() {
        if (pin.length === MAX) {
          pinInput.value = pin;
          lockForm.submit();
        }
      }

      buttons.forEach(b => {
        b.addEventListener('click', () => {
          const v = b.getAttribute('data-value');
          if (pin.length < MAX) {
            pin += v;
            updateDots();
            submitPinIfComplete();
          }
        });
      });

      backspace.addEventListener('click', () => {
        if (pin.length > 0) {
          pin = pin.slice(0, -1);
          updateDots();
        }
      });

      document.addEventListener('keydown', (e) => {
        if (e.key >= '0' && e.key <= '9') {
          if (pin.length < MAX) {
            pin += e.key;
            updateDots();
            submitPinIfComplete();
          }
        } else if (e.key === 'Backspace') {
          if (pin.length > 0) {
            pin = pin.slice(0, -1);
            updateDots();
          }
        }
      });

      updateDots();
    });

     document.addEventListener('DOMContentLoaded', () => {

        /* ===============================
            HELPERS
        =============================== */

        function base64urlToBuffer(base64url) {
            const padding = '='.repeat((4 - base64url.length % 4) % 4);
            const base64 = (base64url + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');

            const raw = atob(base64);
            const buffer = new ArrayBuffer(raw.length);
            const view = new Uint8Array(buffer);

            for (let i = 0; i < raw.length; i++) {
            view[i] = raw.charCodeAt(i);
            }

            return buffer;
        }

        function showError(message) {
            const errorEl = document.getElementById('error-message');
            if (!errorEl) return;
            errorEl.textContent = message;
            errorEl.classList.remove('hidden');
        }

        function hideError() {
            const errorEl = document.getElementById('error-message');
            if (!errorEl) return;
            errorEl.classList.add('hidden');
        }

        /* ===============================
            BIOMETRIC SUPPORT CHECK
        =============================== */

      const biometricBtn = document.getElementById('biometric-auth');

        if (
            biometricBtn &&
            window.isSecureContext &&
            'PublicKeyCredential' in window
        ) {
            biometricBtn.classList.remove('hidden');
        } else {
            return; // WebAuthn not supported
        }

        /* ===============================
            BIOMETRIC LOGIN FLOW
        =============================== */

        biometricBtn.addEventListener('click', async () => {
            try {
            hideError();

            /* -------------------------------
                STEP 1: FETCH ASSERTION OPTIONS
            -------------------------------- */
            const optionsRes = await fetch(
                "{{ route('biometric.login.options') }}",
                {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
                }
            );

            if (!optionsRes.ok) {
                throw new Error('Unable to start biometric authentication');
            }

            const publicKey = await optionsRes.json();

            /* -------------------------------
                STEP 2: REQUIRED BINARY FIXES
                (THIS FIXES YOUR ERROR)
            -------------------------------- */
            publicKey.challenge = base64urlToBuffer(publicKey.challenge);

            if (publicKey.allowCredentials) {
                publicKey.allowCredentials = publicKey.allowCredentials.map(cred => ({
                ...cred,
                id: base64urlToBuffer(cred.id)
                }));
            }

            // Hardening
            publicKey.timeout = 60000; // 60s
            publicKey.userVerification = 'preferred';

            /* -------------------------------
                STEP 3: CALL AUTHENTICATOR
            -------------------------------- */
            const assertion = await navigator.credentials.get({
                publicKey
            });

            if (!assertion) {
                throw new Error('Biometric authentication cancelled');
            }

            /* -------------------------------
                STEP 4: VERIFY ASSERTION
            -------------------------------- */
            const verifyRes = await fetch(
                "{{ route('biometric.login') }}",
                {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(assertion)
                }
            );

            if (!verifyRes.ok) {
                throw new Error('Fingerprint verification failed');
            }

            /* -------------------------------
                SUCCESS → UNLOCK APP
            -------------------------------- */
            window.location.href = "{{ route('dashboard') }}";

            } catch (err) {
            console.error('WebAuthn error:', err);
            showError(err.message || 'Biometric failed. Use PIN.');
            }
        });

    });
 </script>
</x-layouts.app>

