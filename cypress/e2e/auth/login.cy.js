// cypress/e2e/auth/login.cy.js

describe('Proceso de Autenticación', () => {

  beforeEach(() => {

    // *** AÑADIDO: Prevenir el Modal de Notificaciones ***
    cy.window().then((win) => {
        win.localStorage.setItem('fm_note_seen', 'true');
    });
    
    // --- ¡ESTE ES EL PASO CLAVE! ---
    // Reseteamos la BD llamando a nuestro script PHP antes de cada test.
    cy.request('/_reset_db.php').then((response) => {
      // Nos aseguramos que el script funcionó
      expect(response.status).to.eq(200);
      expect(response.body).to.contain('Base de datos reseteada');
    });

    // (Opcional) Si tu script de reset crea un usuario de prueba, puedes usarlo.
    // Si no, también puedes crear un usuario de prueba aquí:
  });

  it('Debería permitir a un usuario válido iniciar sesión', () => {

    // 1. Visitar la página principal
    cy.visit('/'); 

    // 2. Abrir el Modal de Autenticación
    cy.get('#loginBtn').click(); // Haz clic en "Ingresar"

    // *** PASO CRÍTICO AÑADIDO: Mostrar el formulario de email/password ***
    // El botón tiene la clase .email (y llama a showEmailForm() )
    cy.get('.oauth button.email').click(); 

    // 3. Rellenar credenciales (El formulario ahora SÍ está visible)
    // Usamos `input#authEmail` y `input#authPassword` que son los IDs dentro del formulario.
    cy.get('input#authEmail').type('test@usuario.com'); 
    cy.get('input#authPassword').type('123456'); 
    
    // 4. Enviar
    cy.get('#authForm').submit();

    // 5. Verificar el resultado (Aserción)
    // El botón de Cerrar Sesión debe estar visible, y el botón de Ingresar debe estar oculto.
    cy.get('#logoutBtn').should('be.visible'); 
    cy.get('#loginBtn').should('not.be.visible');

  });



  it('No debería permitir un usuario con clave incorrecta', () => {
    // 1. Visitar y abrir el modal
    cy.visit('/');
    cy.get('#loginBtn').click(); // Abre el modal

    // *** PASO CRÍTICO AÑADIDO: Mostrar el formulario de email/password ***
    cy.get('.oauth button.email').click(); 

    // 2. Interactuar
    cy.get('input#authEmail').type('test@usuario.com');
    cy.get('input#authPassword').type('clave-incorrecta');
    
    // *** PASO CRÍTICO AÑADIDO: Manejar la alerta del navegador ***
    // Cuando el servidor devuelve 401, tu JS lanza un alert()
    cy.on('window:alert', (str) => {
        // Esta aserción verifica que el texto de la alerta sea el correcto
        expect(str).to.equal('Email o contraseña incorrectos'); 
    });

    cy.get('#authForm').submit();

    // 3. Aserción
    // La URL no cambia, y el modal de autenticación sigue visible.
    cy.url().should('eq', Cypress.config('baseUrl') + '/'); 
    cy.get('#authModal').should('be.visible'); // <-- Añadir esta aserción para confirmar que la interfaz falló correctamente
    
    // ¡ELIMINAMOS ESTA LÍNEA porque el error es un alert, no un elemento HTML!
    // cy.get('.error-message').should('be.visible').and('contain', 'Credenciales incorrectas');

  });


});