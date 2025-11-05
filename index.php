
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Frutería Madrid</title>
  <link rel="stylesheet" href="./styles.css">
  <link rel="icon" href="./assets/logoFM.png">
  <script src="https://js.stripe.com/v3"></script>
</head>
<body>
  <!-- Header -->
  <header class="header">
    <div class="container header-top">
      <a class="brand" href="#" onclick="navigate('catalogo')">
        <img src="./assets/logoFM.png" alt="Frutería Madrid logo">
        <div class="name">Frutería Madrid</div>
      </a>
      <nav class="mega">
        <button class="mega-trigger" aria-haspopup="true">☰ Departamentos</button>
        <div class="mega-panel" role="menu">
          <div class="mega-grid">
            <ul id="deptList" class="mega-list"></ul>
            <div id="subcats" class="mega-subgrid"></div>
          </div>
        </div>
      </nav>
      <div class="actions">
        <button type="button" class="btn icon" id="cartButton" onclick="toggleCart(true)">
        🛒 <span id="cartCount">0</span>
        </button>
        <button class="btn" id="profileBtn" onclick="navigate('perfil')">Perfil</button>
        <button class="btn" id="loginBtn" onclick="openAuth()">Ingresar</button>
        <button class="btn primary" id="registerBtn" onclick="openAuth(true)">Crear tu cuenta</button>
        <button class="btn hidden" id="logoutBtn" onclick="logout()">Cerrar sesión</button>
      </div>
    </div>
  </header>

  <!-- Main -->
  <main class="main container">
    <section id="catalogoView">
      <div class="grid">
        <!-- Sidebar filters -->
        <aside class="sidebar">
          <h3>Filtrar</h3>
          <div class="toggle"><span>Nuevos</span><input type="checkbox" id="fNuevo"></div>
          <div class="toggle"><span>Promociones</span><input type="checkbox" id="fOferta"></div>
          <div class="toggle"><span>Menor precio</span><input type="checkbox" id="fMenor"></div>

          <div class="filter-group">
            <h4>Categorías</h4>
            <div class="checks" id="catChecks"></div>
          </div>

          <div class="filter-group">
            <h4>Marca</h4>
            <div class="checks" id="brandChecks"></div>
          </div>
        </aside>

        <!-- Catalog content -->
        <div>
          <div class="banner">
            <img src="./assets/promo.png" alt="promo">
            <div>
              <h2 style="margin:0 0 6px 0;color:var(--red);font-size:2rem">¡Descubre un NUEVO SABOR!</h2>
              <p>Lattes, con intensidad nivel PRO. <strong>Compra aquí →</strong></p>
            </div>
          </div>
          <h3 style="margin:16px 0">Todos los productos</h3>
          <div id="products" class="products"></div>
        </div>
      </div>
    </section>

    <section id="perfilView" class="hidden">
      <div class="profile-header">
        <h2>Mi perfil</h2>
        <div class="profile-meta">
          <span class="provider-badge" id="providerBadge"></span>
        </div>
      </div>
      <div class="profile-form">
        <div class="form-group">
          <label for="pNombre">Nombre completo</label>
          <input id="pNombre" class="input" placeholder="Tu nombre completo" required>
        </div>
        <div class="form-group">
          <label for="pEmail">Correo electrónico</label>
          <input id="pEmail" class="input" placeholder="tu@email.com" type="email" required>
        </div>
        <div class="form-group">
          <label for="pTelefono">Teléfono (opcional)</label>
          <input id="pTelefono" class="input" placeholder="Número de teléfono" type="tel">
        </div>
        <div class="profile-actions">
          <button class="btn primary" onclick="saveProfile()" id="saveProfileBtn">
            Guardar cambios
          </button>
          <button class="btn" onclick="logout()">Cerrar sesión</button>
        </div>
        <div id="profileMessage" class="profile-message"></div>
      </div>
    </section>

    <section id="simplePage" class="hidden">
      <div id="simpleContent"></div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="footer">
    <div class="container wrap">
      <div>
        <div class="brand">
          <img src="./assets/logoFM.png" alt="logo">
          <div class="name">Frutería Madrid</div>
        </div>
        <p><a class="btn" onclick="openAuth(true)">Regístrate</a></p>
        <div style="display:grid;gap:4px">
          <strong>¿Necesitas ayuda?</strong>
          <a class="btn icon" href="https://wa.me/526621064585" target="_blank">💬 WhatsApp: +52 6621064585</a>
          <a class="btn icon" href="mailto:fruteriamadrid@gmail.com">✉️ fruteriamadrid@gmail.com</a>
        </div>
      </div>
      <div>
        <strong>Acerca de Frutería Madrid</strong>
        <ul style="list-style:none;padding:0;margin:8px 0;display:grid;gap:6px">
          <li><a href="#/sobre" onclick="showSimple('Sobre nosotros')">Sobre nosotros</a></li>
          <li><a href="#/ayuda" onclick="showSimple('Centro de ayuda')">Centro de ayuda</a></li>
          <li><a href="#/blog" onclick="showSimple('Blog')">Blog</a></li>
        </ul>
      </div>
      <div>
        <strong>¡Trabaja con nosotros!</strong>
        <p>¿Quieres compartir tu CV?</p>
        <a class="btn" href="#/cv" onclick="showSimple('Trabaja con nosotros')">Enviar CV</a>
      </div>
      <div>
        <strong>Formas de pago</strong>
        <div class="pay">
          <span>VISA</span><span>Mastercard</span><span>Pluxee</span><span>Vales</span><span>Transferencia</span>
        </div>
        <div style="margin-top:10px">
          <small>Dirección legal: C. Lázaro Mercado 1342, Villa del Real, 83318 Hermosillo, Son.</small>
        </div>
      </div>
    </div>
    <div class="container" style="border-top:1px solid #e5e7eb;padding:12px 0;display:flex;gap:16px;flex-wrap:wrap;align-items:center;justify-content:space-between">
      <small>© Frutería Madrid 2025</small>
      <div style="display:flex;gap:12px">
        <a href="#/terminos" onclick="showSimple('Términos y Condiciones')">Términos y Condiciones</a>
        <a href="#/privacidad" onclick="showSimple('Avisos de Privacidad')">Avisos de Privacidad</a>
      </div>
    </div>
  </footer>

  <!-- Auth Modal -->
  <div id="authModal" class="modal" role="dialog" aria-modal="true">
    <div class="sheet">
      <button class="close-btn" onclick="closeAuth()">&times;</button>
      <div class="auth-hero"></div>
      <h2 id="authTitle">Crear tu cuenta</h2>
      <p>Haz tu súper completo y recibe gratis.</p>
      
      <!-- Formulario Email/Password -->
      <form id="authForm" class="auth-form" style="display:none">
        <div id="nombreGroup">
          <label>Nombre completo</label>
          <input type="text" id="authNombre" class="input" required>
        </div>
        <div>
          <label>Email</label>
          <input type="email" id="authEmail" class="input" required>
        </div>
        <div>
          <label>Contraseña</label>
          <input type="password" id="authPassword" class="input" required minlength="6">
        </div>
        <button type="submit" class="btn primary" style="width:100%">Continuar</button>
        <p style="text-align:center;margin-top:1rem">
          <a href="#" id="authToggle">¿Ya tienes cuenta? Inicia sesión</a>
        </p>
      </form>

      <!-- Botones OAuth -->
      <div class="oauth" id="oauthButtons">
        <button class="google" onclick="firebaseLogin('google')">Continuar con Google</button>
        <button class="email" onclick="showEmailForm()">Continuar con Email</button>
      </div>

      <p style="margin-top:16px;text-align:center"><small>Al dar en continuar, declaro que soy mayor de edad y acepto los <a href='#/terminos' onclick="showSimple('Términos y Condiciones')">Términos y Condiciones</a> y <a href='#/privacidad' onclick="showSimple('Avisos de Privacidad')">Políticas de Privacidad</a>.</small></p>
    </div>
  </div>

  <!-- Notification Prompt Modal -->
  <div id="noteModal" class="modal">
    <div class="sheet" style="max-width:520px">
      <div class="note-card">
        <div style="font-size:54px">🌽</div>
        <h3>Permite envío de notificaciones y no te pierdas</h3>
        <div style="text-align:left">
          <p>✅ Promociones hasta 50% de descuento</p>
          <p>✅ Cupones exclusivos por tiempo limitado</p>
          <p>✅ Sigue la entrega de tu pedido en tiempo real</p>
        </div>
        <div class="note-actions">
          <button class="btn" onclick="closeNote()">Quizá más tarde</button>
          <button class="btn primary" onclick="enableNotifications()">Permitir notificaciones</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Cart Drawer -->
  <aside id="cartDrawer" class="drawer" aria-hidden="true">
    <header>
      <strong>Tu carrito</strong>
      <button class="btn" onclick="toggleCart(false)">Cerrar</button>
    </header>
    <main id="cartLines"></main>
    <div class="checkout">
      <div class="select">
        <label><input type="radio" name="fulfill" value="pickup" checked> Recoger y pagar en sucursal</label>
        <label><input type="radio" name="fulfill" value="delivery"> Envío a domicilio</label>
      </div>
      <div id="addressBlock" class="hidden">
        <input id="addressInput" class="input" placeholder="Dirección de entrega">
        <button class="btn" onclick="askLocation()">📍 Usar mi ubicación actual</button>
      </div>
      <div class="total"><span>Total</span><span id="totalPrice">$0.00</span></div>
      <button class="btn primary" id="checkoutBtn">Continuar al pago</button>

      <small>El pago con tarjeta se integrará más adelante (Stripe / Mercado Pago).</small>
    </div>
  </aside>

  
  

<!-- Firebase Client SDKs -->
<script src="https://www.gstatic.com/firebasejs/10.13.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.13.0/firebase-auth-compat.js"></script>
<script>
  // TODO: Pega aquí tu firebaseConfig (desde Firebase Console → Project settings → General → tu app web)
  const firebaseConfig = {
    apiKey: "AIzaSyBQdLuPydDyVUovgkk1ofpfFzzJkI1y_E0",
    authDomain: "fruteriamadridv1.firebaseapp.com",
    projectId: "fruteriamadridv1",
    storageBucket: "fruteriamadridv1.firebasestorage.app",
    messagingSenderId: "515495841503",
    appId: "1:515495841503:web:c62a297bf2793e1a49236a",
    measurementId: "G-GLGPFSGSMB"
  };
  firebase.initializeApp(firebaseConfig);
  const auth = firebase.auth();

  async function firebaseLogin(providerName){
    try{
      console.log('firebaseLogin invoked for provider:', providerName);
      let provider;
      if(providerName==='google') provider = new firebase.auth.GoogleAuthProvider();
      if(providerName==='facebook') provider = new firebase.auth.FacebookAuthProvider();
      if(providerName==='apple') provider = new firebase.auth.OAuthProvider('apple.com');

      let signResult;
      if(providerName==='email'){
        const email = prompt('Correo:');
        const pass = prompt('Contraseña:');
        signResult = await auth.signInWithEmailAndPassword(email, pass)
          .catch(async err=>{
            if(err.code==='auth/user-not-found'){
              return await auth.createUserWithEmailAndPassword(email, pass);
            } else { throw err; }
          });
      } else {
        // use result to get user reliably and for debugging
        signResult = await auth.signInWithPopup(provider);
        console.log('Firebase signInWithPopup result:', signResult);
      }

      const user = (signResult && signResult.user) ? signResult.user : auth.currentUser;
      if(!user){
        alert('No se pudo obtener el usuario desde Firebase. Revisa la consola para más detalles.');
        console.error('firebaseLogin: no user after sign-in', { signResult, currentUser: auth.currentUser });
        return;
      }

      const idToken = await user.getIdToken(/* forceRefresh */ true);
      console.log('firebaseLogin: obtained idToken (truncated):', idToken ? idToken.substr(0,40)+'...' : '<none>');

      // Enviar token al backend para VERIFICACIÓN real y crear sesión PHP
      const res = await fetch('./api/login_verify.php', {
        method:'POST',
        credentials: 'include',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({ idToken })
      });

      console.log('login_verify HTTP status:', res.status);
      try{
        const json = await res.json();
        console.log('login_verify response JSON:', json);
        if(!res.ok || !json.ok){
          alert('Error de verificación: ' + (json.details || json.error || res.status));
          return;
        }

        // If backend indicates the user needs to set a password (Google sign-up), prompt to set one
        if(json.needsPasswordSetup){
          // Ask for display name (pre-fill with firebase name) and password
          const desiredName = prompt('Parece que tu cuenta no tiene contraseña. Ingresa tu nombre para mostrar:', user.displayName || json.user.email || '');
          const desiredPassword = prompt('Elige una contraseña para ingresar con email en el futuro (mínimo 6 caracteres):');
          if(!desiredPassword || desiredPassword.length < 6){
            alert('Contraseña no válida. Debe tener al menos 6 caracteres. Puedes configurarla más tarde desde tu perfil.');
          } else {
            // send to set_password endpoint
            const setRes = await fetch('./api/set_password.php', {
              method: 'POST',
              credentials: 'include',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ nombre: desiredName, password: desiredPassword })
            });
            const setJson = await setRes.json().catch(()=>null);
            if(setRes.ok && setJson && setJson.ok){
              alert('Contraseña establecida correctamente. Ahora podrás iniciar sesión con tu correo y la contraseña elegida.');
            } else {
              console.error('set_password response:', setRes.status, setJson);
              alert('No fue posible establecer la contraseña. Revisa la consola y contacta al administrador.');
            }
          }
        }

      } catch(parseErr){
        const txt = await res.text();
        console.error('login_verify non-JSON response:', txt);
        alert('Error de verificación (respuesta no válida). Revisa la consola Network para más detalles.');
        return;
      }

      await refreshUserUI();
      closeAuth();
      if(!localStorage.getItem('fm_note_seen')){ document.getElementById('noteModal').classList.add('show'); }
    }catch(e){
      console.error('firebaseLogin exception:', e);
      alert('Login cancelado o falló: ' + e.message);
    }
  }

  auth.onAuthStateChanged(async (u)=>{
    if(u){
      document.getElementById('logoutBtn').classList.remove('hidden');
      document.getElementById('loginBtn').classList.add('hidden');
      document.getElementById('registerBtn').classList.add('hidden');
    } else {
      // El cierre real se hace con /api/logout.php (para destruir sesión PHP)
    }
  });
</script>
<script src="./app.js"></script>

</body>
</html>
