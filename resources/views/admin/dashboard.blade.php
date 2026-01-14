
<x-layouts.admin>
    <div class="min-h-screen bg-bg2 text-t1 p-6 font-['Roboto']">

        <div class="w-full flex justify-start mt-6 mb-4">
            <a href="{{ url()->previous() }}" class="text-accent hover:underline flex items-center">
                <i class="material-icons mr-1">arrow_back</i> Back
            </a>
        </div>

        <div class="w-full text-center -mt-1 p-4">
            <span class="text-2xl font-bold text-accent mb-6">
                Admin Panel
            </span>
        </div>

        <!-- Top Section -->
        <div class="grid grid-cols-2 gap-4 mb-6">

            <!-- All Users -->
            <a href="{{ route('viewUser') }}" class="bg-gradient-to-br from-bg-bg1 to-bg-bg3 bg-1 rounded-2xl p-5 shadow-2xl">
                <p class="text-sm text-t2">All Users</p>
                <div class="flex items-center gap-3">
                    <div class="bg-orange-500/20 p-3 rounded-xl">
                        <i class="fas fa-users text-orange-400 text-xl"></i>
                    </div>
                    <h2 class="text-xl font-bold">
                        {{ short_amount($totalUsers) }}
                    </h2>
                </div>
            </a>

            <!-- Users Balance -->
            <div class="bg-gradient-to-br from-bg-bg1 to-bg-bg3 bg-1 rounded-2xl p-5 shadow-2xl">
                <p class="text-sm text-t2">Users Balance</p>
                <div class="flex items-center gap-3">
                    <div class="bg-accent/20 p-3 rounded-xl">
                        <i class="fas fa-wallet text-accent text-xl"></i>
                    </div>
                    <h2 class="text-xl font-bold">
                        ₦{{ short_amount($totalUserBalance) }}
                    </h2>
                </div>
            </div>

            <!-- Total Funding -->
            <div class="bg-gradient-to-br from-bg-bg1 to-bg-bg3 bg-1 rounded-2xl p-5 shadow-2xl">
                <p class="text-sm text-t2">Total Funding</p>
                <div class="flex items-center gap-3">
                    <div class="bg-purple-500/20 p-3 rounded-xl">
                        <i class="fas fa-arrow-alt-circle-down text-purple-400 text-xl"></i>
                    </div>
                    <h2 class="text-xl font-bold">
                        ₦{{ short_amount($totalFunding) }}
                    </h2>
                </div>
            </div>

             <!-- Customers -->
            <div class="bg-gradient-to-br from-[#182430] to-[#0C141C] rounded-2xl p-5 shadow-md">
                <div class=" flex justify-start ">
                    <p class="text-sm opacity-80">Pending Oders</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="bg-rose-500/20 p-3 rounded-xl">
                        <i class="fas fa-shopping-cart text-rose-400 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">
                            {{ number_format($processingTransactions) }}
                        </h2>
                  </div>
                </div>
            </div>
            
        </div>

        <!-- Active Routers -->
        <a href="{{ route('admin.routers.online') }}"
        class="bg-bg1 rounded-xl p-4 flex justify-between items-center mb-4 hover:opacity-90">

            <div class="flex items-center">
                <i class="fas fa-network-wired mr-3 text-green-400 text-xl p-3 rounded-xl bg-green-500/20"></i>
                <span class="text-t2">Active Routers</span>
            </div>

            <span class="text-green-400 font-bold">
                {{ number_format($onlineRouters ?? 0) }}
            </span>
        </a>


        <!-- Dropdown Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">

            <!-- Reusable dropdown style -->
            @php
                $box = 'relative inline-block text-left bg-bg1 rounded-xl p-4 shadow-md';
                $menu = 'hidden absolute mt-2 w-56 rounded-lg shadow-lg bg-bg3 ring-1 ring-accent/40 divide-y divide-border-neutral z-50';
                $item = 'block px-4 py-2 text-sm text-t1 hover:bg-bg2';
            @endphp

            <!-- Users -->
            <div class="{{ $box }}">
                <button id="userDropdown1" class="w-full inline-flex justify-between items-center text-sm font-medium text-t1">
                    Users
                    <svg class="w-5 h-5 ml-2 text-accent" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div id="userMenu1" class="{{ $menu }}">
                    <a href="{{ route('viewUser') }}" class="{{ $item }}">All Users</a>
                    <a href="{{ route('wallets.manage') }}" class="{{ $item }}">Manage Wallet</a>
                    <a href="{{ route('admin.reseller-view') }}" class="{{ $item }}">Upgrade User</a>
                    <a href="{{ route('admin.users.blockForm') }}" class="{{ $item }}">Block/Unblock</a>
                    <a href="{{ route('display.change.password') }}" class="{{ $item }}">Change Pass or Pin</a>

                </div>
            </div>

        <!-- Vocher Settings-->
            <div class="{{ $box }}">
                <button id="userDropdown2" class="w-full inline-flex justify-between items-center text-sm font-medium text-t1">
                    Vocher Settings
                    <svg class="w-5 h-5 ml-2 text-accent" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div id="userMenu2" class="{{ $menu }}">
                    <a href="{{ route('admin.voucher_profiles.index') }}" class="{{ $item }}">Add Vocher Plan</a> 
                    <a href="{{ route('VoucherSettings.selectReseller') }}" class="{{ $item }}">Manage  Routers</a>
                    <a href="{{ route('admin.routers.online') }}" class="{{ $item }}">Resellers Status </a>
                    <a href="{{ route('admin.Commission.index') }}" class="{{ $item }}">Manage  Commission</a>
                </div>
            </div>

            <!-- Transaction -->
            <div class="{{ $box }}">
                <button id="userDropdown3" class="w-full inline-flex justify-between items-center text-sm font-medium text-t1">
                    Transaction
                    <svg class="w-5 h-5 ml-2 text-accent" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div id="userMenu3" class="{{ $menu }}">
                    <a href="{{ route('T.all') }}" class="{{ $item }}">All Transaction</a>
                    <a href="{{ route('T.processings') }}" class="{{ $item }}">processing Oders</a>
                </div>
            </div>

            <!-- Transaction -->
            <div class="{{ $box }}">
                <button id="userDropdown4" class="w-full inline-flex justify-between items-center text-sm font-medium text-t1">
                    Settings
                    <svg class="w-5 h-5 ml-2 text-accent" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div id="userMenu4" class="{{ $menu }}">
                    <a href="{{ route('Snotify') }}" class="{{ $item }}">Notify Users</a>
                    <a href="{{ route('rewards.index') }}" class="{{ $item }}">Update Reward View</a>
                    <a href="{{ route('admin.settings.appContacts') }}" class="{{ $item }}">App Contacts</a>
                    <a href="{{ route('admin.settings.emergency') }}" class="{{ $item }}">toggleEmergency</a>

                </div>
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
    </div>
</x-layouts.admin>





