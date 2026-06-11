// ============================================================
//  Nora's Flora — script.js
//  Cart logic: add, remove, qty, clear, display, badge
//  FIXED: items now group by id with quantity instead of duplicates
// ============================================================

// --- Helpers ---
function getCart() {
  try {
    return JSON.parse(localStorage.getItem('cart')) || [];
  } catch {
    return [];
  }
}

function saveCart(cart) {
  localStorage.setItem('cart', JSON.stringify(cart));
}

function getTotalItems(cart) {
  return cart.reduce((sum, item) => sum + (item.qty || 1), 0);
}

// --- Toast notification ---
function showToast(message) {
  let toast = document.getElementById('noraToast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'noraToast';
    document.body.appendChild(toast);
  }

  // Reset animation
  toast.style.transition = 'none';
  toast.style.opacity = '0';
  toast.style.transform = 'translateY(12px)';
  toast.textContent = message;

  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      toast.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
      toast.style.opacity = '1';
      toast.style.transform = 'translateY(0)';
    });
  });

  clearTimeout(toast._timeout);
  toast._timeout = setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(12px)';
  }, 2500);
}

// --- Cart actions ---

/**
 * Add item to cart. If item with same id exists, increment qty.
 */
function voegToe(id, naam, prijs) {
  const cart = getCart();
  const existing = cart.find(item => item.id === id);
  if (existing) {
    existing.qty = (existing.qty || 1) + 1;
  } else {
    cart.push({ id, naam, prijs, qty: 1 });
  }
  saveCart(cart);
  updateCartCount();
  showToast('🛒 ' + naam + ' toegevoegd!');

  // If we're on the cart page, re-render
  if (document.getElementById('cartLayout')) {
    renderCartPage();
  }
}

function changeQty(id, delta) {
  const cart = getCart();
  const item = cart.find(i => i.id === id);
  if (!item) return;

  item.qty = (item.qty || 1) + delta;
  if (item.qty <= 0) {
    const idx = cart.findIndex(i => i.id === id);
    cart.splice(idx, 1);
  }
  saveCart(cart);
  updateCartCount();
  renderCartPage();
}

function removeFromCartById(id) {
  const cart = getCart().filter(i => i.id !== id);
  saveCart(cart);
  updateCartCount();
  renderCartPage();
}

// Legacy index-based remove (kept for compatibility with old assortiment.php generated buttons)
function removeFromCart(index) {
  const cart = getCart();
  cart.splice(index, 1);
  saveCart(cart);
  updateCartCount();
  if (document.getElementById('cartLayout')) renderCartPage();
}

function clearCart() {
  saveCart([]);
  updateCartCount();
  renderCartPage();
  showToast('Winkelmandje leeggemaakt.');
}

// --- Cart badge ---
function updateCartCount() {
  const cart = getCart();
  const badge = document.getElementById('cartCount');
  if (!badge) return;
  const total = getTotalItems(cart);
  badge.textContent = total > 0 ? total : '';
  badge.style.display = total > 0 ? 'inline-flex' : 'none';
}

// --- Cart page rendering ---
function renderCartPage() {
  const layout = document.getElementById('cartLayout');
  if (!layout) return; // not on cart page

  const cart = getCart();
  const itemsPanel = document.getElementById('cartItemsPanel');
  const summaryPanel = document.getElementById('cartSummary');
  const successPanel = document.getElementById('checkoutSuccess');

  if (!itemsPanel || !summaryPanel) return;

  if (cart.length === 0) {
    itemsPanel.innerHTML = `
      <div class="empty-cart">
        <div class="empty-cart-icon">🌱</div>
        <p class="empty-cart-title">Je mandje is leeg</p>
        <p class="empty-cart-sub">Voeg wat mooie planten toe aan je bestelling.</p>
        <a href="assortiment.php">Bekijk assortiment</a>
      </div>`;
    // Hide summary, show empty state
    summaryPanel.style.opacity = '0.4';
    summaryPanel.style.pointerEvents = 'none';
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn) checkoutBtn.disabled = true;
    updateSummary(cart);
    return;
  }

  summaryPanel.style.opacity = '1';
  summaryPanel.style.pointerEvents = '';

  const itemCount = getTotalItems(cart);

  let html = `
    <div class="cart-panel-header">
      <h2>Jouw producten</h2>
      <span class="cart-item-count">${itemCount} ${itemCount === 1 ? 'artikel' : 'artikelen'}</span>
    </div>`;

  cart.forEach(item => {
    const lineTotal = (item.prijs * (item.qty || 1)).toFixed(2).replace('.', ',');
    html += `
      <div class="cart-row" data-id="${item.id}">
        <div class="cart-row-img-placeholder">🌿</div>
        <div class="cart-row-info">
          <p class="cart-row-name">${escHtml(item.naam)}</p>
          <p class="cart-row-unit">€${item.prijs.toFixed(2).replace('.', ',')} per stuk</p>
        </div>
        <div class="qty-stepper">
          <button class="qty-btn" onclick="changeQty(${item.id}, -1)" aria-label="Minder">−</button>
          <span class="qty-value">${item.qty || 1}</span>
          <button class="qty-btn" onclick="changeQty(${item.id}, 1)" aria-label="Meer">+</button>
        </div>
        <span class="cart-row-price">€${lineTotal}</span>
        <button class="cart-row-remove" onclick="removeFromCartById(${item.id})" aria-label="Verwijderen">✕</button>
      </div>`;
  });

  itemsPanel.innerHTML = html;
  updateSummary(cart);
}

function updateSummary(cart) {
  const subtotalEl = document.getElementById('summarySubtotal');
  const shippingEl = document.getElementById('summaryShipping');
  const totalEl    = document.getElementById('summaryTotal');
  const checkoutBtn = document.getElementById('checkoutBtn');

  if (!subtotalEl) return;

  const subtotal = cart.reduce((sum, item) => sum + item.prijs * (item.qty || 1), 0);
  const shipping = cart.length > 0 ? 4.95 : 0;
  const total = subtotal + shipping;

  subtotalEl.textContent = '€' + subtotal.toFixed(2).replace('.', ',');
  shippingEl.textContent = shipping > 0 ? '€' + shipping.toFixed(2).replace('.', ',') : 'Gratis';
  totalEl.textContent    = '€' + total.toFixed(2).replace('.', ',');

  if (checkoutBtn) checkoutBtn.disabled = cart.length === 0;
}

function handleCheckout() {
  const cart = getCart();
  if (cart.length === 0) return;

  // Show loading state
  const btn = document.getElementById('checkoutBtn');
  btn.textContent = '⏳ Bestelling wordt verwerkt...';
  btn.disabled = true;

  // Simulate processing (in a real app this would POST to a server)
  setTimeout(() => {
    clearCart();
    const layout = document.getElementById('cartLayout');
    const success = document.getElementById('checkoutSuccess');
    if (layout) layout.style.display = 'none';
    if (success) success.classList.add('visible');
  }, 1500);
}

function escHtml(str) {
  return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// --- Init ---
updateCartCount();
if (document.getElementById('cartLayout')) {
  renderCartPage();
}

// Contact form validation
const contactBtn = document.getElementById('contactButton');
if (contactBtn) {
  contactBtn.addEventListener('click', function () {
    const emailInput = document.getElementById('email');
    const message = document.getElementById('vragenEnOpmerkingen');
    const feedback = document.getElementById('contactFeedback');

    if (!emailInput || !message || !feedback) return;

    const email = emailInput.value.trim();
    const msg = message.value.trim();

    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      feedback.textContent = '⚠ Voer een geldig e-mailadres in.';
      feedback.className = 'error';
      emailInput.focus();
      return;
    }

    if (!msg) {
      feedback.textContent = '⚠ Vul je bericht in.';
      feedback.className = 'error';
      message.focus();
      return;
    }

    // Simulate send
    contactBtn.disabled = true;
    contactBtn.textContent = 'Verzenden...';
    setTimeout(() => {
      feedback.textContent = '✓ Je bericht is verzonden! We nemen snel contact op.';
      feedback.className = 'success';
      emailInput.value = '';
      message.value = '';
      contactBtn.disabled = false;
      contactBtn.textContent = 'Verzenden';
    }, 900);
  });
}
