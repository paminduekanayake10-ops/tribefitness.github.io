<?php
// Database configuration
$host = "localhost";
$username = "root";        // default XAMPP username
$password = "";            // default XAMPP password is empty
$database = "tribefitness_db";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set charset
$conn->set_charset("utf8mb4");
?>