<?php
require "db.php";
session_start();

if (!isset($_SESSION['admin_id'])) {
    header ("Location: admin_login.php");
    exit;
}

function requireRole($roles) {
    if (!in_array($_SESSION['role'],(array)$roles)) {
        die ("Access denied");
}
}
?>