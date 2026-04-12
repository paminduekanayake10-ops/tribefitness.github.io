document.addEventListener('DOMContentLoaded', () => {

  // ---------- Hamburger Menu ----------
  const hamburger = document.getElementById("hamburger");
  const navLinks = document.getElementById("nav-links");

  if (hamburger && navLinks) {
    hamburger.addEventListener("click", () => {
      navLinks.classList.toggle("active");
      const icon = hamburger.querySelector("i");
      icon.classList.toggle("ri-close-line");
      icon.classList.toggle("ri-menu-line");
    });

    document.querySelectorAll("#nav-links a").forEach(link => {
      link.addEventListener("click", () => {
        navLinks.classList.remove("active");
        const icon = hamburger.querySelector("i");
        icon.classList.remove("ri-close-line");
        icon.classList.add("ri-menu-line");
      });
    });
  }

  // ---------- Page Detection ----------
  const page = window.location.pathname.split("/").pop();
  if (!["shop.php", "checkout.html"].includes(page)) return;

  // ---------- Cart Elements ----------
  const cartIcon = document.querySelector("#cart-icon");
  const cartEl = document.querySelector(".cart");
  const cartClose = document.querySelector("#cart-close");
  const cartContent = document.querySelector(".cart-content");
  const totalPriceElement = document.querySelector(".total-price");
  const cartItemCountBadge = document.querySelector(".cart-item-count");
  const checkoutBtn = document.getElementById("checkout-btn");

  console.log('Cart elements:', {cartIcon, cartEl, cartClose, cartContent, totalPriceElement, cartItemCountBadge, checkoutBtn});

  // Load cart from localStorage
  let cartItems = JSON.parse(localStorage.getItem("cart") || "[]");
  cartItems.forEach(item => {
    if (!item.name) item.name = 'Unknown Product';
  });
  console.log('Loaded cart items:', cartItems);

  // ---------- Helper Functions ----------
  function saveCart() {
    localStorage.setItem("cart", JSON.stringify(cartItems));
  }
    
function updateCartCount() {
  if (!cartItemCountBadge) return;

  const itemCount = cartItems.length; // ✅ number of products

  if (itemCount > 0) {
    cartItemCountBadge.style.visibility = "visible";
    cartItemCountBadge.textContent = itemCount;
  } else {
    cartItemCountBadge.style.visibility = "hidden";
    cartItemCountBadge.textContent = "";
  }
}


  function updateTotalPrice() {
    if (!totalPriceElement) return;
    const total = cartItems.reduce((sum, item) => sum + item.price * item.qty, 0);
    totalPriceElement.textContent = `Rs ${total.toFixed(2)}`;
  }

  function recalcAndRender() {
    renderCart();
    updateTotalPrice();
    updateCartCount();
    saveCart();
  }

  // ---------- Cart Show/Hide ----------
  cartIcon?.addEventListener("click", () => {
    console.log('Cart icon clicked');
    cartEl?.classList.add("active");
  });
  cartClose?.addEventListener("click", () => {
    console.log('Cart close clicked');
    cartEl?.classList.remove("active");
  });

// ---------- Add to Cart ----------
document.querySelectorAll(".add-cart").forEach(btn => {
  btn.addEventListener("click", e => {
    const productBox = e.target.closest(".product-box");
    if (!productBox) return;

    const productId = productBox.dataset.id;
    const maxQty = parseInt(productBox.dataset.maxqty, 10) || 0;

    // ❌ Check stock
    if (maxQty <= 0) {
      alert("This product is out of stock");
      return;
    }

    // ✅ Only add once
    const existing = cartItems.find(it => it.id === productId);
    if (existing) {
      return;
    }

    const productName = productBox.dataset.name;
    const priceNum = parseFloat(productBox.dataset.price);
    const imgSrc = productBox.dataset.img || "";
    const inStock = productBox.querySelector(".in-stock") !== null;

    cartItems.push({
      id: productId,
      name: productName,
      price: priceNum,
      qty: 1,
      maxQty: maxQty,
      imgSrc,
      inStock
    });

    recalcAndRender();
  });
});


// ---------- Render Cart ----------
function renderCart() {
  if (!cartContent) return;
  cartContent.innerHTML = "";

  if (cartItems.length === 0) {
    cartContent.innerHTML = "<p>Your cart is empty.</p>";
    totalPriceElement.textContent = "Rs 0.00";
    return;
  }

  cartItems.forEach(item => {
    const box = document.createElement("div");
    box.className = "cart-box";

    box.innerHTML = `
      <img src="${item.imgSrc}" class="cart-img" alt="${item.name}">
      <div class="cart-detail">
        <h2 class="cart-product-title">${item.name}</h2>
        <span class="cart-price">Rs.${item.price.toFixed(2)}</span>
        <div class="cart-quantity">
          <button class="decrement">-</button>
          <span class="number">${item.qty}</span>
          <button class="increment">+</button>
        </div>
      </div>
      <i class="ri-delete-bin-line cart-delete"></i>
    `;

    cartContent.appendChild(box);

  // Increment
  box.querySelector(".increment")?.addEventListener("click", () => {
  if (item.qty < item.maxQty) {
    item.qty += 1;
    recalcAndRender();
  } else {
    alert("Maximum stock reached");
  }
});

// Decrement
box.querySelector(".decrement")?.addEventListener("click", () => {
  if (item.qty > 1) item.qty -= 1;
  else cartItems = cartItems.filter(ci => ci.id !== item.id);
  recalcAndRender();
});

    // Delete
    box.querySelector(".cart-delete")?.addEventListener("click", () => {
      cartItems = cartItems.filter(ci => ci.id !== item.id);
      recalcAndRender();
    });
  });

  // Update total price
  updateTotalPrice();
  updateCartCount();
  saveCart();
}


  // ---------- Checkout ----------
  checkoutBtn?.addEventListener("click", () => {
    if (cartItems.length === 0) {
      alert("Your cart is empty!");
      return;
    }
    localStorage.setItem("cart", JSON.stringify(cartItems));
    window.location.href = "cart.php";
  });

  // ---------- Clear Cart ----------
  const clearCartBtn = document.getElementById("clear-cart-btn");
  clearCartBtn?.addEventListener("click", () => {
    cartItems = [];
    recalcAndRender();
  });

  // Initial render
  recalcAndRender();
});
