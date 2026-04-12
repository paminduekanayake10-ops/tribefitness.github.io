<?php
session_start();
$conn = new mysqli("localhost","root","","tribefitness_db");
if($conn->connect_error) die("DB Connection Error");

$error = '';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check admin table
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username=? LIMIT 1");
    $stmt->bind_param("s",$username);
    $stmt->execute();
    $result = $stmt->get_result();
    if($row = $result->fetch_assoc()){
        if(password_verify($password, $row['password'])){
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['name'];
            $_SESSION['role'] = $row['role'];
            header("Location: admin_dashboard.php");
            exit;
        } else { $error="Invalid password"; }
    } else { $error="Invalid username"; }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
<style>
h2 {text-align: center;}
body { font-family: Arial,sans-serif; background:#f5f5f5; display:flex; flex-direction:row; justify-content:center; align-items:center;margin-top: 200px; }
form { background:#fff; padding:20px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1); width:300px; }
input { width:100%; padding:10px; margin:5px 0; box-sizing: border-box; }
button { width:100%; padding:10px; background:#333; color:#fff; border:none; cursor:pointer; }
.error { color:red; margin-bottom:10px; }
.back { display:block; margin-top:10px; text-decoration:none; color:#333; }
</style>
</head>
<body>
<form method="POST">
<h2>Admin Login</h2>
<?php if($error) echo "<div class='error'>$error</div>"; ?>
<input type="text" name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit">Login</button>
<a class="back" href="admin_create.php">← Sign Up</a>
</form>
</body>
</html>

