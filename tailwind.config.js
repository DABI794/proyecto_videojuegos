/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          DEFAULT: '#efcc08',
          hover:   '#1cf0e2',
          light:   '#a5c129',
        },
        surface: {
          /* Cambiamos los tonos aquí para que sean menos oscuros */
          darkest: '#2a3b1e', // Antes era #0f172a
          dark:    '#c87816', // Antes era #1e293b
          medium:  '#694764', // Antes era #334155
        },
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
