<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    @PwaHead
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#58a6ff">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">


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
    @vite(['resources/css/theme.css', 'resources/js/app.js'])
</head>
    @RegisterServiceWorkerScript
    <script>
        if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/service-worker.js');
        });
        }

        if (evt.request.url.includes('/admin')) {
            return fetch(evt.request);
        }

    </script>

<body class="m-0 p-0 bg-bg1 text-t1 font-['Roboto']">
    
    
    <main class="min-h-screen">
        {{ $slot }}
    </main>

    @if (in_array(Route::currentRouteName(), ['dashboard', 'transactions.index', 'help.index', 'profile', 'earn.index']))
        <!-- BOTTOM NAV -->
<div class="unique-bottom-nav">

    <a href="{{ route('dashboard') }}">
        <i class="material-icons !text-[34px]">home</i>
    </a>

    <a href="{{ route('transactions.index') }}">
        <i class="material-icons !text-[34px]">history</i>
    </a>

    
    <a href="{{ route('earn.index') }}"
   class="unique-bottom-nav-profit">
    <img src="{{ Vite::asset('resources/images/profit-white.png') }}" 
         id="profit-icon"
         class="w-[34px] h-[34px] transition-all duration-300" />
</a>


    <a href="{{ route('help.index') }}">
        <i class="material-icons !text-[34px]">support_agent</i>
    </a>

    <a href="{{ route('profile') }}">
        <i class="material-icons !text-[34px]">person</i>
    </a>

</div>


    @endif

    {{-- <!-- THEME SWITCHER SCRIPT -->
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const isDark = html.getAttribute("data-theme") === "dark";
            html.setAttribute("data-theme", isDark ? "light" : "dark");
            localStorage.setItem("theme", isDark ? "light" : "dark");
        }

        document.addEventListener("DOMContentLoaded", () => {
            const saved = localStorage.getItem("theme") || "light";
            document.documentElement.setAttribute("data-theme", saved);
        });
    </script> --}}
    <!-- THEME SWITCHER (drop this in your main layout, replacing any older theme script) -->
<script>
  (function () {
    const html = document.documentElement;
    // keys / defaults
    const KEY = 'theme';
    const DEFAULT = 'light';

    // helper to apply a theme across both systems:
    function applyTheme(theme) {
      // 1) data-theme for your CSS variable system
      html.setAttribute('data-theme', theme);

      // 2) also toggle 'dark' class so Tailwind dark: variants work
      if (theme === 'dark') {
        html.classList.add('dark');
      } else {
        html.classList.remove('dark');
      }

      // 3) persist
      localStorage.setItem(KEY, theme);
    }

    // initialize on DOMContentLoaded
    document.addEventListener('DOMContentLoaded', () => {
      const saved = localStorage.getItem(KEY);

      // if user hasn't chosen, prefer system preference
      if (!saved) {
        const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        applyTheme(prefersDark ? 'dark' : DEFAULT);
        return;
      }

      applyTheme(saved);
    });

    // expose toggle function globally (used by your buttons)
    window.toggleTheme = function () {
      const current = html.getAttribute('data-theme') || DEFAULT;
      applyTheme(current === 'dark' ? 'light' : 'dark');
    };

    // also expose a force-apply helper for debugging if needed
    window.applyTheme = applyTheme;
  })();
</script>


    <!-- Real-time Notification Listener (unchanged) -->
    <script>
        window.userId = {{ Auth::id() ?? 'null' }};

        if (window.userId && window.Echo) {
            Echo.private(`App.Models.User.${window.userId}`)
                .notification((notification) => {
                    const notifList = document.querySelector('#notification-list');

                    if (notifList) {
                        const item = document.createElement('div');
                        item.className =
                            'flex items-center gap-3 p-4 border border-accent rounded-xl mb-3 bg-bg3 shadow-accent';
                        item.innerHTML = `
                            <img src="{{ Vite::asset('resources/images/logo.png') }}" class="w-10 h-10 rounded-full border border-white" />
                            <div>
                                <p class="text-sm font-semibold text-t1">${notification.title || 'New Notification'}</p>
                                <p class="text-xs text-t2">${notification.message || ''}</p>
                            </div>
                        `;
                        notifList.prepend(item);
                    }
                });
        }
    </script>

</body>
</html>

