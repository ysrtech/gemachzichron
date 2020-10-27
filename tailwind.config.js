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
                '600': '#4b5495',
                '500': '#6574cd',
                '400': '#7886d7',
                '300': '#b2b7ff',
                '200': '#c3dafe',
                '100': '#e6e8ff',
              },
              // primary: {
              //   '50': '#ece5e3',
              //   '100': '#d1beba',
              //   '200': '#b2938c',
              //   '300': '#93685e',
              //   '400': '#7b473c',
              //   '500': '#642719',
              //   '600': '#5c2316',
              //   '700': '#521d12',
              //   '800': '#48170e',
              //   '900': '#360e08',
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
