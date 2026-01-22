const colors = require('tailwindcss/colors');

module.exports = {
  content: [
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue',
  ],

  theme: {
    extend: {
      colors: {
        primary: colors.sky,
        zinc: colors.zinc,
      },
    },
  },
};
