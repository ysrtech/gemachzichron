const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    purge: [
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Roboto', ...defaultTheme.fontFamily.sans],
            },
            colors: {
              primary: {
                '900': '#191e38',
                '800': '#2f365f',
                '700': '#394173',
                // '600': '#5661b3',
                '600': '#4b5495',
                '500': '#6574cd',
                '400': '#7886d7',
                '300': '#b2b7ff',
                '200': '#c3dafe',
                '100': '#e6e8ff',
              },
              // primary: {
              //   '100' : '#b9c8cc',
              //   '200' : '#8aa4aa',
              //   '300' : '#5b7f87',
              //   '400' : '#38636e',
              //   '500' : '#154854',
              //   '600' : '#12414d',
              //   '700' : '#0f3843',
              //   '800' : '#0c303a',
              //   '900' : '#062129',
              // }
            },
        },
    },

    variants: {
      fill: ['responsive', 'hover', 'focus', 'group-hover'],
      opacity: ['responsive', 'hover', 'focus', 'disabled'],
    },

    plugins: [require('@tailwindcss/ui')],
};
