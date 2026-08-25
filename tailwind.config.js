/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './*.php',
    './template-parts/**/*.php',
    './woocommerce/**/*.php',
    './inc/**/*.php',
    './src/**/*.{js,css}',
  ],
  theme: {
    extend: {
      colors: {
        primary: 'var(--color-primary)',
        'primary-dark': 'var(--color-primary-dark)',
        secondary: 'var(--color-secondary)',
        muted: 'var(--color-muted)',
        success: 'var(--color-success)',
        warning: 'var(--color-warning)',
        danger: 'var(--color-danger)',
      },
      fontFamily: {
        sans: ['Vazirmatn', 'sans-serif'],
      },
      borderRadius: {
        card: 'var(--radius-card)',
        button: 'var(--radius-button)',
      },
      boxShadow: {
        card: 'var(--shadow-card)',
      },
      transitionDuration: {
        base: '150ms',
      },
    },
  },
  plugins: [],
};
