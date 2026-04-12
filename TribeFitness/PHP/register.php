<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tribefitness_db";

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Collect POST data
$first_name = $_POST['first_name'] ?? '';
$last_name  = $_POST['last_name'] ?? '';
$name       = $first_name . ' ' . $last_name;
$email      = $_POST['email'] ?? '';
$phone      = $_POST['phone'] ?? '';
$membership_type = $_POST['membership_type'] ?? '';
$payment_method  = $_POST['payment_method'] ?? '';

// Card fields
$card_number = $_POST['card_number'] ?? null;
$card_expiry = $_POST['expiry_date'] ?? null;
$card_cvv    = $_POST['cvv'] ?? null;

// Bank transfer / online payment
$bank_account = $_POST['bank_account'] ?? null;
$payment_proof = null;

// Handle payment proof upload
if(isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === 0){
    $targetDir = "../uploads/proofs/";
    if(!is_dir($targetDir)) mkdir($targetDir, 0755, true);

    $filename = time().'_'.basename($_FILES['payment_proof']['name']);
    $targetFile = $targetDir . $filename;

    if(move_uploaded_file($_FILES['payment_proof']['tmp_name'], $targetFile)){
        $payment_proof = $targetFile;
    }
}

// Mask card number (store last 4 digits only)
if ($payment_method === 'card' && $card_number) {
    $card_number = substr($card_number, -4);
} else {
    $card_number = null;
    $card_expiry = null;
    $card_cvv = null;
}

// Insert into DB
$stmt = $conn->prepare("INSERT INTO members 
(name, email, phone, membership_type, payment_method, card_number, card_expiry, card_cvv, bank_account, payment_proof)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param(
    "ssssssssss",
    $name, $email, $phone, $membership_type, $payment_method,
    $card_number, $card_expiry, $card_cvv, $bank_account, $payment_proof
);

if($stmt->execute()){
    $member_id = $stmt->insert_id;
    header("Location: registration_success.php?member_id=$member_id");
    exit();
} else {
    echo "Database error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
