function voegToe(id, naam, prijs) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  cart.push({id, naam, prijs});
  localStorage.setItem("cart", JSON.stringify(cart));
  alert(naam + ' toegevoegd aan winkelmandje!');
}

function initializeCart() {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  updateCartCount();
}

function updateCartCount() {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  const cartCount = document.getElementById("cartCount");
  if (cartCount) cartCount.textContent = cart.length;
}

function removeFromCart(index) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  cart.splice(index, 1);
  localStorage.setItem("cart", JSON.stringify(cart));
  location.reload();
}

function displayCart() {
  const cartContainer = document.getElementById("cartItems");
  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  if (!cartContainer) return;
  
  if (cart.length === 0) {
    cartContainer.innerHTML = 'Je winkelmandje is leeg.';
    return;
  }

  let html = '<table><tr><th>Product</th><th>Prijs</th><th></th></tr>';
  let total = 0;

  cart.forEach((item, i) => {
    total += item.prijs;
    html += '<tr><td>' + item.naam + '</td><td>€' + item.prijs.toFixed(2) + '</td><td><button onclick="removeFromCart(' + i + ')">Verwijderen</button></td></tr>';
  });

  html += '</table><p>Totaal: €' + total.toFixed(2) + '</p>';
  cartContainer.innerHTML = html;
}

initializeCart();
displayCart();
