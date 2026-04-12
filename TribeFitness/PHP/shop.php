<?php
// shop.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tribefitness_db";

// Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// --------------------
// FILTER LOGIC
// --------------------
$search = $_GET['search'] ?? '';
$price  = $_GET['price'] ?? '';
$stock  = $_GET['stock'] ?? '';
$type = $_GET['type'] ?? '';

$sql = "SELECT id, name, quantity, price, img, type FROM products WHERE 1";

if (!empty($search)) {
    $safeSearch = $conn->real_escape_string($search);
    $sql .= " AND name LIKE '%$safeSearch%'";
}

if (!empty($price)) {
    if ($price === 'low') $sql .= " AND price < 2000";
    elseif ($price === 'mid') $sql .= " AND price BETWEEN 2000 AND 3000";
    elseif ($price === 'high') $sql .= " AND price > 3000";
}

if ($stock === 'in') $sql .= " AND quantity > 0";
elseif ($stock === 'out') $sql .= " AND quantity = 0";

if (!empty($type)) {
    $safeType = $conn->real_escape_string($type);
    $sql .= " AND type = '$safeType'";
}

$result = $conn->query($sql);

$productsByType = [];
while ($row = $result->fetch_assoc()) {
    $productsByType[$row['type']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TribeFitness - Shop</title>
<link rel="stylesheet" href="../style1.css">
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css" rel="stylesheet"/>
</head>
<body>
<header>
<nav class="nav">
    <div class="container">
        <div class="left-side-header">
            <a href="../index.html" class="logo"><img src="../resources/image/Designer.png" alt="logo"></a>
            <a href="../index.html" class="head">FitnessTribe</a>
        </div>
        <div class="hamburger" id="hamburger"><i class="ri-menu-line"></i></div>
        <div class="right-side-header nav-links" id="nav-links">
            <a href="../index.html">Home</a>
            <a href="../membership.html">Membership</a>
            <a href="shop.php">Shop</a>
            <a href="../about-us.html">About Us</a>
            <div id="cart-icon"><i class="ri-shopping-cart-fill"></i><span class="cart-item-count"></span></div>
        </div>
    </div>
</nav>
</header>

<!-- Cart Section -->
<div class="cart">
    <h2 class="cart-title">Your Cart</h2>
    <div class="cart-content"></div>
    <div class="total">
        <div class="total-title">Total</div>
        <div class="total-price">Rs.0.00</div>
    </div><br>
    <button id="checkout-btn" class="btn-buy">View Cart</button>
    <i class="ri-close-large-fill" id="cart-close"></i>
</div>

<h1 class="section-title">Gym Products</h1>

<!-- Filter -->
<form method="get" class="filter-form">
    <input type="text" name="search" placeholder="Search products..."
           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    <select name="type">
        <option value="">All Types</option>
        <option value="protein" <?= (($_GET['type'] ?? '')=='protein') ? 'selected' : '' ?>>Proteins</option>
        <option value="vitamin" <?= (($_GET['type'] ?? '')=='vitamin') ? 'selected' : '' ?>>Vitamins</option>
        <option value="capsules" <?= (($_GET['type'] ?? '')=='capsules') ? 'selected' : '' ?>>Capsules</option>
        <option value="other supplement" <?= (($_GET['type'] ?? '')=='other supplement') ? 'selected' : '' ?>>Other Supplements</option>
    </select>
    <select name="price">
        <option value="">All Prices</option>
        <option value="low"  <?= (($_GET['price'] ?? '')=='low')  ? 'selected' : '' ?>>Below Rs.2000</option>
        <option value="mid"  <?= (($_GET['price'] ?? '')=='mid')  ? 'selected' : '' ?>>Rs.2000 - 3000</option>
        <option value="high" <?= (($_GET['price'] ?? '')=='high') ? 'selected' : '' ?>>Above Rs.3000</option>
    </select>
    <select name="stock">
        <option value="">All Stock</option>
        <option value="in" <?= (($_GET['stock'] ?? '')=='in') ? 'selected' : '' ?>>In Stock</option>
        <option value="out" <?= (($_GET['stock'] ?? '')=='out') ? 'selected' : '' ?>>Out of Stock</option>
    </select>
    <button type="submit">Filter</button>
</form>

<!-- Shop Products -->
<section class="shop">
<div class="product-content-block">
<?php if (!empty($productsByType)): ?>
    <?php foreach ($productsByType as $typeName => $items): ?>
    <div class="product-type-block">
        <h2 class="type-title"><?= ucfirst($typeName) ?></h2>
        <div class="product-type-container">
            <?php foreach ($items as $prod): ?>
                <div class="product-box"
                        data-id="<?= $prod['id'] ?>"
                        data-name="<?= htmlspecialchars($prod['name'], ENT_QUOTES) ?>"
                        data-price="<?= $prod['price'] ?>"
                        data-img="<?= $prod['img'] ?>"
                    	data-maxqty="<?= $prod['quantity'] ?>">

                    <div class="image-box">
                        <img src="<?= $prod['img'] ?>" alt="<?= htmlspecialchars($prod['name']) ?>">
                    </div>
                    <h3 class="product-title"><?= htmlspecialchars($prod['name']) ?></h3>
                    <?php if ($prod['quantity'] > 0): ?>
                        <p class="stock in-stock">In Stock</p>
                            <?php else: ?>
                            <p class="stock out-stock">Out of Stock</p>
                        <?php endif; ?>

                    <div class="price-of-cart">
                        <span class="price">Rs.<?= number_format($prod['price'], 2) ?></span>
                        <?php if ($prod['quantity'] > 0): ?>
                            <i class="ri-shopping-cart-fill add-cart"></i>
                        <?php else: ?>
                            <span class="out-of-stock">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>

<?php else: ?>
    <p class="no-products">No products found matching your criteria.</p>
<?php endif; ?>
</div>
</section>

<div class="social-media">
    <a href="https://www.facebook.com/" target="_blank"><i class="ri-facebook-line"></i></a>
    <a href="https://www.instagram.com/" target="_blank"><i class="ri-instagram-line"></i></a>
    <a href="https://x.com/" target="_blank"><i class="ri-twitter-x-fill"></i></a>
    <a href="https://www.linkedin.com/" target="_blank"><i class="ri-linkedin-box-line"></i></a>
    <a href="https://www.tiktok.com/" target="_blank"><i class="ri-tiktok-line"></i></a>
    <a href="https://www.youtube.com/" target="_blank"><i class="ri-youtube-line"></i></a>
  </div>
  
<footer class="footer">
<div class="container">
    <a href="track_order.php" style="text-decoration: none; color: #fff;">Track Your Order</a></p>
    <div>© 2025 TribeFitness — Kaduwela • Malabe • Nugegoda</div>
    <div><small>All rights reserved to TribeFitness PVT.LTD</small></div>
</div>
</footer>

<script src="../resources/JS/script.js"></script>
</body>
</html>
