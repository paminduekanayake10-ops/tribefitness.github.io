<?php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tribefitness_db";b";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and collect input
    $first_name = $conn->real_escape_string($_POST['first-name']);
    $last_name = $conn->real_escape_string($_POST['last-name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone_number = $conn->real_escape_string($_POST['phone-number']);
    $message = $conn->real_escape_string($_POST['message']);
    
    // Insert data into table
    $sql = "INSERT INTO contact_form (first_name, last_name, email, phone_number, message)
            VALUES ('$first_name', '$last_name', '$email', '$phone_number', '$message')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Form submitted successfully!'); window.location.href='about-us.html';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    $conn->close();
}
?>
