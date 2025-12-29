<x-layouts.admin>
    <div class="min-h-screen bg-[#0d1117] text-[#f0f6fc] p-6 font-['Roboto']">

        <div class=" w-full flex justify-start mt-6 mb-4">
            <a href="{{ url()->previous() }}" class="text-[#58a6ff] hover:underline flex items-center">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a>
        </div>
        <div class="w-full text-center -mt-1 p-4">
            <span class="text-2xl font-bold text-[#58a6ff] mb-6">
                Admin Panel
            </span>
        </div>

        
        <!-- Top Section -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <!-- Balance -->
            <div class="bg-gradient-to-br from-[#182430] to-[#0C141C] rounded-2xl p-5 shadow-md">
                <div class=" flex justify-start ">
                    <p class="text-sm opacity-80">All Users</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="bg-orange-500/20 p-3 rounded-xl">
                        <i class="fas fa-users text-orange-400 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">
                            {{ short_amount($totalUsers) }}
                        </h2>

                    </div>
                </div>
            </div>

            <!-- Users Balance -->
            <div class="bg-gradient-to-br from-[#182430] to-[#0C141C] rounded-2xl p-5 shadow-md">
                <div class=" flex justify-start ">
                    <p class="text-sm opacity-80">Users Balance</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="bg-[#58a6ff]/20 p-3 rounded-xl">
                        <i class="fas fa-wallet text-[#58a6ff] text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">
                            ₦{{ short_amount($totalUserBalance) }}
                        </h2>


                    </div>
                </div>
            </div>

            <!-- Products -->
            <div class="bg-gradient-to-br from-[#182430] to-[#0C141C] rounded-2xl p-5 shadow-md">
                <div class=" flex justify-start ">
                    <p class="text-sm opacity-80">Total Funding</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="bg-purple-500/20 p-3 rounded-xl">
                        <i class="fas fa-arrow-alt-circle-down text-purple-400 text-xl"></i>
                        {{-- <i class="fas fa-box text-purple-400 text-xl"></i> --}}
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">
                            ₦{{ short_amount($totalFunding) }}
                        </h2>

                    </div>
                </div>
            </div>

            <!-- Customers -->
            <div class="bg-gradient-to-br from-[#182430] to-[#0C141C] rounded-2xl p-5 shadow-md">
                <div class=" flex justify-start ">
                    <p class="text-sm opacity-80">Pending Oders</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="bg-green-500/20 p-3 rounded-xl">
                        <i class="fas fa-shopping-cart text-green-400 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">
                            {{ short_amount($processingTransactions) }}
                        </h2>

                    </div>
                </div>
            </div>
        </div>

        {{-- processing  Oders --}}
        <div class="grid grid-row-2 gap-4">
            <div class="bg-[#182430] rounded-xl p-4 flex justify-between items-center mb-4">
                <div>
                    <i class="fas fa-hourglass-half  mr-3  text-[#58a6ff] text-xl p-3 rounded-xl bg-[#58a6ff]/20"></i>
                        <span>processing Oders</span>
                </div>
                    <span class="text-[#58a6ff] font-bold">
                        {{ number_format($processingTransactions) }}
                    </span>
            </div>
        </div>

        <!-- Bottom Section -->
        <!-- User Management Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">

            <!-- Dropdown 1 -->
            <div class="relative inline-block text-left bg-[#182430] rounded-xl p-4 shadow-md">
                <button id="userDropdown1"
                    class="w-full inline-flex justify-between items-center text-sm font-medium text-[#f0f6fc] focus:outline-none">
                    Users
                    <svg class="w-5 h-5 ml-2 text-[#58a6ff]" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div id="userMenu1"
                    class="hidden absolute mt-2 w-56 rounded-lg shadow-lg bg-[#161b22] ring-1 ring-[#58a6ff]/40 divide-y divide-gray-700 z-50">
                    <div class="py-1">
                        <a href="{{ route('viewUser') }}"
                            class="block px-4 py-2 text-sm text-[#f0f6fc] hover:bg-[#182430]">All Users</a>
                        <a href="{{ route('wallets.manage') }}" class="block px-4 py-2 text-sm text-[#f0f6fc] hover:bg-[#182430]">Manage
                            User Wallet</a>
                        
                        <a href="{{ route('admin.reseller-view') }}" class="block px-4 py-2 text-sm text-[#f0f6fc] hover:bg-[#182430]">Upgrade
                            User</a>
                        <a href="#"
                            class="block px-4 py-2 text-sm text-[#f0f6fc] hover:bg-[#182430]">Block/Unblock</a>
                        <a href="{{ route('display.change.password') }}"
                            class="block px-4 py-2 text-sm text-[#f0f6fc] hover:bg-[#182430]">Change Pass or Pin</a>
                    </div>
                </div>
            </div>

            <!-- Dropdown 2 -->
            <div class="relative inline-block text-left bg-[#182430] rounded-xl p-4 shadow-md">
                <button id="userDropdown2"
                    class="w-full inline-flex justify-between items-center text-sm font-medium text-[#f0f6fc] focus:outline-none">
                    Vocher settings
                    <svg class="w-5 h-5 ml-2 text-[#58a6ff]" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div id="userMenu2"
                    class="hidden absolute mt-2 w-56 rounded-lg shadow-lg bg-[#161b22] ring-1 ring-[#58a6ff]/40 divide-y divide-gray-700 z-50">
                    <div class="py-1">
                        <a href="{{ route('admin.voucher_profiles.index') }}" class="block px-4 py-2 text-sm text-[#f0f6fc] hover:bg-[#182430]">Add vocher
                            plan</a>
                        <a href="{{ route('VoucherSettings.selectReseller') }}" class="block px-4 py-2 text-sm text-[#f0f6fc] hover:bg-[#182430]">Add Wan port</a>
                        <a href="#" class="block px-4 py-2 text-sm text-[#f0f6fc] hover:bg-[#182430]">Generate
                            vocher</a>
                    </div>
                </div>
            </div>

            <!-- Dropdown 3 -->
            <div class="relative inline-block text-left bg-[#182430] rounded-xl p-4 shadow-md">
                <button id="userDropdown3"
                    class="w-full inline-flex justify-between items-center text-sm font-medium text-[#f0f6fc] focus:outline-none">
                    Transaction
                    <svg class="w-5 h-5 ml-2 text-[#58a6ff]" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div id="userMenu3"
                    class="hidden absolute mt-2 w-56 rounded-lg shadow-lg bg-[#161b22] ring-1 ring-[#58a6ff]/40 divide-y divide-gray-700 z-50">
                    <div class="py-1">
                        <a href="{{ route('T.all') }}"
                            class="block px-4 py-2 text-sm text-[#f0f6fc] hover:bg-[#182430]">All Transaction</a>
                        <a href="{{ route('T.processings') }}"
                            class="block px-4 py-2 text-sm text-[#f0f6fc] hover:bg-[#182430]">processind Oders</a>
                        <a href="#" class="block px-4 py-2 text-sm text-[#f0f6fc] hover:bg-[#182430]">pending
                            Oders</a>
                    </div>
                </div>
            </div>

            <!-- Dropdown 4 -->
            <div class="relative inline-block text-left bg-[#182430] rounded-xl p-4 shadow-md">
                <button id="userDropdown4"
                    class="w-full inline-flex justify-between items-center text-sm font-medium text-[#f0f6fc] focus:outline-none">
                    Settings
                    <svg class="w-5 h-5 ml-2 text-[#58a6ff]" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div id="userMenu4"
                    class="hidden absolute mt-2 w-56 rounded-lg shadow-lg bg-[#161b22] ring-1 ring-[#58a6ff]/40 divide-y divide-gray-700 z-50">
                    <div class="py-1">
                        <a href="{{ route('Snotify') }}"
                            class="block px-4 py-2 text-sm text-[#f0f6fc] hover:bg-[#182430]">Notify Users</a>
                        <a href="#" class="block px-4 py-2 text-sm text-[#f0f6fc] hover:bg-[#182430]">Add App
                            slide_image</a>
                        <a href="{{ route('rewards.index') }}" class="block px-4 py-2 text-sm text-[#f0f6fc] hover:bg-[#182430]">Update
                            Reward View</a>
                            
                        <a href="{{ route('admin.settings.appContacts') }}"
                            class="block px-4 py-2 text-sm text-[#f0f6fc] hover:bg-[#182430]">App Contacts</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">


        </div>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // find all dropdown buttons that follow the pattern userDropdown1..N
                const buttons = Array.from(document.querySelectorAll('[id^="userDropdown"]'));
                const menus = buttons
                    .map(btn => {
                        const idx = btn.id.replace('userDropdown', '');
                        return document.getElementById('userMenu' + idx);
                    })
                    .filter(Boolean);

                // toggle clicked menu, close the rest
                buttons.forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const idx = btn.id.replace('userDropdown', '');
                        const menu = document.getElementById('userMenu' + idx);
                        if (!menu) return;
                        const wasHidden = menu.classList.contains('hidden');
                        // close all menus
                        menus.forEach(m => m.classList.add('hidden'));
                        // open the clicked one if it was hidden
                        if (wasHidden) menu.classList.remove('hidden');
                    });
                });

                // prevent clicks inside menu from closing it
                menus.forEach(m => m.addEventListener('click', e => e.stopPropagation()));

                // click outside -> close all
                document.addEventListener('click', () => menus.forEach(m => m.classList.add('hidden')));

                // esc -> close all
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') menus.forEach(m => m.classList.add('hidden'));
                });
            });
        </script>
</x-layouts.admin>
