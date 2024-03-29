/* eslint-disable global-require */

const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    purge: {
        enable: false, // We're doing purge as part of the build process
    },
    theme: {
        extend: {
            colors: {
                'aqua-haze': '#eef5f3',
                'aqua-haze-2': '#e4ebe9',
                'aqua-island': '#99dbca',
                'caribbean-green': '#00bf8f',
                jade: '#00a67c',
                'deep-sea': '#008c69',
                'aqua-deep': '#005c45',
                'spring-wood': '#f6f2ee',
                champagne: '#fae8d8',
                'gold-sand': '#e6b894',
                whiskey: '#d89c6d',
                meteor: '#cf640e',
                'pumpkin-skin': '#b7580c',
                'rich-gold': '#9f4d0b',
                'totem-pole': '#9f290b',
                'blue-smoke': '#788480',
                'nandor-light': '#626f6c',
                nandor: '#545f5c',
                'mine-shaft': '#313131',
                'mine-shaft-dark': '#232323',
                'cod-gray': '#171717',
                'lightest-red': '#eeadad',
                'lighter-red': '#ee908f',
                'light-red': '#ca5153',
                red: '#870f12',
            },
            // Disabling the "dark:" media query for now. It's taking too much
            // time
            // screens: {
            //     dark: {
            //         raw: '(prefers-color-scheme: dark)',
            //     },
            // },
            fontFamily: {
                // eslint-disable-next-line no-undef
                sans: ['Inter var', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    variants: {},
    plugins: [
        require('@tailwindcss/ui'),
    ],
};
