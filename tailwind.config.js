/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
        colors: {
        },
    },
},
darkMode: 'class', // or 'media'

  plugins: [],
}

/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class', // <-- use class strategy
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        'ti-accent': '#00E6C3',
        accent: 'var(--accent)',
        'accent-soft': 'var(--accent-soft)',
        'accent-border': 'var(--accent-border)',
        'bg1': 'var(--bg-1)',
        'bg2': 'var(--bg-2)',
        'bg3': 'var(--bg-3)',
        'bg4': 'var(--bg-4)',
        'bg5': 'var(--bg-5)',
        'bg6': 'var(--bg-6)',
        'bg7': 'var(--bg-7)',


        't1': 'var(--text-1)',
        't2': 'var(--text-2)',
        't3': 'var(--text-3)',
      },
      boxShadow: {
        accent: '0 0 10px var(--shadow-accent)',
      },
    },
  },
  darkMode: 'class', // or 'media'
  plugins: [],
}

