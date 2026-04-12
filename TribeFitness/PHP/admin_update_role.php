<?php
require_once 'auth.php';
requireRole(['admin','manager']);

$conn = new mysqli("localhost","root","","tribefitness_db");

$id = (int)$_POST['id'];
$role = $_POST['role'];

$stmt = $conn->prepare("UPDATE admins SET role=? WHERE id=?");
$stmt->bind_param("si",$role,$id);
$stmt->execute();

header("Location: admin_manage.php");
exit;
