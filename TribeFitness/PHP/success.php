<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tribefitness_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$order_id = intval($_GET['order_id'] ?? 0);

$sql = "SELECT * FROM orders WHERE id=$order_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
    $products = json_decode($order['products'], true);
    $product_names = json_decode($order['product_names'], true);
    if (!is_array($products)) $products = [];
    if (!is_array($product_names)) $product_names = [];
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success | TribeFitness</title>
    <link rel="stylesheet" href="../style1.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #FAF3E0;
            padding: 0;
            margin: 0;
        }
         
        .success-container {
            max-width: 700px;
            margin: 75px auto 50px; /* Push below fixed header */
            background: #fff;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
        }

        .success-container h1 {
            color: #FF6347;
            font-size: 40px;
            margin-bottom: 20px;
        }

        .success-container p {
            font-size: 18px;
            color: #333;
            margin: 10px 0;
        }

        .success-container ul {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }

        .success-container li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            font-size: 16px;
            color: #555;
        }

        .success-container strong {
            color: #111;
        }

        .total {
            margin-top: 20px;
            font-size: 20px;
            font-weight: 700;
            color: #111;
        }

        .btn-home {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 25px;
            background: #111;
            color: #00ff88;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-home:hover {
            background: #00ff88;
            color: #111;
        }

        footer {
            background: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
            width: 100%;
            position: relative;
            bottom: 0;
        }

        .branches {
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
        
    <div class="success-container">
        <h1>Thank You for Your Purchase!</h1>
        <p><strong>Order #:</strong> <?= htmlspecialchars($order['id']) ?></p>
        <p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?>, <?= htmlspecialchars($order['city']) ?> - <?= htmlspecialchars($order['postal']) ?></p>
        <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>

        <h3>Products Ordered:</h3>
        <?php if (count($products) > 0): ?>
            <ul>
                <?php for ($i = 0; $i < count($products); $i++): ?>
                    <li>
                        <?= htmlspecialchars($product_names[$i] ?? 'N/A') ?> - 
                        Qty: <?= intval($products[$i]['qty'] ?? 0) ?> - 
                        Price: Rs.<?= number_format(floatval($products[$i]['price'] ?? 0), 2) ?>
                    </li>
                <?php endfor; ?>
            </ul>
        <?php else: ?>
            <p>No products found in this order.</p>
        <?php endif; ?>

        <div class="total"><strong>Total:</strong> Rs.<?= number_format(floatval($order['total']), 2) ?></div>

        <a href="../index.html" class="btn-home">Back to Home</a>
    </div>

    <footer>
        <div class="branches">Copyright © 2025 TribeFitness — Kaduwela • Malabe • Nugegoda</div>
        &copy; 2025 TribeFitness — All rights reserved
    </footer>

    <script>
        // Clear the cart after successful purchase
        localStorage.removeItem('cart');
    </script>
</body>
</html>
<?php
} else {
    echo "<h2>Order not found.</h2>";
}

$conn->close();
?>
