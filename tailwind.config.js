/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./vendor/tales-from-a-dev/flowbite-bundle/templates/**/*.html.twig",
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      fontFamily: {
        'body': [
          'Montserrat', 
          'ui-sans-serif', 
          'system-ui',
          // other fallback fonts
        ],
        'sans': [
          'Montserrat', 
          'ui-sans-serif', 
          'system-ui',
          // other fallback fonts
        ]
      },
      colors: {
        transparent: 'transparent',
        black: '#1a202c',
        white: '#fff',
        gray: {
          100: '#f7fafc',
          // ...
          900: '#1a202c',
        },
  
        // ...
      }
    },
  },
  plugins: [],
}
