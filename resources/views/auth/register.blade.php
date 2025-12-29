<x-layouts.app>
    <div class="bg-[#0d1117] text-[#f0f6fc] font-['Roboto'] min-h-screen flex flex-col items-center p-4">
        <!-- Logo -->
        <div class="absolute top-4 left-6 flex items-center gap-2 font-bold text-lg">
            <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="Welcome Image" class="w-8 h-8">
            <span class="text-white -ml-3">connect</span>
        </div>

        <!-- Register Card -->
        <div class="w-full max-w-md bg-[#161b22] py-6 px-6 mt-16 shadow-lg rounded-lg">
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <!-- Full Name -->
                <input type="text" name="full_name" value="{{ old('full_name') }}" placeholder="Full Name"
                    class="w-full px-3 py-2 border border-[#30363d] rounded-md bg-[#0d1117] text-sm text-[#f0f6fc] focus:outline-none focus:ring-1 focus:ring-[#58a6ff]" />
                @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                <!-- Email -->
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address"
                    class="w-full px-3 py-2 border border-[#30363d] rounded-md bg-[#0d1117] text-sm text-[#f0f6fc] focus:outline-none focus:ring-1 focus:ring-[#58a6ff]" />
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                <!-- Phone -->
                <input type="tel" name="phone_number" value="{{ old('phone_number') }}" placeholder="Phone Number"
                    class="w-full px-3 py-2 border border-[#30363d] rounded-md bg-[#0d1117] text-sm text-[#f0f6fc] focus:outline-none focus:ring-1 focus:ring-[#58a6ff]" />
                @error('phone_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                <!-- Password -->
                <div class="relative">
                    <input type="password" name="password" id="password"
                        placeholder="Password"
                        class="w-full px-3 py-2 border border-[#30363d] rounded-md bg-[#0d1117] text-sm text-[#f0f6fc] focus:outline-none focus:ring-1 focus:ring-[#58a6ff]" />
                    <span id="togglePassword"
                        class="material-icons absolute right-4 top-3.5 text-[#8b949e] cursor-pointer select-none text-lg">
                        visibility_off
                    </span>
                </div>
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                <!-- Confirm Password -->
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        placeholder="Confirm Password"
                        class="w-full px-3 py-2 border border-[#30363d] rounded-md bg-[#0d1117] text-sm text-[#f0f6fc] focus:outline-none focus:ring-1 focus:ring-[#58a6ff]" />
                    <span id="toggleConfirmPassword"
                        class="material-icons absolute right-4 top-3.5 text-[#8b949e] cursor-pointer select-none text-lg">
                        visibility_off
                    </span>
                </div>
                @error('password_confirmation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                <!-- Location -->
                <select name="reseller_id" id="reseller_id" required
                    class=" w-full px-3 py-2 border border-[#30363d] rounded-md bg-[#0d1117] text-sm text-[#f0f6fc] focus:outline-none focus:ring-1 focus:ring-[#58a6ff]">
                    <option value="" disabled selected>Select Your Area</option>
                     @foreach ($resellers as $reseller)
                        <option value="{{ $reseller->id }}">{{ $reseller->name }}</option>
                    @endforeach
                </select>


                <!-- Button -->
                <button type="submit"
                    class="w-full py-3 bg-[#233249] text-[#f0f6fc] font-bold text-sm rounded-md hover:bg-[#2c4963] transition">
                    Continue →
                </button>

                <!-- Footer -->
                <div class="text-center text-xs mt-3">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-[#58a6ff] hover:underline">Sign in</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toggle for first password
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        togglePassword.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            togglePassword.textContent = isPassword ? 'visibility' : 'visibility_off';
        });

        // Toggle for confirm password
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmInput = document.getElementById('password_confirmation');
        toggleConfirmPassword.addEventListener('click', () => {
            const isPassword = confirmInput.type === 'password';
            confirmInput.type = isPassword ? 'text' : 'password';
            toggleConfirmPassword.textContent = isPassword ? 'visibility' : 'visibility_off';
        });
    </script>
</x-layouts.app>
