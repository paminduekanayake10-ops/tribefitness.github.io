<?php

$conn = new mysqli("localhost","root","","tribefitness_db");
if($conn->connect_error) die("DB Error");

$result = $conn->query("SELECT * FROM admins ORDER BY created_at DESC");
?>

<?php include 'admin_header.php'; ?>
<?php include 'admin_sidebar.php'; ?>

<style>
.table-wrapper { padding:30px; }
table { width:100%; border-collapse:collapse; background:#fff; }
th,td { padding:10px; border:1px solid #ddd; }
th { background:#f4f4f4; }
.badge { padding:4px 8px; border-radius:4px; font-size:13px; }
.Active { background:#d4edda; color:#155724; }
.Inactive { background:#f8d7da; color:#721c24; }
.Super { background:#cce5ff; }
.Admin { background:#e2e3e5; }
.Moderator { background:#fff3cd; }
button { padding:6px 10px; cursor:pointer; border:none; }
.btn-activate { background:#28a745; color:white; }
.btn-deactivate { background:#dc3545; color:white; }
.btn-delete { background:#000; color:white; }
select { padding:4px; }
</style>

<div class="table-wrapper">
<h2>Admin Management</h2>

<a href="admin_create.php" style="margin-bottom:10px; display:inline-block;">➕ Add Admin</a>

<table>
<tr>
<th>ID</th>
<th>Name</th>
<th>Username</th>
<th>Role</th>
<th>Status</th>
<th>Created</th>
<th>Actions</th>
</tr>

<?php while($a = $result->fetch_assoc()): ?>
<tr>
<td><?= $a['id'] ?></td>
<td><?= htmlspecialchars($a['name']) ?></td>
<td><?= htmlspecialchars($a['username']) ?></td>

<td>
<form method="POST" action="admin_update_role.php">
<input type="hidden" name="id" value="<?= $a['id'] ?>">
<select name="role" onchange="this.form.submit()">
    <option>Super Admin</option>
    <option>Admin</option>
    <option>Moderator</option>
</select>
</form>
</td>

<td>
<span class="badge <?= $a['status'] ?>">
<?= $a['status'] ?>
</span>
</td>

<td><?= $a['created_at'] ?></td>

<td>
<?php if($a['status']=='Active'): ?>
<form method="POST" action="admin_toggle_status.php" style="display:inline;">
<input type="hidden" name="id" value="<?= $a['id'] ?>">
<button class="btn-deactivate">Deactivate</button>
</form>
<?php else: ?>
<form method="POST" action="admin_toggle_status.php" style="display:inline;">
<input type="hidden" name="id" value="<?= $a['id'] ?>">
<button class="btn-activate">Activate</button>
</form>
<?php endif; ?>

<?php if($a['role']!='Super Admin'): ?>
<form method="POST" action="admin_delete.php" style="display:inline;">
<input type="hidden" name="id" value="<?= $a['id'] ?>">
<button class="btn-delete" onclick="return confirm('Delete admin?')">Delete</button>
</form>
<?php endif; ?>
</td>
</tr>
<?php endwhile; ?>
</table>
</div>
