
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



