module.exports = {
  content: ["./**/*.{php, js}"],
  theme: {
    extend: {
      spacing: {
        '128': '32rem',
        '138': '34.5rem',
        '192': '48rem',
      },
      colors: {
        'lsg-blue' : '#000022',
        'lsg-gold' : '#aba273',
        'lsg-gold-hover' : '#8e8761',
        'lsg-gray' : '#f9f9f8'
      },
    },
  },
  plugins: [
    require('@tailwindcss/typography')
  ],
}