{{-- <x-layouts.app>
      <div class="bg-[#121212] text-[#f0f0f0] font-sans flex justify-center items-center h-screen m-0 overflow-hidden">
        <div class="w-full max-w-sm p-4 flex flex-col items-center mb-8">
            <!-- Lock Icon -->

            <div class="mb-2">
                <i class="fas fa-lock text-4xl text-white "></i>
            </div>
            <!-- Header Section -->

            <div class="mb-8 mt-12 text-center">

                <!-- Dynamic Message/PIN Dots -->
                <div id="pin-display-container" class="h-10 flex flex-col justify-center items-center">
                    <!-- Initial state (Screenshot 1) -->
                    <div id="initial-message">
                        <p class="text-white text-xl font-light">Unlock to use Telegram</p>
                        <p class="text-gray-400 text-base mt-1">Enter your PIN</p>
                    </div>

                    <!-- PIN Dots state (Screenshot 2) - Initially hidden -->
                    <div id="pin-dots" class="hidden flex space-x-4">
                        <!-- PIN Dot Styles: w-[12px] h-[12px] rounded-full border-2 border-[#555555] -->
                        <div
                            class="pin-dot w-[12px] h-[12px] rounded-full border-2 border-[#555555] transition duration-200">
                        </div>
                        <div
                            class="pin-dot w-[12px] h-[12px] rounded-full border-2 border-[#555555] transition duration-200">
                        </div>
                        <div
                            class="pin-dot w-[12px] h-[12px] rounded-full border-2 border-[#555555] transition duration-200">
                        </div>
                        <div
                            class="pin-dot w-[12px] h-[12px] rounded-full border-2 border-[#555555] transition duration-200">
                        </div>
                    </div>

                    <!-- Error Message - Initially hidden -->
                    <p id="error-message" class="hidden text-red-500 text-base font-semibold">Incorrect PIN. Try again.
                    </p>
                </div>
            </div>

            <!-- Keypad Grid -->
            <div class="grid grid-cols-3 gap-6 w-full max-w-xs">

                <!-- Tailwind Keypad Button Styles: w-[70px] h-[70px] bg-[#2c2c2c] rounded-full shadow-lg transition duration-100 ease-in-out active:bg-[#404040] -->
                <button
                    class="pin-button w-[70px] h-[70px] bg-[#2c2c2c] text-white rounded-full flex flex-col justify-center items-center text-2xl font-medium shadow-lg transition duration-100 ease-in-out cursor-pointer active:bg-[#404040]"
                    data-value="1">1</button>
                <button
                    class="pin-button w-[70px] h-[70px] bg-[#2c2c2c] text-white rounded-full flex flex-col justify-center items-center text-2xl font-medium shadow-lg transition duration-100 ease-in-out cursor-pointer active:bg-[#404040]"
                    data-value="2">2</button>
                <button
                    class="pin-button w-[70px] h-[70px] bg-[#2c2c2c] text-white rounded-full flex flex-col justify-center items-center text-2xl font-medium shadow-lg transition duration-100 ease-in-out cursor-pointer active:bg-[#404040]"
                    data-value="3">3</button>

                <!-- Row 2 -->
                <button
                    class="pin-button w-[70px] h-[70px] bg-[#2c2c2c] text-white rounded-full flex flex-col justify-center items-center text-2xl font-medium shadow-lg transition duration-100 ease-in-out cursor-pointer active:bg-[#404040]"
                    data-value="4">4</button>
                <button
                    class="pin-button w-[70px] h-[70px] bg-[#2c2c2c] text-white rounded-full flex flex-col justify-center items-center text-2xl font-medium shadow-lg transition duration-100 ease-in-out cursor-pointer active:bg-[#404040]"
                    data-value="5">5</button>
                <button
                    class="pin-button w-[70px] h-[70px] bg-[#2c2c2c] text-white rounded-full flex flex-col justify-center items-center text-2xl font-medium shadow-lg transition duration-100 ease-in-out cursor-pointer active:bg-[#404040]"
                    data-value="6">6</button>

                <!-- Row 3 -->
                <button
                    class="pin-button w-[70px] h-[70px] bg-[#2c2c2c] text-white rounded-full flex flex-col justify-center items-center text-2xl font-medium shadow-lg transition duration-100 ease-in-out cursor-pointer active:bg-[#404040]"
                    data-value="7">7</button>
                <button
                    class="pin-button w-[70px] h-[70px] bg-[#2c2c2c] text-white rounded-full flex flex-col justify-center items-center text-2xl font-medium shadow-lg transition duration-100 ease-in-out cursor-pointer active:bg-[#404040]"
                    data-value="8">8</button>
                <button
                    class="pin-button w-[70px] h-[70px] bg-[#2c2c2c] text-white rounded-full flex flex-col justify-center items-center text-2xl font-medium shadow-lg transition duration-100 ease-in-out cursor-pointer active:bg-[#404040]"
                    data-value="9">9</button>

                <!-- Row 4 -->
                <div class="flex justify-center items-center h-full">
                    <!-- Placeholder/Biometric Icon area -->
                    <i class="fas fa-fingerprint text-xl text-gray-500 cursor-pointer hover:text-white transition"></i>
                </div>
                <button
                    class="pin-button w-[70px] h-[70px] bg-[#2c2c2c] text-white rounded-full flex flex-col justify-center items-center text-2xl font-medium shadow-lg transition duration-100 ease-in-out cursor-pointer active:bg-[#404040]"
                    data-value="0">0</button>
                <button id="backspace-button"
                    class="flex justify-center items-center h-full text-gray-400 cursor-pointer hover:text-white transition">
                    <!-- Backspace icon (small X in the screenshot) -->
                    <i class="fas fa-backspace text-xl"></i>
                </button>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // --- Configuration ---
                const CORRECT_PIN = "2580"; // The secret PIN for this demo
                const MAX_LENGTH = CORRECT_PIN.length;

                // --- State and DOM Elements ---
                let enteredPin = "";
                let isChecking = false;

                const initialMessageEl = document.getElementById('initial-message');
                const pinDotsEl = document.getElementById('pin-dots');
                const errorMessagEl = document.getElementById('error-message');
                // Pin dots are now selected via their class, but we need the specific element selector for the JS class toggle
                const pinDots = document.querySelectorAll('#pin-dots > div');
                const keypadButtons = document.querySelectorAll('.pin-button');
                const backspaceButton = document.getElementById('backspace-button');

                // --- Functions ---

                /**
                 * Updates the visual display of the PIN dots.
                 */
                const updatePinDisplay = () => {
                    // Show PIN dots and hide messages once the first digit is entered
                    if (enteredPin.length > 0) {
                        initialMessageEl.classList.add('hidden');
                        errorMessagEl.classList.add('hidden');
                        pinDotsEl.classList.remove('hidden');
                    } else {
                        // Back to initial state
                        initialMessageEl.classList.remove('hidden');
                        pinDotsEl.classList.add('hidden');
                    }

                    // Fill the dots based on the entered length
                    pinDots.forEach((dot, index) => {
                        // Custom class definition for filled state: bg-white border-white
                        if (index < enteredPin.length) {
                            dot.classList.add('bg-white', 'border-white');
                        } else {
                            dot.classList.remove('bg-white', 'border-white');
                        }
                    });
                };

                /**
                 * Handles the entry of a digit.
                 * @param {string} digit The digit entered (0-9).
                 */
                const handleDigitEntry = (digit) => {
                    if (isChecking || enteredPin.length >= MAX_LENGTH) {
                        return; // Ignore input while checking or if max length reached
                    }

                    enteredPin += digit;
                    updatePinDisplay();

                    if (enteredPin.length === MAX_LENGTH) {
                        checkPin();
                    }
                };

                /**
                 * Checks the entered PIN against the secret.
                 */
                const checkPin = () => {
                    isChecking = true;

                    // Introduce a small delay to visually show the final dot filling
                    setTimeout(() => {
                        if (enteredPin === CORRECT_PIN) {
                            // Success Logic
                            pinDotsEl.innerHTML =
                                '<p class="text-green-400 text-lg font-semibold">Unlocked!</p>';
                            setTimeout(() => {
                                // Reset for demo purposes
                                resetPin();
                                // Using a custom modal message instead of alert()
                                alert('Authentication Successful!');
                            }, 1000);

                        } else {
                            // Failure Logic
                            enteredPin = ""; // Clear entered PIN immediately
                            isChecking = false;

                            // Display error message
                            pinDotsEl.classList.add('hidden');
                            errorMessagEl.classList.remove('hidden');

                            // Reset visual dots after a delay
                            setTimeout(() => {
                                updatePinDisplay
                                    (); // This will clear the dots and show the message based on enteredPin length (0)
                            }, 1500);

                        }
                    }, 300);
                };

                /**
                 * Handles the backspace action.
                 */
                const handleBackspace = () => {
                    if (isChecking || enteredPin.length === 0) {
                        return;
                    }
                    // Clear any visible error message
                    errorMessagEl.classList.add('hidden');

                    // Remove the last character
                    enteredPin = enteredPin.slice(0, -1);
                    updatePinDisplay();
                };

                /**
                 * Resets the entire state for re-entry.
                 */
                const resetPin = () => {
                    enteredPin = "";
                    isChecking = false;
                    errorMessagEl.classList.add('hidden');
                    updatePinDisplay();
                    // Restore the original pin dots div content in case it was replaced by 'Unlocked!'
                    pinDotsEl.innerHTML = `
                    <div class="pin-dot w-[12px] h-[12px] rounded-full border-2 border-[#555555] transition duration-200"></div>
                    <div class="pin-dot w-[12px] h-[12px] rounded-full border-2 border-[#555555] transition duration-200"></div>
                    <div class="pin-dot w-[12px] h-[12px] rounded-full border-2 border-[#555555] transition duration-200"></div>
                    <div class="pin-dot w-[12px] h-[12px] rounded-full border-2 border-[#555555] transition duration-200"></div>
                `;
                    // Re-select pin dots after resetting innerHTML
                    const newPinDots = document.querySelectorAll('#pin-dots > div');
                    newPinDots.forEach(dot => dot.classList.remove('bg-white', 'border-white'));
                };

                // --- Event Listeners ---

                // Keypad button listeners
                keypadButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        const digit = button.getAttribute('data-value');
                        handleDigitEntry(digit);
                    });
                });

                // Backspace listener
                backspaceButton.addEventListener('click', handleBackspace);

                // Keyboard support (0-9 and Backspace/Delete)
                document.addEventListener('keydown', (e) => {
                    if (e.key >= '0' && e.key <= '9') {
                        handleDigitEntry(e.key);
                    } else if (e.key === 'Backspace' || e.key === 'Delete') {
                        handleBackspace();
                    }
                });

                // Initial setup
                updatePinDisplay();
            });
        </script>
    </div>

</x-layouts.app> --}}

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

