const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            // Font dan warna tema Sutra Parfum (sama dengan proyek React sebelumnya)
            fontFamily: {
                serif: ['"Times New Roman"', 'Georgia', 'serif'],
                sans: ['Jost', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                paper: '#faf9f7',
                ink: '#1f2326',
                mute: '#5f6469',
                line: '#e4e2dd',
                soft: '#f0eeea',
                acc: '#5a2d3f',
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};