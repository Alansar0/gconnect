

<x-layouts.app>
  <div class="bg-[#121212] text-[#f0f0f0] font-sans flex justify-center items-center h-screen m-0 overflow-hidden">
    <div class="w-full max-w-sm p-4 flex flex-col items-center mb-8">
      <div class="mb-2">
        <i class="fas fa-lock text-4xl text-white"></i>
      </div>

      <div class="mb-8 mt-12 text-center w-full">
        <div id="pin-display-container" class="h-12 flex flex-col justify-center items-center">
          @if(session('pin_temp'))
            <p class="text-white text-xl font-light">Confirm your PIN</p>
            <p class="text-gray-400 text-base mt-1">Re-enter your 4-digit PIN</p>
          @else
            <p class="text-white text-xl font-light">Create a 4-digit PIN</p>
            <p class="text-gray-400 text-base mt-1">This will secure your account for quick access</p>
          @endif

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
          @if (session('status'))
            <div class="text-yellow-300 text-sm mt-3">{{ session('status') }}</div>
          @endif
        </div>
      </div>

      <!-- Hidden form that receives the PIN (4 digits) -->
      <form id="pin-form" action="{{ route('pin.store') }}" method="POST">
        @csrf
        <input type="hidden" name="pin" id="pin-input" />
      </form>

      <!-- Keypad Grid -->
      <div class="grid grid-cols-3 gap-6 w-full max-w-xs">
        @foreach(range(1,9) as $n)
          <button data-value="{{ $n }}" class="pin-button w-[70px] h-[70px] bg-[#2c2c2c] text-white rounded-full flex flex-col justify-center items-center text-2xl font-medium shadow-lg transition duration-100 ease-in-out cursor-pointer active:bg-[#404040]">
            {{ $n }}
          </button>
        @endforeach

        <div class="flex justify-center items-center h-full">
          <i class="fas fa-fingerprint text-xl text-gray-500 cursor-pointer hover:text-white transition" id="biometric-placeholder"></i>
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
      const pinForm = document.getElementById('pin-form');
      const pinDotsEl = document.getElementById('pin-dots');
      const pinDots = pinDotsEl.querySelectorAll('.pin-dot');
      const buttons = document.querySelectorAll('.pin-button');
      const backspace = document.getElementById('backspace-button');

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
          // submit
          pinInput.value = pin;
          pinForm.submit();
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

      // keyboard support
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
  </script>
</x-layouts.app>

