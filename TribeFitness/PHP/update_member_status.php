<?php
session_start();
require "auth.php";
requireRole(['admin','cashire','manager']);

$conn = new mysqli("localhost","root","","tribefitness_db");
if($conn->connect_error) die("DB Connection Error");

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = intval($_POST['member_id']);
    $current_status = $_POST['current_status'] ?? '';

    // Update logic using your actual statuses
    if ($current_status === 'Pending') {
        $next_status = 'Verified';
    } elseif ($current_status === 'Verified') {
        $next_status = 'Cancelled';
    } else { // Cancelled
        $next_status = 'Verified';
    }

    $stmt = $conn->prepare("UPDATE members SET status=? WHERE id=?");
    $stmt->bind_param("si", $next_status, $member_id);

    if($stmt->execute()) {
        header("Location: admin_members.php");
        exit;
    } else {
        die("Database error: " . $conn->error);
    }
}
?>