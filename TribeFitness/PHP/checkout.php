<?php
$conn = new mysqli("localhost", "root", "", "tribefitness_db");
if ($conn->connect_error) die("DB connection error");

// Customer details
$first = trim($_POST['first_name'] ?? '');
$last  = trim($_POST['last_name'] ?? '');
$name  = $first . ' ' . $last;

$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$city    = trim($_POST['city'] ?? '');
$postal  = trim($_POST['postal'] ?? '');
$notes   = trim($_POST['order_notes'] ?? '');
$payment = $_POST['payment_method'] ?? '';

// Cart validation
$product_ids   = $_POST['product_id'] ?? [];
$product_names = $_POST['product_name'] ?? [];
$qtys          = $_POST['qty'] ?? [];
$prices        = $_POST['price'] ?? [];

if (count($product_ids) === 0) die("Cart empty");

$products = [];
$total = 0;

// Stock validation
for ($i=0; $i<count($product_ids); $i++) {
    $pid = (int)$product_ids[$i];
    $qty = (int)$qtys[$i];
    $price = (float)$prices[$i];

    $stmt = $conn->prepare("SELECT quantity FROM products WHERE id=?");
    $stmt->bind_param("i",$pid);
    $stmt->execute();
    $stock = $stmt->get_result()->fetch_assoc()['quantity'];

    if ($qty > $stock) die("Stock changed. Please update your cart.");

    $products[] = ['id'=>$pid,'qty'=>$qty,'price'=>$price];
    $total += $qty * $price;
}

// Bank proof upload
$proofPath = null;
if ($payment === 'bank' && isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === 0) {

    // Allowed file types
    $allowed = ['jpg','jpeg','png','pdf'];
    $ext = strtolower(pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION));

    // Validate file type
    if (!in_array($ext, $allowed)) {
        die("Invalid file type. Only images or PDF allowed.");
    }

    // Limit file size to 5MB
    if ($_FILES['payment_proof']['size'] > 5*1024*1024) {
        die("File too large (max 5MB)");
    }

    // Ensure upload directory exists
    $uploadDir = "../uploads/payment_proofs/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    // Unique file name
    $newName = "proof_" . time() . "_" . bin2hex(random_bytes(5)) . "." . $ext;
    $proofPath = $uploadDir . $newName;

    // Move uploaded file
    if (!move_uploaded_file($_FILES['payment_proof']['tmp_name'], $proofPath)) {
        die("Failed to save uploaded file.");
    }
}


// Save order
$stmt = $conn->prepare("
INSERT INTO orders
(customer_name,email,phone,address,city,postal,
 payment_method,payment_status,payment_proof,
 products,product_names,total,notes)
VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending Verification', ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "ssssssssssds",
    $name,$email,$phone,$address,$city,$postal,
    $payment,$proofPath,json_encode($products),json_encode($product_names),$total,$notes
);

$stmt->execute();
$order_id = $conn->insert_id;

// Deduct stock
foreach($products as $p){
    $stmt = $conn->prepare("UPDATE products SET quantity = GREATEST(quantity-?,0) WHERE id=?");
    $stmt->bind_param("ii",$p['qty'],$p['id']);
    $stmt->execute();
}

header("Location: success.php?order_id=$order_id");
exit;
?>
