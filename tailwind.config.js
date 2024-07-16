const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        'node_modules/flowbite-vue/**/*.{js,jsx,ts,tsx}',
        'node_modules/flowbite/**/*.{js,jsx,ts,tsx}'
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Helvetica Neue', ...defaultTheme.fontFamily.sans],
            },
        },
        fontSize: {
            xxs: '0.65rem',
            xs: '0.75rem',
            sm: '0.875rem',
            base: '1rem',
            xl: '1.25rem',
            '1xs': '0.600rem',
            '2xl': '1.263rem',
            '3xl': '1.953rem',
            '4xl': '2.441rem',
            '5xl': '3.052rem',
            '6xl': '3.752rem',
        },
        screens: {
            'sm': '340px',
            // => @media (min-width: 640px) { ... }
        
            'md': '768px',
            // => @media (min-width: 768px) { ... }
        
            'lg': '1024px',
            // => @media (min-width: 1024px) { ... }
        
            'xl': '1280px',
            // => @media (min-width: 1280px) { ... }
        
            '2xl': '1536px',
            // => @media (min-width: 1536px) { ... }
        }
    },

    plugins: [
        require('flowbite/plugin'),
        require('@tailwindcss/forms'),
    ]
        
};
