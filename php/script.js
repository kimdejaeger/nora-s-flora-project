const productGrid = document.getElementById("producten");

function toonPlanten() {
  if (!productGrid) return;

  productGrid.innerHTML = "";

  planten.forEach((plant) => {
    const productDiv = document.createElement("div");
    productDiv.classList.add("product");

    if (plant.groot) {
      productDiv.classList.add("product-large");
    }

    productDiv.innerHTML = `
      <img src="${plant.afbeelding}" alt="Foto van ${plant.naam}" loading="lazy">
      <p>${plant.naam}<br />€${plant.prijs.toFixed(2)}</p>
      <button class="cart-button" onclick="voegToe(${plant.id})">
        🛒 Bestellen
      </button>
    `;

    productGrid.appendChild(productDiv);
  });
}

if (productGrid) {
  toonPlanten();
}

function voegToe(id) {
  console.log("Plant met ID " + id + " is toegevoegd!");
  addToCart(id);
}

function addToCart(id) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  const product = planten.find((p) => p.id === id);

  if (product) {
    cart.push(product);
    localStorage.setItem("cart", JSON.stringify(cart));
    updateCartCount();
  }
}

function updateCartCount() {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  const cartCountElement = document.getElementById("cartCount");
  if (cartCountElement) {
    cartCountElement.innerText = `(${cart.length})`;
  }
}

function initializeCart() {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  let total = 0;

  cart.forEach((item) => {
    total += item.prijs;
  });

  console.log("Cart total: €" + total.toFixed(2));
  updateCartCount();
}

initializeCart();

function removeFromCart(index) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  cart.splice(index, 1);
  localStorage.setItem("cart", JSON.stringify(cart));
  location.reload();
}

function displayCart() {
  const cartContainer = document.getElementById("cartItems");
  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  if (cart.length === 0) {
    cartContainer.innerHTML =
      '<div class="empty-cart">Je winkelmandje is leeg. <br><a href="assortiment.html">Ga naar assortiment</a></div>';
    return;
  }

  let cartHTML = "<table><tr><th>Product</th><th>Prijs</th></tr>";
  let total = 0;

  cart.forEach((item, index) => {
    cartHTML += `
             <tr>
                <td>${item.naam}</td>
                <td>€${item.prijs.toFixed(2)}</td>
                     
            </tr>
        `;
    total += item.prijs;
  });

  cartHTML += `</table><div class="cart-total">Totaal: €${total.toFixed(2)}</div>`;
  cartContainer.innerHTML = cartHTML;
}

function clearCart() {
  if (confirm("Weet je zeker dat je je winkelmandje wilt leegmaken?")) {
    localStorage.removeItem("cart");
    displayCart();
  }
}

displayCart();
