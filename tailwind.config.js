import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'

/** @type {import('tailwindcss').Config} */
export default {
  darkMode: false,
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        'brand-red': '#E62111',
        'brand-orange': '#ff6600',
        'brand-preto': '#262626',
        'brand-gray': '#d9d9d9',
        'brand-white': '#ffffff',
      },
    },
  },

  plugins: [forms],
}
