/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        // primary project blue
        'pu-blue': '#203368',
        'pu-blue-dark': '#1b2d56',
      },
    },
  },
  plugins: [],
}
