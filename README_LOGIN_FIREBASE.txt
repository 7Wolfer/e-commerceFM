
FRUTERÍA MADRID — LOGIN REAL CON FIREBASE (ID TOKEN VERIFICADO EN PHP)

Este paquete convierte el login simulado en un login REAL usando Firebase Authentication.
El backend en PHP verifica el ID Token con el Admin SDK (Kreait) y solo entonces crea la sesión.

────────────────────────────────────────────────────────────
PRERREQUISITOS
────────────────────────────────────────────────────────────
1) PHP + XAMPP en marcha (Apache y MySQL).
2) Composer instalado (https://getcomposer.org/).
3) Proyecto en Firebase:
   - Crea proyecto y una App Web.
   - Ve a Authentication → Sign-in method → habilita Google, Facebook, Apple y/o Email/Password.
   - En Authentication → Settings → Authorized domains, agrega: localhost
   - DESCARGA las credenciales de Admin:
     Firebase Console → Project settings → Service accounts → Generate new private key
     Guarda el JSON como:  /api/firebase-service-account.json  (en este proyecto).

────────────────────────────────────────────────────────────
INSTALACIÓN
────────────────────────────────────────────────────────────
1) Copia esta carpeta a:
   Windows: C:\xampp\htdocs\fruteria-madrid
   Mac:     /Applications/XAMPP/htdocs/fruteria-madrid

2) Instala dependencias del Admin SDK en la raíz del proyecto (donde está composer.json, lo crearemos aquí):
   - Abre una terminal en la carpeta del proyecto y ejecuta:
     composer require kreait/firebase-php

   Esto creará la carpeta /vendor y el autoload de Composer.

3) Coloca el archivo de servicio de Firebase aquí:
   /api/firebase-service-account.json

4) Abre /index.php y pega tu firebaseConfig (apiKey, authDomain, etc.) en el bloque indicado.

5) Abre en el navegador:
   http://localhost/fruteria-madrid/index.php

   Prueba "Ingresar" con Google (u otro proveedor). El flujo será:
   - Front obtiene el idToken de Firebase.
   - Envía idToken a /api/login_verify.php
   - PHP verifica el token con Admin SDK y crea la sesión segura.
   - La UI cambia a "Cerrar sesión" y puedes usar endpoints protegidos.

────────────────────────────────────────────────────────────
ENDPOINTS NUEVOS/CLAVE
────────────────────────────────────────────────────────────
- POST /api/login_verify.php
    Body JSON: { idToken: "<firebase_id_token>" }
    Respuesta: { ok: true, user: { uid, name, email, photoURL, provider } }

- GET  /api/me.php
- POST /api/logout.php

────────────────────────────────────────────────────────────
NOTAS
────────────────────────────────────────────────────────────
- Este paquete parte de la versión MySQL, por lo que el catálogo y pedidos siguen yendo a la BD.
- Puedes, si quieres, hacer "upsert" del usuario en la tabla `usuarios` tras validar el token (ya está en el código).
- Si tienes errores 500 en login_verify.php, revisa:
  - Que /vendor/autoload.php exista (composer require kreait/firebase-php).
  - Que /api/firebase-service-account.json exista y coincida con tu proyecto.
