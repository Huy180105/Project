import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import flowbitePlugin from 'flowbite/plugin';

/** @type {import('tailwindcss').Config} */
export default {

    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',

        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',

        './node_modules/flowbite/**/*.js',
    ],

    theme: {
        extend: {

            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },

            colors: {

                primary: {
                    50: '#ecfeff',
                    100: '#cffafe',
                    200: '#a5f3fc',
                    300: '#67e8f9',
                    400: '#22d3ee',
                    500: '#06b6d4',
                    600: '#0891b2',
                    700: '#0e7490',
                    800: '#155e75',
                    900: '#164e63',
                },

                dark: {
                    900: '#020617',
                    800: '#0f172a',
                    700: '#1e293b',
                },
            },

            boxShadow: {
                glow: '0 0 20px rgba(34, 211, 238, 0.35)',
            },

            borderRadius: {
                xl2: '1rem',
            },

            animation: {
                float: 'float 3s ease-in-out infinite',
            },

            keyframes: {
                float: {
                    '0%, 100%': {
                        transform: 'translateY(0px)',
                    },
                    '50%': {
                        transform: 'translateY(-10px)',
                    },
                },
            },
        },
    },

    plugins: [
        forms,
        flowbitePlugin,
    ],
};