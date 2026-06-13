const $ = (q)=>document.querySelector(q);
const $$ = (q)=>document.querySelectorAll(q);
const state = {
  cart: JSON.parse(localStorage.getItem('fm_cart')||'[]'),
  profile: {},
  user: null,
  departments: {
    'Frutas y Verduras': ['Ver todos','Verduras','Orgánicos','Frutas'],
    'Despensa': ['Ver todos','Azúcar y Postres','Harinas y Repostería','Especias y Condimentos','Salsas y Aderezos','Aceites y Vinagres','Cereales y Barras','Alimentos infantiles','Café, Chocolate y Té','Granos y Semillas','Sopas, Pastas y Purés','Enlatados y Conservas','Untables y Miel'],
    'Lácteos y Huevo': ['Ver todos','Untables y Conservas','Leches y Bebidas Vegetales','Queso','Crema y Mantequilla','Huevo','Yogurts y Postres'],
    'Carnes y Pescados': ['Ver todos','Pollo y Pavo','Proteína Vegana','Pescados y Mariscos','Carne de Cerdo','Carne de Res'],
    'Botanas, Dulces y Galletas': ['Ver todos','Botanas','Importados','Galletas','Dulces'],
    'Granel': ['Ver todos','Condimentos','Frutos Secos y Nueces','Granos, Semillas y Pastas','Snacks Dulces y Salados','Harinas, Endulzantes y Repostería','Tisanas e Infusiones'],
    'Limpieza y Hogar': ['Ver todos','Hogar','Papel Higiénico y Pañuelos','Lavandería','Desechables','Limpieza General'],
    'Pan y Tortillas': ['Ver todos','Pan Empacado','Panadería Natural','Tortillas'],
    'Higiene Personal y Belleza': ['Ver todos','Cuidado Corporal','Cuidado del Cabello','Cuidado Personal','Higiene Bucal','Bebés','Cuidado Facial','Afeitado y Depilación'],
    'Bebidas': ['Ver todos','Energizantes','Agua','Sueros y Suplementos Alimentacios','Jugos y Bebidas de Fruta','Refrescos'],
    'Congelados': ['Ver todos','Frutas y Verduras Congeladas','Listos para Comer','Helados y Postres'],
    'Salchichonería': ['Ver todos','Jamón y Pechuga','Carnes Frías','Especialidades','Embutidos','Salchichas','Tocino']
  },
  products: [],
  mega: null   // filtro activo del mega-menú: { label, cats:Set, tag } | null
};

function saveCart(){ localStorage.setItem('fm_cart', JSON.stringify(state.cart)); renderCartCount(); }
function formatPrice(v){ return '$' + v.toFixed(2); }
function sumCart(){ return state.cart.reduce((s,i)=>s+i.price*i.qty,0); }
function renderCartCount(){ $('#cartCount').textContent = state.cart.reduce((s,i)=>s+i.qty,0); }
function navigate(view){ 
  $('#catalogoView').classList.toggle('hidden', view!=='catalogo');
  $('#perfilView').classList.toggle('hidden', view!=='perfil');
  $('#simplePage').classList.toggle('hidden', view!=='simple');
  if(view==='catalogo') window.scrollTo({top:0,behavior:'smooth'});
}

/* Departments */
function buildDepartments(){
  const list = $('#deptList'); list.innerHTML='';
  const names = Object.keys(state.departments);
  names.forEach((name,i)=>{
    const li = document.createElement('li');
    li.textContent = name;
    li.className = i===0? 'active':'';
    li.addEventListener('mouseenter',()=>{ $$('.mega-list li').forEach(n=>n.classList.remove('active')); 
    li.classList.add('active'); 
    showSubcats(name); });
    li.addEventListener('click',()=> setMegaFilter(name, { cats: deptCats(name) }));
    list.appendChild(li);
  });
  showSubcats(names[0]);
}

function showSubcats(name){
  const sub = $('#subcats');
  sub.innerHTML = '';

  // 1) Siempre pinto "Ver todos" primero
  const all = document.createElement('a');
  all.href = '#';
  all.textContent = 'Ver todos';
  all.onclick = e => { e.preventDefault(); setMegaFilter(name, { cats: deptCats(name) }); };
  sub.appendChild(all);

  // 2) Pinto el resto, ignorando cualquier "Ver todos" que venga del array
  (state.departments[name] || []).forEach(s => {
    if (String(s).trim().toLowerCase() === 'ver todos') return; // evita duplicado
    const a = document.createElement('a');
    a.href = '#';
    a.textContent = s;
    a.onclick = e => { e.preventDefault(); pickSubcat(s); };
    sub.appendChild(a);
  });
}

/* ---- Mega-menú: filtrado real del catálogo ---- */

// Categorías que cubre un departamento (su nombre + sus subcategorías), en minúsculas.
function deptCats(name){
  const subs = (state.departments[name] || []).filter(s => s.toLowerCase() !== 'ver todos');
  return [name, ...subs];
}

// Click en una subcategoría: algunas son "tags" (orgánico/nuevo/oferta); el resto, categoría.
function pickSubcat(label){
  const low = label.toLowerCase();
  if (low.startsWith('orgánico') || low.startsWith('organico')) return setMegaFilter(label, { tag:'organico' });
  if (low.startsWith('nuevo'))   return setMegaFilter(label, { tag:'nuevo' });
  if (low.startsWith('promo') || low.startsWith('oferta')) return setMegaFilter(label, { tag:'oferta' });
  setMegaFilter(label, { cats:[label] });
}

// Aplica un filtro del mega-menú y limpia el sidebar para que no se contradigan.
function setMegaFilter(label, opts = {}){
  state.mega = {
    label,
    cats: opts.cats ? new Set(opts.cats.map(c => String(c).toLowerCase())) : null,
    tag:  opts.tag  || null
  };
  resetSidebar();
  navigate('catalogo');
  renderProducts();
  setCatalogTitle(label);
}

// Quita todos los filtros (mega-menú + sidebar) y muestra todo.
function clearFilters(){
  state.mega = null;
  resetSidebar();
  renderProducts();
  setCatalogTitle(null);
}

// Si el usuario toca el sidebar, desactiva el filtro del mega-menú.
function onSidebarChange(){
  state.mega = null;
  setCatalogTitle(null);
  renderProducts();
}

function resetSidebar(){
  ['#fNuevo','#fOferta','#fMenor'].forEach(sel => { const el = $(sel); if (el) el.checked = false; });
  $$('.f-cat, .f-brand').forEach(el => el.checked = false);
}

// Cambia el título del catálogo (el <h3> que está justo antes de #products).
function setCatalogTitle(label){
  const grid = document.getElementById('products');
  const h = grid ? grid.previousElementSibling : null;
  if (h && h.tagName === 'H3') h.textContent = label || 'Todos los productos';
}

// Escapa texto para insertarlo en HTML de forma segura.
function escapeHtml(s){
  return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

/* Filters */
function buildFilters(){
  const cats = [...new Set(state.products.map(p=>p.category))];
  const brands = [...new Set(state.products.map(p=>p.brand))];
  $('#catChecks').innerHTML = cats.map(c=>`<label><input type="checkbox" value="${c}" class="f-cat"> ${c}</label>`).join('');
  $('#brandChecks').innerHTML = brands.map(b=>`<label><input type="checkbox" value="${b}" class="f-brand"> ${b}</label>`).join('');
  $$('#fNuevo,#fOferta,#fMenor,.f-cat,.f-brand').forEach(el=>el.addEventListener('change',onSidebarChange));
}
function matchesFilters(p){
  const nuevo = $('#fNuevo').checked;
  const oferta = $('#fOferta').checked;
  const menor = $('#fMenor').checked;
  const catSel = [...$$('.f-cat:checked')].map(i=>i.value);
  const brandSel = [...$$('.f-brand:checked')].map(i=>i.value);
  if(nuevo && !(p.tags||[]).includes('nuevo')) return false;
  if(oferta && !(p.tags||[]).includes('oferta')) return false;
  if(catSel.length && !catSel.includes(p.category)) return false;
  if(brandSel.length && !brandSel.includes(p.brand)) return false;
  if(state.mega){
    if(state.mega.tag){
      if(!(p.tags||[]).includes(state.mega.tag)) return false;
    } else if(state.mega.cats && state.mega.cats.size){
      if(!state.mega.cats.has(String(p.category||'').toLowerCase())) return false;
    }
  }
  return true;
}
function renderProducts(){
  let data = state.products.filter(matchesFilters);
  if($('#fMenor').checked){ data = data.slice().sort((a,b)=>a.price-b.price); }
  const root = $('#products'); root.innerHTML='';
  if(!data.length){
    const label = state.mega?.label || 'esta selección';
    root.innerHTML = `
      <div style="grid-column:1/-1;text-align:center;padding:48px 16px;color:var(--muted)">
        <div style="font-size:48px">🧺</div>
        <p style="margin:8px 0 16px">Aún no tenemos productos en <strong>${escapeHtml(label)}</strong>.</p>
        <button class="btn primary" onclick="clearFilters()">Ver todos los productos</button>
      </div>`;
    return;
  }
  data.forEach(p=>{
    const card = document.createElement('div'); card.className='card';
    card.innerHTML = `
      <div class="thumb"><img alt="${p.name}" src="${p.img}"/></div>
      <div class="body">
        <div class="price">${formatPrice(p.price)} <small>${p.unit}</small></div>
        <div style="min-height:40px">${p.name}</div>
        <div class="add">
          <span class="badge">${p.brand}</span>
          <button class="inc">+</button>
        </div>
      </div>`;
    card.querySelector('.inc').addEventListener('click',()=>addToCart(p));
    root.appendChild(card);
  });
}

/* Cart */
/* ===================== CARRITO ===================== */

/* Usa SOLO una utilidad de precio (ya tienes arriba una: formatPrice(v)).
   Si prefieres la de abajo, borra la de arriba y deja esta: */
// function formatPrice(n){ return `$${n.toFixed(2)}`; }

/* Añadir al carrito */
function addToCart(p){
  const line = state.cart.find(i => i.id === p.id);
  if (line) line.qty++;
  else state.cart.push({ id:p.id, name:p.name, price:p.price, img:p.img, qty:1 });

  saveCart();
  updateCartCount();
  renderCart();
  toggleCart(true);
}

/* Cambiar cantidad */
function qty(i, d){
  state.cart[i].qty += d;
  if (state.cart[i].qty <= 0) state.cart.splice(i,1);
  saveCart();
  updateCartCount();
  renderCart();
}

/* Quitar línea */
function removeLine(i){
  state.cart.splice(i,1);
  saveCart();
  updateCartCount();
  renderCart();
}

/* Contador del ícono del carrito */
function updateCartCount(){
  const n = state.cart.reduce((a,l) => a + l.qty, 0);
  const badge = document.getElementById('cartCount');
  if (badge) badge.textContent = n;
}

/* Pintar líneas dentro del drawer */
function renderCart(){
  const root = document.getElementById('cartLines');
  if (!root) return;                // <-- FIX CRÍTICO

  root.innerHTML = '';
  state.cart.forEach((l, idx)=>{
    const row = document.createElement('div');
    row.className = 'line';
    row.innerHTML = `
      <img src="${l.img}" alt="">
      <div style="flex:1">
        <div style="font-weight:600">${l.name}</div>
        <div>${formatPrice(l.price)}</div>
      </div>
      <div style="display:flex;gap:6px;align-items:center">
        <button class="btn" onclick="qty(${idx},-1)">−</button>
        <span>${l.qty}</span>
        <button class="btn" onclick="qty(${idx},1)">+</button>
      </div>
      <button class="btn" onclick="removeLine(${idx})">×</button>
    `;
    root.appendChild(row);
  });

  const totalEl = document.getElementById('totalPrice');
  if (totalEl) totalEl.textContent = formatPrice(sumCart());
}

/* Abrir/cerrar drawer */
function toggleCart(open){
  const drawer = document.getElementById('cartDrawer');
  if (!drawer) return;
  drawer.classList.toggle('open', open);
  drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
  if (open) renderCart();
}

/* Enlaces/Listeners ÚNICOS */
document.addEventListener('DOMContentLoaded', () => {
  // Botón del icono del carrito (header)
  document.addEventListener('click', (e)=>{
    const btn = e.target.closest('#cartButton');
    if (btn){
      e.preventDefault();
      toggleCart(true);
    }
  });

  // Botón "Continuar al pago"
  document.getElementById('checkoutBtn')?.addEventListener('click', onCheckoutClick);


  // Pinta contador al cargar
  updateCartCount();
});


/* Auth & Profile via PHP */
function openAuth(isRegister=false){ $('#authTitle').textContent = isRegister? 'Crear tu cuenta' : 'Iniciar sesión'; $('#authModal').classList.add('show'); }
function closeAuth(){ $('#authModal').classList.remove('show'); }
async function logout(){ await fetch('/fruteria-madrid/api/logout.php'); await refreshUserUI(); }
async function refreshUserUI(){
  const res = await fetch('/fruteria-madrid/api/me.php');
  if(res.status===200){ state.user = await res.json(); $('#loginBtn').classList.add('hidden'); $('#registerBtn').classList.add('hidden'); $('#logoutBtn').classList.remove('hidden'); }
  else { state.user=null; $('#logoutBtn').classList.add('hidden'); $('#loginBtn').classList.remove('hidden'); $('#registerBtn').classList.remove('hidden'); }
}
async function saveProfile(){
  const body = JSON.stringify({ nombre: $('#pNombre').value, telefono: $('#pTelefono').value, email: $('#pEmail').value });
  await fetch('/fruteria-madrid/api/profile.php', {method:'POST', body});
  alert('Perfil actualizado');
}

/* Notifications placeholder */
function closeNote(){ $('#noteModal').classList.remove('show'); localStorage.setItem('fm_note_seen','1'); }
function enableNotifications(){ closeNote(); alert('Listo, activaremos notificaciones cuando estén disponibles.'); }

/* Simple pages */
function showSimple(title){ navigate('simple'); $('#simpleContent').innerHTML = `<h2>${title}</h2><p>Por el momento vacío.</p>`; }

/* Init */
async function init(){
  buildDepartments();
  await refreshUserUI();
  const prodRes = await fetch('/fruteria-madrid/api/products.php'); state.products = await prodRes.json();
  buildFilters();
  renderProducts();
  renderCartCount();
  navigate('catalogo');
}
init();

async function createOrder(){
  const metodo = document.querySelector("input[name='fulfill']:checked").value;
  const address = (metodo==='delivery') ? { linea1: document.getElementById('addressInput').value } : null;
  const items = state.cart.map(i=>({id:i.id || 0, name:i.name, price:i.price, qty:i.qty}));
  const res = await fetch('./api/create_order.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({metodo, direccion:address, items})});
  const data = await res.json();
  if(data.ok){ alert('Pedido creado #' + data.pedido_id + '\nTotal: $' + data.total); state.cart = []; saveCart(); toggleCart(false); }
  else { alert('Error: ' + (data.error||'desconocido')); }
}
let isProcessing = false;

async function onCheckoutClick(e){
  e.preventDefault();
  if (isProcessing) return;
  isProcessing = true;
  try {
    const metodo = getFulfillMethod(); // 'pickup' o 'delivery'
    if (metodo === 'pickup') {
      // Solo crea la orden en tu backend y NO Stripe
      await createOrder();
    } else {
      // Envío a domicilio -> Stripe
      await pagarConTarjeta(e);
    }
  } finally {
    isProcessing = false;
  }
}

// Helpers
function getFulfillMethod() {
  return document.querySelector("input[name='fulfill']:checked")?.value || 'pickup';
}

function getDeliveryAddress() {
  const metodo = getFulfillMethod();
  if (metodo !== 'delivery') return null;
  const line1 = document.getElementById('addressInput')?.value.trim() || '';
  return { linea1: line1 };
}

// ===================== Mostrar/Ocultar dirección =====================

// Muestra/oculta el bloque de dirección según el método
function toggleAddressBlock(){
  const addressBlock = document.getElementById('addressBlock');
  const isDelivery = getFulfillMethod() === 'delivery';
  if (addressBlock) addressBlock.classList.toggle('hidden', !isDelivery);
}

// Usa la ubicación actual
function askLocation(){
  if(!navigator.geolocation){
    alert('Tu navegador no soporta geolocalización');
    return;
  }
  navigator.geolocation.getCurrentPosition(pos=>{
    const { latitude, longitude } = pos.coords;
    const input = document.getElementById('addressInput');
    if (input) input.value = `Ubicación actual: ${latitude.toFixed(5)}, ${longitude.toFixed(5)}`;
    toggleAddressBlock();
  }, err=>{
    alert('No se pudo obtener tu ubicación. Revisa permisos del navegador.');
  });
}

// Conecta los listeners una vez cargado el DOM
document.addEventListener('DOMContentLoaded', () => {
  // Radios de envío
  document.querySelectorAll("input[name='fulfill']").forEach(r =>
    r.addEventListener('change', toggleAddressBlock)
  );

  // Botón "usar ubicación actual"
  document.getElementById('useLocationBtn')?.addEventListener('click', (e)=>{
    e.preventDefault();
    askLocation();
  });

  // Estado inicial
  toggleAddressBlock();
});

async function pagarConTarjeta(e){
  e?.preventDefault();

  try {
    // Validación básica (si pides dirección para envío)
    const metodo = getFulfillMethod();
    const direccion = getDeliveryAddress();
    if (metodo === 'delivery' && (!direccion || !direccion.linea1)) {
      alert('Por favor, escribe la dirección de entrega.');
      return;
    }

    if (!state.cart || !state.cart.length) {
      alert('Tu carrito está vacío.');
      return;
    }

    const items = state.cart.map(i => ({
      id: i.id, name: i.name, price: i.price, qty: i.qty
    }));

    console.log('[FM] Iniciando pago…', { metodo, direccion, items });

    const res = await fetch('./api/create_stripe_checkout.php', {
      method: 'POST',
      headers: { 'Content-Type':'application/json' },
      body: JSON.stringify({ metodo, direccion, items })
    });

    // Ver errores de red
    if (!res.ok) {
      const txt = await res.text();
      console.error('[FM] Error HTTP:', res.status, txt);
      alert('Error iniciando el pago (HTTP '+res.status+'). Revisa la consola.');
      return;
    }

    const data = await res.json();
    console.log('[FM] Respuesta Stripe:', data);

    if (!data.ok) {
      alert('Stripe dijo: ' + (data.error || 'No se pudo iniciar el pago.'));
      return;
    }

    // Asegura que Stripe.js está cargado
    if (typeof Stripe !== 'function') {
      alert('Stripe.js no se cargó. Revisa que tengas <script src="https://js.stripe.com/v3"></script> en index.php');
      return;
    }

    const stripe = Stripe(data.publishableKey);
    const { error } = await stripe.redirectToCheckout({ sessionId: data.id });
    if (error) {
      console.error('[FM] Error al redirigir a Checkout:', error);
      alert('No se pudo redirigir a Stripe: ' + error.message);
    }
  } catch (err) {
    console.error('[FM] Excepción pagarConTarjeta:', err);
    alert('Error iniciando el pago: ' + err.message);
  }
}
function updateCheckoutBtnText() {
  const metodo = getFulfillMethod();
  const btn = document.getElementById('checkoutBtn');
  if (!btn) return;
  btn.textContent = (metodo === 'delivery') ? 'Continuar al pago' : 'Crear pedido';
}
document.querySelectorAll("input[name='fulfill']").forEach(r =>
  r.addEventListener('change', updateCheckoutBtnText)
);
// Llama una vez al cargar
updateCheckoutBtnText();

window.renderCart = renderCart;
window.qty = qty;
window.removeLine = removeLine;
window.toggleCart = toggleCart;
window.addToCart = addToCart;
window.askLocation = askLocation;
window.saveProfile = saveProfile;
window.openAuth = openAuth;
window.closeAuth = closeAuth;
window.logout = logout;
window.showSimple = showSimple;
window.clearFilters = clearFilters;



