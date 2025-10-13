
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
  products: []
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
    li.addEventListener('mouseenter',()=>{ $$('.mega-list li').forEach(n=>n.classList.remove('active')); li.classList.add('active'); showSubcats(name); });
    list.appendChild(li);
  });
  showSubcats(names[0]);
}
function showSubcats(name){
  const sub = $('#subcats'); sub.innerHTML='';
  (state.departments[name]||[]).forEach(s=>{
    const a = document.createElement('a'); a.href='#'; a.textContent=s; a.onclick=(e)=>e.preventDefault();
    sub.appendChild(a);
  });
  const all = document.createElement('a'); all.href='#'; all.textContent='Ver todos'; sub.prepend(all);
}

/* Filters */
function buildFilters(){
  const cats = [...new Set(state.products.map(p=>p.category))];
  const brands = [...new Set(state.products.map(p=>p.brand))];
  $('#catChecks').innerHTML = cats.map(c=>`<label><input type="checkbox" value="${c}" class="f-cat"> ${c}</label>`).join('');
  $('#brandChecks').innerHTML = brands.map(b=>`<label><input type="checkbox" value="${b}" class="f-brand"> ${b}</label>`).join('');
  $$('#fNuevo,#fOferta,#fMenor,.f-cat,.f-brand').forEach(el=>el.addEventListener('change',renderProducts));
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
  return true;
}
function renderProducts(){
  let data = state.products.filter(matchesFilters);
  if($('#fMenor').checked){ data = data.slice().sort((a,b)=>a.price-b.price); }
  const root = $('#products'); root.innerHTML='';
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
function addToCart(p){
  const line = state.cart.find(i=>i.id===p.id);
  if(line) line.qty++; else state.cart.push({id:p.id, name:p.name, price:p.price, img:p.img, qty:1});
  saveCart(); toggleCart(true);
}
function toggleCart(open){ $('#cartDrawer').classList.toggle('open', open); if(open) renderCart(); }
function renderCart(){
  const root = $('#cartLines'); root.innerHTML='';
  state.cart.forEach((l,idx)=>{
    const row = document.createElement('div'); row.className='line';
    row.innerHTML = `
      <img src="${l.img}" alt="">
      <div style="flex:1">
        <div style="font-weight:600">${l.name}</div>
        <div>${formatPrice(l.price)}</div>
      </div>
      <div style="display:flex;gap:6px;align-items:center">
        <button class="btn" onclick="qty(${idx},-1)">-</button>
        <span>${l.qty}</span>
        <button class="btn" onclick="qty(${idx},1)">+</button>
      </div>
      <button class="btn" onclick="removeLine(${idx})">✕</button>
    `;
    root.appendChild(row);
  });
  $('#totalPrice').textContent = formatPrice(sumCart());
}
function qty(i,d){ state.cart[i].qty += d; if(state.cart[i].qty<=0) state.cart.splice(i,1); saveCart(); renderCart(); }
function removeLine(i){ state.cart.splice(i,1); saveCart(); renderCart(); }
$$("input[name='fulfill']").forEach(r=>r.addEventListener('change',()=>{
  const v = document.querySelector("input[name='fulfill']:checked").value;
  $('#addressBlock').classList.toggle('hidden', v!=='delivery');
}));
function askLocation(){
  if(!navigator.geolocation){ alert('Tu navegador no soporta geolocalización'); return; }
  navigator.geolocation.getCurrentPosition(pos=>{
    const {latitude, longitude} = pos.coords;
    $('#addressInput').value = `Ubicación actual: ${latitude.toFixed(5)}, ${longitude.toFixed(5)}`;
  }, err=>{
    alert('No se pudo obtener tu ubicación. Revisa permisos del navegador.');
  });
}

/* Auth & Profile via PHP */
function openAuth(isRegister=false){ $('#authTitle').textContent = isRegister? 'Crear tu cuenta' : 'Iniciar sesión'; $('#authModal').classList.add('show'); }
function closeAuth(){ $('#authModal').classList.remove('show'); }
async function fakeLogin(provider){
  await fetch(`/fruteria-madrid/api/login.php?provider=${provider}`);
  await refreshUserUI();
  closeAuth();
  if(!localStorage.getItem('fm_note_seen')){ $('#noteModal').classList.add('show'); }
}
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

async function loadMeta(){
  const res = await fetch('./api/meta.php'); const meta = await res.json();
  state.meta = meta;
}
document.addEventListener('click', (e)=>{
  if(e.target && e.target.matches('.checkout .btn.primary')){
    createOrder();
  }
});
async function createOrder(){
  const metodo = document.querySelector("input[name='fulfill']:checked").value;
  const address = (metodo==='delivery') ? { linea1: document.getElementById('addressInput').value } : null;
  const items = state.cart.map(i=>({id:i.id || 0, name:i.name, price:i.price, qty:i.qty}));
  const res = await fetch('./api/create_order.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({metodo, direccion:address, items})});
  const data = await res.json();
  if(data.ok){ alert('Pedido creado #' + data.pedido_id + '\nTotal: $' + data.total); state.cart = []; saveCart(); toggleCart(false); }
  else { alert('Error: ' + (data.error||'desconocido')); }
}
