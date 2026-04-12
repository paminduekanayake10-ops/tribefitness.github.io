<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: admin_login.php");
  exit;
}

$conn = new mysqli("localhost","root","","tribefitness_db");
if ($conn->connect_error) die("DB Error");

$filter = $_GET['status'] ?? 'all';
$where = ($filter !== 'all') ? "WHERE payment_status='$filter'" : "";
$result = $conn->query("SELECT * FROM orders $where ORDER BY created_at DESC");
?>

<?php include 'admin_header.php'; ?>
<?php include 'admin_sidebar.php'; ?>
<style>
  *{margin: 0;padding: 0;}
  th, td { border:1px solid #ccc; padding:8px; text-align:left; }
  th { background:#f0f0f0; }
</style>



<h1>Orders</h1>

<table style="width: 100%; border-collapse: collapse; background:#fff;">
<tr>
<th>ID</th><th>Customer</th><th>Address</th><th>Products</th><th>Total</th><th>Status</th><th>Proof</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['customer_name']) ?></td>
<td><?= htmlspecialchars($row['address']) ?></td>
<td><?= implode(", ", array: json_decode($row['product_names'],true)) ?></td>
<td>Rs.<?= number_format($row['total'],2) ?></td>
<td><?= $row['payment_status'] ?></td>
<td>
<?php if($row['payment_proof']): ?>
<a href="<?= $row['payment_proof'] ?>" target="_blank">View</a>
<?php endif; ?>
</td>
<td>
<form action="update_order.php" method="POST">
<input type="hidden" name="order_id" value="<?= $row['id'] ?>">
<select name="status">
<option>Pending</option>
<option>Verified</option>
<option>Completed</option>
<option>Cancelled</option>
</select>
<button>Update</button>
</form>
</td>
</tr>
<?php endwhile; ?>
</table>

</div></div>
