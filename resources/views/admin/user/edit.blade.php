{{-- <x-layouts.admin>
    <div class="min-h-screen bg-[#0d1117] text-[#f0f6fc] flex items-center justify-center px-4 py-8 font-['Inter']">
          <div class=" w-full flex justify-start mt-6 mb-4">
            <a href="{{ url()->previous() }}" class="text-[#58a6ff] hover:underline flex items-center">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a>
        </div>
        <div class="w-full max-w-md bg-[#161b22] rounded-2xl shadow-lg p-6 border border-[#21262d]">


            <!-- Header -->
            <div class="flex items-center justify-center mb-6">
                <h2 class="text-xl font-semibold text-[#58a6ff]">✏️ Edit User Details</h2>
            </div>

            <!-- Success / Error Messages -->
            @if(session('success'))
                <div class="bg-green-600 text-white p-3 rounded-lg mb-3 text-center">{{ session('success') }}</div>
            @elseif(session('error'))
                <div class="bg-red-600 text-white p-3 rounded-lg mb-3 text-center">{{ session('error') }}</div>
            @endif

            <!-- Form -->
            <form action="{{ route('User.update', $user->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PATCH')

                <!-- Full Name -->
                <div>
                    <label for="full_name" class="block text-sm font-medium text-[#c9d1d9]">Full Name</label>
                    <input type="text" id="full_name" name="full_name" placeholder="Enter full name"
                        value="{{ old('full_name', $user->full_name ?? '') }}"
                        class="mt-1 block w-full rounded-md bg-[#0d1117] border border-[#30363d] text-[#f0f6fc]
                               focus:border-[#238636] focus:ring-2 focus:ring-[#238636] sm:text-sm p-2" />
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-[#c9d1d9]">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter email address"
                        value="{{ old('email', $user->email ?? '') }}"
                        class="mt-1 block w-full rounded-md bg-[#0d1117] border border-[#30363d] text-[#f0f6fc]
                               focus:border-[#238636] focus:ring-2 focus:ring-[#238636] sm:text-sm p-2" />
                </div>

                <!-- Phone Number -->
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-[#c9d1d9]">Phone Number</label>
                    <input type="tel" id="phone_number" name="phone_number" placeholder="000-000-0000"
                        value="{{ old('phone_number', $user->phone_number ?? '') }}"
                        class="mt-1 block w-full rounded-md bg-[#0d1117] border border-[#30363d] text-[#f0f6fc]
                               focus:border-[#238636] focus:ring-2 focus:ring-[#238636] sm:text-sm p-2" />
                </div>

                <!-- User Type (Readonly) -->
                <div>
                    <label for="role" class="block text-sm font-medium text-[#c9d1d9]">User Type</label>
                    <input type="text" id="role" name="role" value="{{ $user->role ?? '' }}" readonly
                        class="mt-1 block w-full rounded-md bg-[#161b22] border border-[#30363d] text-gray-400
                               sm:text-sm p-2" />
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-between pt-4">
                    <button type="submit"
                        class="px-4 py-2 bg-[#238636] hover:bg-[#2ea043] text-white rounded-md text-sm font-medium
                               transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-[#2ea043] focus:ring-offset-2 focus:ring-offset-[#0d1117]">
                        💾 Save Changes
                    </button>

                    <a href="{{ route('viewUser') }}"
                        class="px-4 py-2 bg-[#30363d] hover:bg-[#484f58] text-[#f0f6fc] rounded-md text-sm font-medium
                               transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-[#484f58] focus:ring-offset-2 focus:ring-offset-[#0d1117]">
                        ❌ Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin> --}}

<x-layouts.admin>
    <div class="min-h-screen bg-bg1 dark:bg-bg1 text-t1 dark:text-t1 px-4 py-8 font-['Inter']">

        <!-- Back Button (STRUCTURALLY FIXED) -->
        <div class="max-w-md mx-auto mb-4">
            <a href="{{ url()->previous() }}"
               class="inline-flex items-center gap-1 text-accent dark:text-accent hover:underline">
                <i class="material-icons text-sm">arrow_back</i>
                <span>Back</span>
            </a>
        </div>

        <!-- Card Wrapper -->
        <div class="flex justify-center">
            <div class="w-full max-w-md
                        bg-bg2 dark:bg-bg2
                        border border-accent dark:border-accent
                        rounded-2xl shadow-xl p-6">

                <!-- Header -->
                <h2 class="text-xl font-semibold text-center mb-6 text-accent dark:text-accent">
                    ✏️ Edit User Details
                </h2>

                <!-- Alerts -->
                @if(session('success'))
                    <div class="bg-green-600 text-bg1 p-3 rounded-lg mb-3 text-center">
                        {{ session('success') }}
                    </div>
                @elseif(session('error'))
                    <div class="bg-red-500 text-bg1 p-3 rounded-lg mb-3 text-center">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Form -->
                <form action="{{ route('User.update', $user->id) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    <!-- Full Name -->
                    <div>
                        <label class="block text-sm font-medium text-t2 dark:text-t2">
                            Full Name
                        </label>
                        <input type="text" name="full_name"
                            value="{{ old('full_name', $user->full_name) }}"
                            class="mt-1 w-full rounded-md
                                   bg-bg3 dark:bg-bg3
                                   border border-accent dark:border-accent
                                   text-t1 dark:text-t1
                                   focus:ring-2 focus:ring-green-500 p-2">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-t2 dark:text-t2">
                            Email Address
                        </label>
                        <input type="email" name="email"
                            value="{{ old('email', $user->email) }}"
                            class="mt-1 w-full rounded-md
                                   bg-bg3 dark:bg-bg3
                                   border border-accent dark:border-accent
                                   text-t1 dark:text-t1
                                   focus:ring-2 focus:ring-green-500 p-2">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-t2 dark:text-t2">
                            Phone Number
                        </label>
                        <input type="tel" name="phone_number"
                            value="{{ old('phone_number', $user->phone_number) }}"
                            class="mt-1 w-full rounded-md
                                   bg-bg3 dark:bg-bg3
                                   border border-accent dark:border-accent
                                   text-t1 dark:text-t1
                                   focus:ring-2 focus:ring-green-500 p-2">
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-sm font-medium text-t2 dark:text-t2">
                            User Type
                        </label>
                        <input type="text" value="{{ $user->role }}" readonly
                            class="mt-1 w-full rounded-md
                                   bg-bg2 dark:bg-bg2
                                   border border-accent dark:border-accent
                                   text-t3 dark:text-t3 p-2">
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-between pt-4">
                        <button type="submit"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-bg1 rounded-md transition">
                            💾 Save Changes
                        </button>

                        <a href="{{ route('viewUser') }}"
                           class="px-4 py-2 bg-bg3 dark:bg-bg3
                                  hover:bg-accent/10 dark:hover:bg-accent/20
                                  text-t1 dark:text-t1 rounded-md transition">
                            ❌ Cancel
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>



