<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin_login.php");
    exit;
}

$conn = new mysqli("localhost","root","","tribefitness_db");
if($conn->connect_error) die("DB Connection Error");

$can_manage_admins = ($_SESSION['role'] === 'manager');

// Orders
$total_sales = $conn->query("SELECT SUM(total) AS s FROM orders WHERE payment_status='Completed'")->fetch_assoc()['s'] ?? 0;
$pending_orders = $conn->query("SELECT COUNT(*) AS c FROM orders WHERE payment_status='Pending'")->fetch_assoc()['c'] ?? 0;
$recent_orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");

// Members
$total_members = $conn->query("SELECT COUNT(*) AS c FROM members")->fetch_assoc()['c'] ?? 0;
$pending_members = $conn->query("SELECT COUNT(*) AS c FROM members WHERE status='Pending'")->fetch_assoc()['c'] ?? 0;
$recent_members = $conn->query("SELECT * FROM members ORDER BY registered_at DESC LIMIT 5");

$admin_name = $_SESSION['admin_name'];
?>

<?php include 'admin_header.php'; ?>
<?php include 'admin_sidebar.php'; ?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<style>
body { margin:0; padding:0; background:#f5f5f5; }
.dashboard { display:flex; gap:20px; padding:20px; flex-wrap:wrap; }
.card { background:#fff; padding:20px; border-radius:8px; flex:1; min-width:200px; box-shadow:0 0 10px rgba(0,0,0,0.1);}
.card h2 { margin-bottom:10px; }
table { width: 100%; border-collapse: collapse; background:#fff; }
th, td { border:1px solid #ccc; padding:8px; text-align:left; }
th { background:#f0f0f0; }
button { padding:4px 8px; cursor:pointer; }
</style>
</head>
<body>

<div class="dashboard">
    <div class="card">
        <h2>Total Sales</h2>
        <p>Rs.<?= number_format($total_sales,2) ?></p>
    </div>
    <div class="card">
        <h2>Pending Orders</h2>
        <p><?= $pending_orders ?></p>
    </div>
    <div class="card">
        <h2>Total Members</h2>
        <p><?= $total_members ?></p>
    </div>
    <div class="card">
        <h2>Pending Members</h2>
        <p><?= $pending_members ?></p>
    </div>
</div>

<h2 style="padding:0 20px;">Recent 5 Orders</h2>
<table style="margin:0 20px;">
<tr><th>ID</th><th>Customer</th><th>Email</th><th>Total</th><th>Status</th></tr>
<?php while($order = $recent_orders->fetch_assoc()): ?>
<tr>
<td><?= $order['id'] ?></td>
<td><?= htmlspecialchars($order['customer_name']) ?></td>
<td><?= htmlspecialchars($order['email']) ?></td>
<td>Rs.<?= number_format($order['total'],2) ?></td>
<td><?= $order['payment_status'] ?></td>
</tr>
<?php endwhile; ?>
</table>

</body>
</html>
