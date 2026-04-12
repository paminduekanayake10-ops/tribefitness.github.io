
<style>
.admin-layout {
  display: flex;
  min-height: calc(100vh - 60px);
}

.admin-sidebar {
  width: 140px;
  background: #333;
  padding: 20px;
}

.admin-sidebar a {
  display: block;
  color: #fff;
  text-decoration: none;
  padding: 10px;
  margin-bottom: 10px;
  border-radius: 4px;
}

.admin-sidebar a:hover {
  background: #444;
}

.admin-content {
  flex: 1;
  padding: 20px;
  background: #f5f5f5;
}

.membership-icon {
    gap: 5px;
    color: #;
}

</style>

<div class="admin-layout">
  <div class="admin-sidebar">
    <a href="admin_dashboard.php">📊 Dashboard</a>
    <a href="admin_orders.php">🧾 Orders</a>
    <a href="admin_members.php"><img src="../resources/image/member-list.png" class="membership-icon" alt="members-icon"> Members</a>
    <a href="admin_manage.php">Admin Management</a>

  </div>

  <div class="admin-content">
