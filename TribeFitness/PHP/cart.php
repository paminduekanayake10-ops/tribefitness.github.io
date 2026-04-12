<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tribefitness_db";

// Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$data = json_decode(file_get_contents("php://input"), true);
$cart = $data['cart'] ?? [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Cart | TribeFitness</title>
<link rel="stylesheet" href="../style1.css">
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css" rel="stylesheet"/>
<meta name="description" content="TribeFitness cart page">
</head>
<body>

<header>
<nav class="nav">
  <div class="container">
    <div class="left-side-header">
      <a href="../index.html" class="logo">
        <img src="../resources/image/Designer.png" alt="logo">
      </a>
      <a href="../index.html" class="head">FitnessTribe</a>
    </div>

    <div class="hamburger" id="hamburger">
      <i class="ri-menu-line"></i>
    </div>

    <div class="right-side-header nav-links" id="nav-links">
      <a href="../index.html">Home</a>
      <a href="../membership.html">Membership</a>
      <a href="shop.php">Shop</a>
      <a href="../about-us.html">About Us</a>
    </div>
  </div>
</nav>
</header>

<h1 class="section-title">Your Cart</h1>

<div id="cart-items"></div>

<h3 class="total-text">
    <div class="total">
        Total: Rs.<span id="cart-total">0.00</span>
    </div>
  <button class="remove-btn" onclick="clearCart()">Clear Cart</button>
</h3>
 
   

   <div class="proceed-to-checkout">
      <button type="button"  id="checkoutBtn" class="btn-buy" onclick="goToCheckout()">Proceed to Checkout</button>
</div>

<footer class="footer">
  <div class="container2">
    <a href="track_order.php" style="text-decoration: none; color: #fff;">Track Your Order</a></p>
    <div>© 2025 TribeFitness — Kaduwela • Malabe • Nugegoda</div>
    <div><small>All rights reserved to TribeFitness PVT.LTD</small></div>
  </div>
</footer>

<script>
function getCart() {
  return JSON.parse(localStorage.getItem("cart")) || [];
}

function saveCart(cart) {
  localStorage.setItem("cart", JSON.stringify(cart));
}

function normalizeImg(path) {
  if (!path) return "/resources/image/placeholder.png";
  return path.replace(/\\\\/g, "/").replace("../", "/");
}

function renderCart() {
  const cart = getCart();
  const container = document.getElementById("cart-items");
  const totalEl = document.getElementById("cart-total");
  const checkoutBtn = document.getElementById("checkoutBtn");

  container.innerHTML = "";
  let total = 0;

  if (cart.length === 0) {
    container.innerHTML = "<p>Your cart is empty.</p>";
    totalEl.textContent = "0.00";
    checkoutBtn.disabled = true;
    checkoutBtn.style.opacity = "0.5";
    checkoutBtn.style.cursor = "not-allowed";
    return;
  }

  checkoutBtn.disabled = false;
  checkoutBtn.style.opacity = "1";
  checkoutBtn.style.cursor = "pointer";

  cart.forEach((item, index) => {
    total += item.price * item.qty;

    const stockText  = item.inStock ? "In Stock" : "Out of Stock";
    const stockClass = item.inStock ? "in-stock" : "out-stock";

    container.innerHTML += `
      <div class="cart-row">
        <img src="${normalizeImg(item.imgSrc)}" class="cart-img">

        <div class="cart-info">
          <strong>${item.name}</strong><br>
          Price: Rs.${item.price}<br>

          <span class="stock ${stockClass}">
            ${stockText}
          </span><br>

          Qty:
          <input type="number" min="1"  value="${item.qty}"
            onchange="updateQty(${index}, this.value, ${item.maxQty})">
        </div>

        <button class="remove-btn" onclick="removeItem(${index})">
          Remove
        </button>
      </div>
    `;
  });

  totalEl.textContent = total.toFixed(2);
}

// ✅ Enforce maxQty when user changes input
function updateQty(index, qty, maxQty) {
  const cart = getCart();
  qty = parseInt(qty);

     if (isNaN(qty) || qty < 1) {
    qty = 1;
  }
    
  if (qty > maxQty) {
    alert(`Maximum stock for this product is ${maxQty}`);
    qty = maxQty;
  } else if (qty < 1) {
    qty = 1;
  }

  cart[index].qty = qty;
  saveCart(cart);
  renderCart();
}

function removeItem(index) {
  const cart = getCart();
  cart.splice(index, 1);
  saveCart(cart);
  renderCart();
}

function clearCart() {
  localStorage.removeItem("cart");
  renderCart();
}

function goToCheckout() {
  const cart = getCart();
  if (cart.length === 0) {
    alert("Your cart is empty. Add items before checkout.");
    return;
  }
  window.location.href = "../checkout.html";
}

renderCart();

/* Hamburger */
const hamburger = document.getElementById("hamburger");
const navLinks = document.getElementById("nav-links");

hamburger?.addEventListener("click", () => {
  navLinks.classList.toggle("active");
});

fetch("cart.php", {
  method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify({ cart: getCart() })
});

</script>

</body>
</html>
