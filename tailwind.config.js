/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./src/**/*.{html,js,php}"],
  theme: {
    extend: {
      colors: {
        'bg': "#1E1E1E",
        'primary': "#ED0000",
        'subtext': '#8D8D8D',
        'active': '#F3F4F6',
      },
      fontFamily: {
        "primary": ['Helvetica Rounded', 'sans-serif'],
        "secondary": ['Georgia', 'serif'],
      },
      screens: {
        'xxl': '1688px',
      },
    },
  },
  plugins: [],
}

