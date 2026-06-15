<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Frutería Madrid — Mercado fresco a domicilio</title>
  <meta name="description" content="Fruta y verdura fresca del día en Hermosillo. Precios justos, ofertas semanales y entrega a domicilio.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700;9..144,900&family=Hanken+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css?v=<?= @filemtime(__DIR__ . '/styles.css') ?>">
  <link rel="icon" href="assets/logoFM.png">
  <script src="https://js.stripe.com/v3"></script>
</head>
<body>
  <!-- Header -->
  <header class="site-header">
    <div class="container header-inner">
      <a class="brand" href="#" onclick="navigate('catalogo')">
        <img src="assets/logoFM.png" alt="Frutería Madrid">
        <span class="brand-name">Frutería&nbsp;Madrid</span>
      </a>

      <nav class="mega">
        <button class="mega-trigger" aria-haspopup="true">
          <span class="mega-bars" aria-hidden="true"></span> Departamentos
        </button>
        <div class="mega-panel" role="menu">
          <div class="mega-grid">
            <ul id="deptList" class="mega-list"></ul>
            <div id="subcats" class="mega-subgrid"></div>
          </div>
        </div>
      </nav>

      <div class="search">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 21l-4.3-4.3M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16z"/></svg>
        <input id="searchInput" type="search" placeholder="Buscar fruta, verdura, marca…" autocomplete="off">
      </div>

      <div class="actions">
        <button type="button" class="icon-btn cart-btn" id="cartButton" onclick="toggleCart(true)" aria-label="Carrito">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3h2l2.4 12.3a2 2 0 0 0 2 1.7h7.7a2 2 0 0 0 2-1.6L23 6H6"/><circle cx="10" cy="20" r="1.6"/><circle cx="18" cy="20" r="1.6"/></svg>
          <span id="cartCount" class="cart-count">0</span>
        </button>
        <button class="btn btn-soft" id="profileBtn" onclick="navigate('perfil')">Perfil</button>
        <button class="btn btn-soft" id="loginBtn" onclick="openAuth()">Ingresar</button>
        <button class="btn btn-primary" id="registerBtn" onclick="openAuth(true)">Crear cuenta</button>
        <button class="btn btn-soft hidden" id="logoutBtn" onclick="logout()">Salir</button>
      </div>
    </div>
  </header>

  <main>
    <section id="catalogoView">
      <!-- Hero -->
      <section class="hero">
        <div class="hero-bg" aria-hidden="true"></div>
        <div class="container hero-inner">
          <div class="hero-text">
            <span class="eyebrow">🧺 Mercado de barrio · Hermosillo</span>
            <h1>Fruta y verdura <em>fresca</em>, directo a tu mesa.</h1>
            <p class="lead">Selección del día, precios justos y entrega a domicilio. Del campo a tu cocina en cuestión de horas.</p>
            <div class="hero-cta">
              <button class="btn btn-primary btn-lg" onclick="document.getElementById('ofertasSection').scrollIntoView({behavior:'smooth'})">Ver ofertas</button>
              <button class="btn btn-outline btn-lg" onclick="document.getElementById('catalogoGrid').scrollIntoView({behavior:'smooth'})">Explorar catálogo</button>
            </div>
            <div class="hero-stats">
              <div><strong>100%</strong><span>Fresco del día</span></div>
              <div><strong>+200</strong><span>Productos</span></div>
              <div><strong>2&nbsp;h</strong><span>Entrega local</span></div>
            </div>
          </div>
          <div class="hero-visual" aria-hidden="true">
            <span class="hero-blob"></span>
            <span class="hero-leaf hero-leaf--1"></span>
            <span class="hero-leaf hero-leaf--2"></span>
            <figure class="hero-card hero-card--a">
              <img src="assets/img/productos/aguacateHass.jpg" alt="">
              <figcaption><span>Aguacate Hass</span><b>$106.60</b></figcaption>
            </figure>
            <figure class="hero-card hero-card--b">
              <img src="assets/img/productos/uvaVerdeSelecta.jpg" alt="">
              <figcaption><span>Uva Verde</span><b>$128.99</b></figcaption>
            </figure>
            <div class="hero-badge"><strong>Fresco</strong><span>cada día</span></div>
          </div>
        </div>
      </section>

      <!-- Ofertas de la semana -->
      <section class="offers-section" id="ofertasSection">
        <div class="container">
          <div class="offers-head">
            <div>
              <span class="section-eyebrow">⚡ Por tiempo limitado</span>
              <h2 class="section-title">Ofertas de la semana</h2>
            </div>
            <div class="offers-timer">
              <span>Termina en</span>
              <strong id="offersCountdown">--:--:--</strong>
            </div>
          </div>
          <div id="offers" class="offers-track"></div>
        </div>
      </section>

      <!-- Catálogo -->
      <div class="container catalog" id="catalogoGrid">
        <aside class="sidebar">
          <h3 class="sidebar-title">Filtrar</h3>
          <div class="switches">
            <label class="switch"><span>Nuevos</span><input type="checkbox" id="fNuevo"><i></i></label>
            <label class="switch"><span>Promociones</span><input type="checkbox" id="fOferta"><i></i></label>
            <label class="switch"><span>Menor precio</span><input type="checkbox" id="fMenor"><i></i></label>
          </div>

          <div class="filter-group">
            <h4>Categorías</h4>
            <div class="checks" id="catChecks"></div>
          </div>

          <div class="filter-group">
            <h4>Marca</h4>
            <div class="checks" id="brandChecks"></div>
          </div>

          <button class="btn btn-soft btn-block" onclick="clearFilters()">Limpiar filtros</button>
        </aside>

        <div class="catalog-main">
          <div class="catalog-head">
            <h2 id="catalogTitle" class="section-title">Todos los productos</h2>
          </div>
          <div id="products" class="products"></div>
        </div>
      </div>
    </section>

    <!-- Perfil -->
    <section id="perfilView" class="hidden">
      <div class="container narrow">
        <div class="panel profile-panel">
          <h2 class="section-title">Mi perfil</h2>
          <p class="muted">Actualiza tu información de contacto.</p>
          <div class="form">
            <label class="field"><span>Nombre completo</span><input id="pNombre" class="input" placeholder="Tu nombre"></label>
            <label class="field"><span>Teléfono</span><input id="pTelefono" class="input" placeholder="Número de teléfono"></label>
            <label class="field"><span>Correo</span><input id="pEmail" class="input" placeholder="tucorreo@email.com"></label>
            <button class="btn btn-primary btn-lg" onclick="saveProfile()">Guardar cambios</button>
          </div>
        </div>
      </div>
    </section>

    <section id="simplePage" class="hidden">
      <div class="container narrow"><div class="panel" id="simpleContent"></div></div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="site-footer">
    <div class="container footer-grid">
      <div class="footer-brand">
        <div class="brand">
          <img src="assets/logoFM.png" alt="">
          <span class="brand-name">Frutería Madrid</span>
        </div>
        <p class="muted">Lo más fresco del mercado, a un clic de distancia.</p>
        <div class="footer-contact">
          <a href="https://wa.me/526621064585" target="_blank" rel="noopener">💬 WhatsApp: +52 662 106 4585</a>
          <a href="mailto:fruteriamadrid@gmail.com">✉️ fruteriamadrid@gmail.com</a>
        </div>
      </div>
      <div>
        <strong>Frutería Madrid</strong>
        <ul>
          <li><a href="#/sobre" onclick="showSimple('Sobre nosotros')">Sobre nosotros</a></li>
          <li><a href="#/ayuda" onclick="showSimple('Centro de ayuda')">Centro de ayuda</a></li>
          <li><a href="#/blog" onclick="showSimple('Blog')">Blog</a></li>
        </ul>
      </div>
      <div>
        <strong>Trabaja con nosotros</strong>
        <ul>
          <li><a href="#/cv" onclick="showSimple('Trabaja con nosotros')">Enviar CV</a></li>
          <li><a href="#" onclick="openAuth(true)">Crear cuenta</a></li>
        </ul>
      </div>
      <div>
        <strong>Formas de pago</strong>
        <div class="pay">
          <span>VISA</span><span>Mastercard</span><span>Pluxee</span><span>Vales</span><span>Transferencia</span>
        </div>
        <p class="muted small">Dirección legal: C. Lázaro Mercado 1342, Villa del Real, 83318 Hermosillo, Son.</p>
      </div>
    </div>
    <div class="container footer-bottom">
      <small>© Frutería Madrid 2025</small>
      <div class="footer-legal">
        <a href="#/terminos" onclick="showSimple('Términos y Condiciones')">Términos y Condiciones</a>
        <a href="#/privacidad" onclick="showSimple('Avisos de Privacidad')">Avisos de Privacidad</a>
      </div>
    </div>
  </footer>

  <!-- Auth Modal -->
  <div id="authModal" class="modal" role="dialog" aria-modal="true">
    <div class="sheet">
      <button class="sheet-close" onclick="closeAuth()" aria-label="Cerrar">✕</button>
      <div class="auth-hero"></div>
      <h2 id="authTitle">Crear tu cuenta</h2>
      <p class="muted">Haz tu súper completo y recibe ofertas exclusivas.</p>
      <div class="oauth">
        <button class="oauth-btn google" onclick="firebaseLogin('google')">Continuar con Google</button>
        <button class="oauth-btn email" onclick="firebaseLogin('email')">Continuar con Email</button>
      </div>
      <p class="legal-note"><small>Al continuar, declaro que soy mayor de edad y acepto los <a href='#/terminos' onclick="showSimple('Términos y Condiciones')">Términos y Condiciones</a> y <a href='#/privacidad' onclick="showSimple('Avisos de Privacidad')">Políticas de Privacidad</a>.</small></p>
    </div>
  </div>

  <!-- Notification Prompt Modal -->
  <div id="noteModal" class="modal">
    <div class="sheet">
      <div class="note-card">
        <div class="note-emoji">🌽</div>
        <h3>No te pierdas nuestras promociones</h3>
        <ul class="note-list">
          <li>✅ Descuentos hasta 50%</li>
          <li>✅ Cupones exclusivos por tiempo limitado</li>
          <li>✅ Sigue tu pedido en tiempo real</li>
        </ul>
        <div class="note-actions">
          <button class="btn btn-soft" onclick="closeNote()">Quizá más tarde</button>
          <button class="btn btn-primary" onclick="enableNotifications()">Permitir notificaciones</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Cart Drawer -->
  <div id="cartOverlay" class="cart-overlay" onclick="toggleCart(false)"></div>
  <aside id="cartDrawer" class="drawer" aria-hidden="true">
    <header class="drawer-head">
      <strong>Tu carrito</strong>
      <button class="icon-btn ghost" onclick="toggleCart(false)" aria-label="Cerrar">✕</button>
    </header>
    <div id="cartLines" class="drawer-body"></div>
    <div class="checkout">
      <div class="segmented">
        <label><input type="radio" name="fulfill" value="pickup" checked><span>🏬 Recoger en sucursal</span></label>
        <label><input type="radio" name="fulfill" value="delivery"><span>🚚 Envío a domicilio</span></label>
      </div>
      <div id="addressBlock" class="hidden address-block">
        <input id="addressInput" class="input" placeholder="Dirección de entrega">
        <button class="btn btn-soft btn-block" id="useLocationBtn">📍 Usar mi ubicación actual</button>
      </div>
      <div class="total"><span>Total</span><span id="totalPrice">$0.00</span></div>
      <button class="btn btn-primary btn-lg btn-block" id="checkoutBtn">Continuar al pago</button>
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
      let provider;
      if(providerName==='google') provider = new firebase.auth.GoogleAuthProvider();
      if(providerName==='facebook') provider = new firebase.auth.FacebookAuthProvider();
      if(providerName==='apple') provider = new firebase.auth.OAuthProvider('apple.com');
      if(providerName==='email'){
        const email = prompt('Correo:');
        const pass = prompt('Contraseña:');
        await auth.signInWithEmailAndPassword(email, pass)
          .catch(async err=>{
            if(err.code==='auth/user-not-found'){
              await auth.createUserWithEmailAndPassword(email, pass);
            } else { throw err; }
          });
      } else {
        await auth.signInWithPopup(provider);
      }
      const user = auth.currentUser;
      const idToken = await user.getIdToken(/* forceRefresh */ true);
      // Enviar token al backend para VERIFICACIÓN real y crear sesión PHP
      const res = await fetch('./api/login_verify.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({ idToken })
      });
      if(!res.ok){
        const err = await res.json().catch(()=>({}));
        alert('Error de verificación: ' + (err.details || res.status));
        return;
      }
      await refreshUserUI();
      closeAuth();
      if(!localStorage.getItem('fm_note_seen')){ document.getElementById('noteModal').classList.add('show'); }
    }catch(e){
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
<script src="app.js?v=<?= @filemtime(__DIR__ . '/app.js') ?>"></script>

</body>
</html>
