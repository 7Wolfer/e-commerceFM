const { defineConfig } = require("cypress");

module.exports = defineConfig({
  e2e: {
    // AQUÍ ES LA MAGIA:
    baseUrl: "http://localhost/E-COMMERCEFM", // <-- ¡Pon tu URL de XAMPP aquí!

    setupNodeEvents(on, config) {
      // implement node event listeners here
    },
  },
});
