<?php
$conn = new mysqli("localhost", "root", "", "tribefitness_db");
if ($conn->connect_error) die("Database Error");

$order = null;
$error = "";
$products = []; // initialize

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT * FROM orders WHERE id=? AND email=?");
    $stmt->bind_param("is", $order_id, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $order = $result->fetch_assoc();
        $products = json_decode($order['product_names'], true); // decode here
        if (!is_array($products)) $products = []; // fallback
    } else {
        $error = "Order not found. Please check Order ID and Email.";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
<title>Track Order | TribeFitness</title>
<style>
h2 {text-align: center;}
body { font-family: Arial; background:#f5f5f5; }
.container { max-width:500px; margin:40px auto; background:#fff; padding:20px 50px; border-radius:8px; }
input, button { width:100%; padding:10px; margin:10px 0; }
button { background:#333; color:#fff; border:none; cursor:pointer; }
.status { padding:10px; border-radius:6px; margin-top:10px; font-weight:bold; }
.Pending { background:#fff2cc; }
.Verified { background:#d9edf7; }
.Completed { background:#d4edda; }
.Cancelled { background:#f8d7da; }
.error { color:red; }
</style>
</head>
<body>

<div class="container">
<h2>Track Your Order</h2>

<form method="POST">
    <input type="number" name="order_id" placeholder="Order ID" required>
    <input type="email" name="email" placeholder="Email used in order" required>
    <button type="submit">Track Order</button>
</form>

<?php if($error): ?>
<p class="error"><?= $error ?></p>
<?php endif; ?>

<?php if($order): ?>
<hr>
<p><strong>Order ID:</strong> <?= $order['id'] ?></p>
<p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
<p><strong>Products:</strong></p>
<ul>
<?php if(is_array($products)): ?>
    <?php foreach($products as $p): ?>
        <li><?= htmlspecialchars($p) ?></li>
    <?php endforeach; ?>
<?php else: ?>
    <li>No products found</li>
<?php endif; ?>
</ul>
<p><strong>Total:</strong> Rs.<?= number_format($order['total'],2) ?></p>
<p><strong>Payment:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
<p class="status <?= $order['payment_status'] ?>">
    Status: <?= $order['payment_status'] ?>
</p>
<p><strong>Ordered on:</strong> <?= $order['created_at'] ?></p>
<?php endif; ?>
</div>

</body>
</html>


