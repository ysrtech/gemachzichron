const colors = require('tailwindcss/colors');

module.exports = {
  // mode: 'jit',
  purge: [
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue',
  ],

  theme: {
    extend: {
      colors: {
        primary: colors.sky
      },
    },
  },

  variants: {
    extend: {
      opacity: ['disabled'],
      backgroundColor: ['active'],
    },
  },
};
