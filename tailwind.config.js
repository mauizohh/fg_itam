import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import wireui from './vendor/wireui/wireui/tailwind.config.js';

/** @type {import('tailwindcss').Config} */
export default {
    presets: [wireui],
    darkMode: 'class',
    content: [
        './app/**/*.php',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './vendor/wireui/wireui/src/**/*.php',
        './vendor/wireui/wireui/ts/**/*.ts',
        './vendor/wireui/wireui/src/WireUi/**/*.php',
        './vendor/wireui/wireui/src/Components/**/*.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Montserrat', ...defaultTheme.fontFamily.sans],
                heading: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#fdf2f2',
                    100: '#fde8e8',
                    200: '#fbd5d5',
                    300: '#f8b4b4',
                    400: '#f98080',
                    500: '#a92d31', // Main brand red
                    600: '#96262a',
                    700: '#8f2428', // Sidebar & dark accents
                    800: '#751c20',
                    900: '#5c1417',
                    950: '#380a0c',
                },
                'fg-red': {
                    DEFAULT: 'var(--theme-primary, #a92d31)',
                    dark: 'var(--theme-primary-dark, #8f2428)',
                    deep: 'var(--theme-primary-deep, #360a0c)',
                    soft: 'var(--theme-primary-soft, #e8cccc)',
                    line: 'var(--theme-primary-line, #c54f53)',
                },
                ink: '#3b4048',
                muted: '#69717d',
                line: '#e3d0d0',
            },
        },
    },
    plugins: [forms],
};
