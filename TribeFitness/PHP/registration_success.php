<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tribefitness_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$member_id = intval($_GET['member_id'] ?? 0);

// Fetch member data
$sql = "SELECT * FROM members WHERE id = $member_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $m = $result->fetch_assoc();

    // Map DB payment method to readable label
    $paymentMethods = [
        'cash-on-delivery' => 'Cash On Delivery',
        'card'             => 'Card',
        'bank-transfer'    => 'Bank Transfer'
    ];

    $displayPaymentMethod = $paymentMethods[$m['payment_method']] ?? 'Unknown';
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Success | TribeFitness</title>
    <link rel="stylesheet" href="../style1.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #FAF3E0; margin: 0; padding: 0; }
        .success-container { max-width:700px; margin:40px auto; background:#fff; padding:40px 30px; border-radius:12px; box-shadow:0 5px 20px rgba(0,0,0,0.1); text-align:center; }
        .success-container h1 { color:#FF6347; font-size:40px; margin-bottom:20px; }
        .success-container p { font-size:18px; color:#333; margin:10px 0; }
        .section-title { margin-top:30px; font-size:24px; font-weight:600; color:#FF6347; text-decoration:none; }
        .btn-home { display:inline-block; margin-top:30px; padding:12px 25px; background:#111; color:#00ff88; border-radius:8px; text-decoration:none; font-weight:600; transition:0.3s; }
        .btn-home:hover { background:#00ff88; color:#111; }
        footer { background:#333; color:#fff; padding:20px; text-align:center; width:100%; position:relative; bottom:0; }
        .branches { padding-bottom:10px; }
    </style>
</head>
<body>

<div class="success-container">
    <h1>Registration Successful!</h1>

    <p><strong>Member ID:</strong> <?= htmlspecialchars($m['id']) ?></p>
    <p><strong>Name:</strong> <?= htmlspecialchars($m['name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($m['email']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($m['phone']) ?></p>
    <p><strong>Membership Type:</strong> <?= htmlspecialchars(ucwords($m['membership_type'])) ?></p>
    <p><strong>Payment Method:</strong> <?= htmlspecialchars($displayPaymentMethod) ?></p>

    <div class="section-title">Payment Details</div>

    <?php if ($m['payment_method'] === 'card'): ?>
        <p><strong>Card Last 4 Digits:</strong> <?= htmlspecialchars($m['card_number']) ?></p>
        <p><strong>Expiry:</strong> <?= htmlspecialchars($m['card_expiry']) ?></p>
        <p><strong>CVV:</strong> <?= htmlspecialchars($m['card_cvv']) ?></p>
    <?php elseif ($m['payment_method'] === 'bank-transfer'): ?>
        <p><strong>Bank Account:</strong> <?= htmlspecialchars($m['bank_account']) ?></p>
        <?php if($m['payment_proof']): 
            $ext = strtolower(pathinfo($m['payment_proof'], PATHINFO_EXTENSION));
        ?>
        <?php endif; ?>
    <?php elseif ($m['payment_method'] === 'cash-on-delivery'): ?>
        <p>No additional payment details. Pay on arrival.</p>
    <?php else: ?>
        <p>No payment details available.</p>
    <?php endif; ?>

    <a href="../index.html" class="btn-home">Back to Home</a>
</div>

<footer>
    <div class="branches">Copyright © 2025 TribeFitness — Kaduwela • Malabe • Nugegoda</div>
    &copy; 2025 TribeFitness — All rights reserved
</footer>

</body>
</html>
<?php
} else {
    echo "<h2>Member not found.</h2>";
}

$conn->close();
?>
