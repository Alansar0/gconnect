<x-layouts.app>

    <div class="bg-bg1 text-t1 font-sans flex flex-col w-full h-[100vh]">

        <div class="bg-bg2 rounded-xl shadow-accent w-[95%] h-[90vh] p-4 sm:p-6 md:p-8 mt-10 mx-auto">

            <!-- Header -->
            <h2 class="text-lg font-semibold mb-6 text-left text-accent">User Details</h2>

            <!-- Profile Details -->
            <div class="flex flex-col h-[60vh]">

                <!-- Profile Picture -->
                <div class="mx-auto mb-4 text-center">
                    <img src="{{ Vite::asset('resources/images/logo.png') }}"
                         alt="Profile Picture"
                         class="w-17 h-17 rounded-full object-cover border-2 border-accent">
                </div>

                <!-- Details List -->
                <div class="w-full">
                    
                    <div class="flex justify-between py-4 px-2 border-b border-accent text-sm">
                        <span class="text-t2 font-medium">Full Name</span>
                        <span>{{ Auth::user()->full_name }}</span>
                    </div>

                    <div class="flex justify-between py-4 px-2 border-b border-accent text-sm">
                        <span class="text-t2 font-medium">Email</span>
                        <span>{{ Auth::user()->email }}</span>
                    </div>

                    <div class="flex justify-between py-4 px-2 border-b border-accent text-sm">
                        <span class="text-t2 font-medium">Phone Number</span>
                        <span>{{ Auth::user()->phone_number }}</span>
                    </div>

                    <div class="flex justify-between py-4 px-2 border-b border-accent text-sm">
                        <span class="text-t2 font-medium">User Type</span>
                        <span>{{ Auth::user()->role }}</span>
                    </div>

                    <div class="flex justify-between py-4 px-2 border-b border-accent text-sm">
                        <span class="text-t2 font-medium">Gconnect Area</span>
                        <span>{{ Auth::user()->reseller?->name ?? 'N/A' }}</span>
                    </div>

                    <!-- Theme Switch -->
                    <div class="flex justify-between py-4 px-2 border-b border-accent text-sm">
                        <span class="text-t2 font-medium">Switch Theme</span>
                        <button onclick="toggleTheme()"
                                class="text-accent px-4 py-2 border border-accent rounded">
                            Toggle Theme
                        </button>
                    </div>

                    <div class="flex justify-between py-4 px-2 border-b border-accent text-sm">
                        <span class="text-t2 font-medium">Biometric Unlock</span>

                        <form method="POST" action="{{ route('biometric.toggle') }}">
                            @csrf
                            <button class="px-4 py-2 rounded border border-accent text-accent">
                                {{ Auth::user()->has_biometric ? 'Disable' : 'Enable' }}
                            </button>
                        </form>
                    </div>


                    <div class="flex justify-between py-4 px-2 border-b border-accent text-sm">
                        <span class="text-t2 font-medium">Date Joined</span>
                        <span>{{ Auth::user()->created_at->format('D M d Y H:i:s') }}</span>
                    </div>
                
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-start mt-4">

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>

                    <button 
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="flex-1 text-center bg-[#da3633] text-white rounded-lg py-2 text-sm mx-1 transition hover:bg-[#f85149]">
                            <i class="fas fa-power-off"></i> Log Out
                    </button>


            </div>
        </div>
    </div>

    <!-- CORRECT THEME TOGGLE SCRIPT -->
    <script>
        (function () {
            const html = document.documentElement;
            const KEY = 'theme';
            const DEFAULT = 'light';

            function applyTheme(theme) {
                html.setAttribute('data-theme', theme);

                if (theme === 'dark') html.classList.add('dark');
                else html.classList.remove('dark');

                localStorage.setItem(KEY, theme);
            }

            document.addEventListener('DOMContentLoaded', () => {
                const saved = localStorage.getItem(KEY);
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                applyTheme(saved || (prefersDark ? 'dark' : DEFAULT));
            });

            window.toggleTheme = function () {
                const current = html.getAttribute('data-theme');
                applyTheme(current === 'dark' ? 'light' : 'dark');
            };
        })();
    </script>

</x-layouts.app>
