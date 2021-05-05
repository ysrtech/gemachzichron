const colors = require('tailwindcss/colors');

module.exports = {
  purge: [
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue',
  ],

  theme: {
    extend: {
      colors: {
        primary: colors.lightBlue
      },
    },
  },

  variants: {
    extend: {
      opacity: ['disabled'],
    },
  },
};
