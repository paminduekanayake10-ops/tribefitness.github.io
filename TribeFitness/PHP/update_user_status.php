<?php
session_start();
require 'permissions.php';

if (!canAccess('members')) {
    die("⛔ Access Denied");
}?>
<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) exit;

$conn = new mysqli("localhost","root","","tribefitness_db");

if($_SERVER['REQUEST_METHOD']=='POST'){
    $id = intval($_POST['user_id']);
    $status = $_POST['status'] === 'Active' ? 'Active' : 'Blocked';
    $conn->query("UPDATE users SET status='$status' WHERE id=$id");
    header("Location: admin_dashboard.php");
    exit;
}
?>
