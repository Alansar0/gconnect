<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel Admin') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Styles / Scripts -->
    @vite(['resources/css/theme.css', 'resources/js/app.js'])
</head>

<body class="m-0 p-0 bg-bg1 text-t1 font-['Roboto'] min-h-screen">

    <main class="min-h-screen">
        {{ $slot }}
    </main>

    <!-- THEME SWITCHER -->
    {{-- <script>
        function toggleTheme() {
            const html = document.documentElement;
            const isDark = html.getAttribute("data-theme") === "dark";
            const newTheme = isDark ? "light" : "dark";

            html.setAttribute("data-theme", newTheme);
            localStorage.setItem("theme", newTheme);
        }

        document.addEventListener("DOMContentLoaded", () => {
            const savedTheme = localStorage.getItem("theme") || "light";
            document.documentElement.setAttribute("data-theme", savedTheme);
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


</body>
</html>
