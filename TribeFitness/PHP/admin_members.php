<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin_login.php");
    exit;
}


$conn = new mysqli("localhost", "root", "", "tribefitness_db");
if ($conn->connect_error) die("DB Connection Error");

// Fetch all members
$result = $conn->query("SELECT * FROM members ORDER BY registered_at DESC");
?>

<?php include 'admin_header.php'; ?>
<?php include 'admin_sidebar.php'; ?>

<style>
body{padding: 0; margin: 0;}
.table-wrapper { padding: 30px; }
table { width: 100%; border-collapse: collapse; background:#fff; }
th, td { border:1px solid #ccc; padding:8px; text-align:left; }
th { background:#f0f0f0; }
img { max-width:50px; border:1px solid #ccc; padding:2px; }
button { padding:4px 8px; cursor:pointer; }
.status-Pending { background:#fff2cc; padding:4px 6px; border-radius:4px; }
.status-Active { background:#d4edda; padding:4px 6px; border-radius:4px; }
.status-Pending  { background:#fff3cd; color:#856404; }
.status-Active   { background:#d4edda; color:#155724; }
.status-Inactive { background:#f8d7da; color:#721c24; }
.status-btn {
    padding: 6px 14px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 14px;
    border: none;
    cursor: pointer;
    transition: all 0.25s ease;
}

/* Activate (Pending → Active / Inactive → Active) */
.btn-activate {
    background: #28a745;
    color: #fff;
}

.btn-activate:hover {
    background: #218838;
}

/* Deactivate (Active → Inactive) */
.btn-deactivate {
    background: #dc3545;
    color: #fff;
}

.btn-deactivate:hover {
    background: #c82333;
}

/* Pending badge */
.status-Pending {
    background: #fff3cd;
    color: #856404;
    padding: 4px 8px;
    border-radius: 4px;
}

/* Active badge */
.status-Active {
    background: #d4edda;
    color: #155724;
    padding: 4px 8px;
    border-radius: 4px;
}

/* Inactive badge */
.status-Inactive {
    background: #f8d7da;
    color: #721c24;
    padding: 4px 8px;
    border-radius: 4px;
}

</style>

<div class="table-wrapper">
<h2>Gym Members</h2>
<table>
<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Phone</th>
<th>Membership</th>
<th>Payment Method</th>
<th>Status</th>
<th>Payment Proof</th>
<th>Registered On</th>
<th>Action</th>
</tr>

<?php while($member = $result->fetch_assoc()): 
$ext = strtolower(pathinfo($member['payment_proof'], PATHINFO_EXTENSION));
?>
<tr>
<td><?= $member['id'] ?></td>
<td><?= htmlspecialchars($member['name']) ?></td>
<td><?= htmlspecialchars($member['email']) ?></td>
<td><?= htmlspecialchars($member['phone']) ?></td>
<td><?= htmlspecialchars($member['membership_type']) ?></td>
<td><?= htmlspecialchars($member['payment_method']) ?></td>
    <td class="status-<?= $member['status'] ?>">
    <?= $member['status'] ?>
    </td>
<td>
<?php if($member['payment_proof']): ?>
    <?php if(in_array($ext,['jpg','jpeg','png','gif'])): ?>
        <img src="<?= $member['payment_proof'] ?>" alt="Proof">
    <?php else: ?>
        <a href="<?= $member['payment_proof'] ?>" target="_blank">View PDF</a>
    <?php endif; ?>
<?php endif; ?>
</td>

<td><?= $member['registered_at'] ?></td>
<td>
<form method="POST" action="update_member_status.php">
    <input type="hidden" name="member_id" value="<?= $member['id'] ?>">
    <input type="hidden" name="current_status" value="<?= $member['status'] ?>">

    <?php if ($member['status'] === 'Verified'): ?>
        <button type="submit" class="status-btn btn-deactivate">
            Deactivate
        </button>
    <?php else: ?>
        <button type="submit" class="status-btn btn-activate">
            Activate
        </button>
    <?php endif; ?>
</form>
</td>


</tr>
<?php endwhile; ?>
</table>
</div>
