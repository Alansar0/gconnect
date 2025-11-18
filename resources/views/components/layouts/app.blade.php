<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Amiri&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="m-0 p-0 bg-[#0d1117] text-[#f0f6fc] font-['Roboto']">
    <main class="min-h-screen">
        {{ $slot }}
    </main>
    @if (in_array(Route::currentRouteName(), ['dashboard', 'transactions.index', 'help.index', 'profile', 'earn.index']))

        <div
    class="fixed bottom-5 left-2 right-2 bg-[#161b22] border border-[#58a6ff]/20 rounded-2xl flex justify-around items-center py-2 shadow-[0_0_20px_rgba(88,166,255,0.3)] backdrop-blur-md z-50">
    <div class="flex w-full justify-around">

        <!-- Home -->
        <a href="{{ route('dashboard') }}"
            class="hover:bg-[#58a6ff]/20 hover:text-[#58a6ff] text-[#f0f6fc] rounded-[40px] p-1.5 transition-all duration-300 shadow-[0_0_8px_rgba(88,166,255,0.1)]">
            <i class="material-icons !text-[34px]">home</i>
        </a>

        <!-- History -->
        <a href="{{ route('transactions.index') }}"
            class="hover:bg-[#58a6ff]/20 hover:text-[#58a6ff] text-[#f0f6fc] rounded-[40px] p-1.5 transition-all duration-300 shadow-[0_0_8px_rgba(88,166,255,0.1)]">
            <i class="material-icons !text-[34px]">history</i>
        </a>

        <!-- Profit (custom icon) -->
        <a href="{{ route('earn.index') }}"
            class="hover:bg-[#58a6ff]/20 hover:text-[#58a6ff] rounded-[40px] p-1.5 transition-all duration-300 flex items-center justify-center shadow-[0_0_8px_rgba(88,166,255,0.1)]">
            <img src="{{ Vite::asset('resources/images/profit-white.png') }}" alt="Profit"
                class="w-[34px] h-[34px] brightness-0 invert hover:invert-0 transition-all duration-300" />
        </a>

        <!-- Support -->
        <a href="{{ route('help.index') }}"
            class="hover:bg-[#58a6ff]/20 hover:text-[#58a6ff] text-[#f0f6fc] rounded-[40px] p-1.5 transition-all duration-300 shadow-[0_0_8px_rgba(88,166,255,0.1)]">
            <i class="material-icons !text-[34px]">support_agent</i>
        </a>

        <!-- Profile -->
        <a href="{{ route('profile') }}"
            class="hover:bg-[#58a6ff]/20 hover:text-[#58a6ff] text-[#f0f6fc] rounded-[40px] p-1.5 transition-all duration-300 shadow-[0_0_8px_rgba(88,166,255,0.1)]">
            <i class="material-icons !text-[34px]">person</i>
        </a>

    </div>
</div>

    @endif
    <script>
        window.userId = {{ Auth::id() ?? 'null' }};

        if (window.userId && window.Echo) {
            Echo.private(`App.Models.User.${window.userId}`)
                .notification((notification) => {
                    console.log('🔔 New notification:', notification);
                    console.log("Received at:", data.created_at);


                    // Example (you can later replace with Alpine.js, Livewire or your UI logic)
                    const notifList = document.querySelector('#notification-list');
                    if (notifList) {
                        const item = document.createElement('div');
                        item.className =
                            'flex items-center gap-3 p-4 border border-white/10 rounded-xl mb-3 bg-gradient-to-br from-[#182430] to-[#0C141C]';
                        item.innerHTML = `
            <img src="{{ Vite::asset('resources/images/logo.png') }}" class="w-10 h-10 rounded-full border border-white" />
            <div>
              <p class="text-sm font-semibold text-white">${notification.title || 'New Notification'}</p>
              <p class="text-xs text-white">${notification.message || ''}</p>
            </div>
          `;
                        notifList.prepend(item);
                    }
                });
        }
    </script>

</body>


</html>
