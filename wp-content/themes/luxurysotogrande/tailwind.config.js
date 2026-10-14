module.exports = {
  content: ["./*.php", "./**/*.php", "./assets/**/*.js"],
  theme: {
    extend: {
      spacing: {
        128: "32rem",
        138: "34.5rem",
        192: "48rem",
      },
      colors: {
        "lsg-blue": "#000022",
        "lsg-gold": "#aba273",
        "lsg-gold-hover": "#8e8761",
        "lsg-gray": "#f9f9f8",
        "lsg-brown": "#2B2622",
        "warm-beige": "#CBBC9A",
      },
      fontFamily: {
        "mrs-saint": ['"Mrs Saint Delafield"', "cursive"],
        playfair: ['"Playfair Display"', "serif"],
        roboto: ["Roboto", "sans-serif"],
      },
    },
  },
  plugins: [
    require("@tailwindcss/typography"),
    function ({ addComponents }) {
      addComponents({
        ".ls-container": {
          maxWidth: "1344px",
          paddingLeft: "16px",
          paddingRight: "16px",
          marginLeft: "auto",
          marginRight: "auto",
        },
      });
    },
  ],
};
