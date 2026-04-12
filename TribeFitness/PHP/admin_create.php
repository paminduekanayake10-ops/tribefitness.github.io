<?php
require "auth.php";


$conn = new mysqli("localhost", "root", "", "tribefitness_db");
if ($conn->connect_error) die("DB Connection Error");

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $name     = trim($_POST['name'] ?? '');
    $role     = trim($_POST['role'] ?? '');

    if (!$username || !$password || !$name) {
        $error = "All fields are required.";
    } else {
        // Check if username exists
        $stmt = $conn->prepare("SELECT id FROM admins WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "Username already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO admins (username, password, name, role) VALUES (?, ?, ?,?)");
            $stmt->bind_param("ssss", $username, $hash, $name,$role);
            if ($stmt->execute()) {
                $success = "Admin created successfully!";
            } else {
                $error = "Database error: Could not create admin.";
            }
        }
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create New Admin | TribeFitness</title>
<style>
h2 {text-align: center;}
body { font-family: Arial, sans-serif; background:#f4f4f4; display:flex; justify-content:center; align-items:center; height:100vh; }
.box { background:#fff; padding:20px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.2); width:350px; }
input { width:100%; padding:8px; margin:8px 0; box-sizing:border-box; }
button { padding:8px 12px; width:100%; background:#333; color:#fff; border:none; border-radius:4px; cursor:pointer; }
button:hover { background:#555; }
.error { color:red; margin:5px 0; }
.success { color:green; margin:5px 0; }
.back { display:block; margin-top:10px; text-decoration:none; color:#333; }
.role {width:100%; padding:8px; margin:8px 0; box-sizing:border-box;}
</style>
</head>
<body>
<div class="box">
    <h2>Create New Admin</h2>

    <?php if($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if($success): ?><div class="success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" class="role">
            <option value="manager">Manager</option>
            <option value="admin">Admin</option>
            <option value="cashire">Cashire</option>
        </select><br>
        <button type="submit">Sign Up</button>
    </form>
    <a href="admin_orders.php" class="back">← Back to sign in</a>
</div>
</body>
</html>
