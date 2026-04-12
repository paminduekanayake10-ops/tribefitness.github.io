<?php
require 'auth.php';

if (requireRole('manager','admin')) {
    die("⛔ Access Denied");
}
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

$conn = new mysqli("localhost","root","","tribefitness_db");
if ($conn->connect_error) die("DB Error");

$order_id = (int)$_POST['order_id'];
$status = $_POST['status'] ?? '';
$allowed_status = ['Pending','Verified','Completed','Cancelled'];

if (!in_array($status, $allowed_status)) die("Invalid status");

$stmt = $conn->prepare("UPDATE orders SET payment_status=? WHERE id=?");
$stmt->bind_param("si",$status,$order_id);
$stmt->execute();

header("Location: admin_orders.php");
exit;
?>