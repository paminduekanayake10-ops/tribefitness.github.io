<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin_login.php");
    exit;
}
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
?>
<style>
.admin-header {
  height: 60px;
  background: #222;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
}
.admin-header a {
  color: #fff;
  text-decoration: none;
  font-weight: bold;
}
.logout-btn {
  background: #f44336;
  padding: 6px 12px;
  border-radius: 4px;
}
</style>

<div class="admin-header">
  <div>Welcome, <?= htmlspecialchars($admin_name) ?></div>
  <a href="admin_logout.php" class="logout-btn">Logout</a>
</div>
