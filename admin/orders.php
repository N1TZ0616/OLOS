<?php
session_start();
require "../config/db.php";

/* ===== Admin only ===== */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

/* ===== Update order status (NOT cancelled) ===== */
if (isset($_POST['update_status'])) {
    $order_id   = (int)$_POST['order_id'];
    $new_status = $_POST['status'];

    $allowed = ['pending','confirmed','preparing','delivering','completed','cancelled'];
    if ($new_status == "ready") $new_status = "confirmed";

    if (in_array($new_status,$allowed)) {
        mysqli_query(
            $conn,
            "UPDATE orders 
             SET status='$new_status' 
             WHERE id=$order_id AND status!='cancelled'"
        );
        echo "<script>alert('Order #$order_id updated');location='orders.php';</script>";
        exit;
    }
}

/* ===== Delete Order (NOT cancelled) ===== */
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    mysqli_query(
        $conn,
        "DELETE FROM orders 
         WHERE id=$delete_id AND status!='cancelled'"
    );
    echo "<script>alert('Order #$delete_id deleted successfully');location='orders.php';</script>";
    exit;
}

/* ===== Fetch orders ===== */
$result = mysqli_query($conn,"
    SELECT o.*, u.username
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin | Manage Orders</title>

<style>
body{margin:0;font-family:Segoe UI, Arial;background:#f8fafc;}
.container{max-width:1200px;margin:40px auto;background:white;padding:40px;border-radius:22px;box-shadow:0 20px 60px rgba(0,0,0,.12);}
.back{display:inline-block;margin-bottom:20px;background:#14b8a6;color:white;padding:8px 16px;border-radius:10px;text-decoration:none;}

h2{margin-top:0;}

table{width:100%;border-collapse:collapse;}
th, td{padding:14px;border-bottom:1px solid #e5e7eb;text-align:center;}
th{background:#f1f5f9;font-weight:600;}

.badge{padding:6px 14px;border-radius:20px;font-size:12px;font-weight:600;display:inline-block;}
.pending{background:#fde68a;color:#92400e;}
.preparing{background:#fdba74;color:#9a3412;}
.confirmed{background:#93c5fd;color:#1e40af;}
.delivering{background:#a7f3d0;color:#065f46;}
.completed{background:#d1fae5;color:#065f46;}
.cancelled{background:#fecaca;color:#7f1d1d;}

select{padding:6px 10px;border-radius:8px;}
button{padding:6px 14px;border:none;border-radius:8px;background:#14b8a6;color:white;cursor:pointer;}

/* PDF button */
.pdf-btn{
    float:right;
    background:#0f9d58;
    padding:10px 20px;
    color:white;
    text-decoration:none;
    font-weight:bold;
    border-radius:8px;
    margin-bottom:10px;
}
.pdf-btn:hover{background:#0b7e47;}

/* Delete button */
.delete-btn{
    background:#e11d48 !important;
    padding:6px 14px;
    color:white;
    border-radius:8px;
    text-decoration:none;
}
.delete-btn:hover{background:#be123c;}
</style>
</head>

<body>

<div class="container">

<a class="back" href="dashboard.php">← Back to Dashboard</a>

<h2>📦 Manage Orders</h2>

<a class="pdf-btn" href="generate_report.php" target="_blank">📄 Export PDF</a>

<table>
<tr>
<th>ID</th>
<th>User</th>
<th>Total</th>
<th>Status</th>
<th>Change Status</th>
<th>Action</th>
</tr>

<?php while ($o=mysqli_fetch_assoc($result)): ?>
<tr>
<td>#<?=$o['id']?></td>
<td><?=$o['username']?></td>
<td>RM <?=number_format($o['total_amount'],2)?></td>

<td>
<span class="badge <?= strtolower($o['status']) ?>">
    <?= ucfirst($o['status']) ?>
</span>
</td>

<td>
<?php if ($o['status'] === 'cancelled'): ?>
    <em style="color:#9ca3af;">No action</em>
<?php else: ?>
<form method="POST" style="display:flex;justify-content:center;gap:10px;">
<input type="hidden" name="order_id" value="<?=$o['id']?>">
<select name="status">
<?php foreach(['pending','confirmed','preparing','delivering','completed','cancelled'] as $s): ?>
<option value="<?=$s?>" <?=$o['status']==$s?'selected':''?>><?=ucfirst($s)?></option>
<?php endforeach; ?>
</select>
<?php endif; ?>
</td>

<td style="display:flex;gap:8px;justify-content:center;">
<?php if ($o['status'] !== 'cancelled'): ?>
    <button name="update_status">Update</button>

    <a class="delete-btn"
       href="orders.php?delete_id=<?=$o['id']?>"
       onclick="return confirm('Are you sure to DELETE Order #<?=$o['id']?> ?');">
       Delete
    </a>
<?php else: ?>
    <span style="color:#9ca3af;">—</span>
<?php endif; ?>
</td>

<?php if ($o['status'] !== 'cancelled'): ?>
</form>
<?php endif; ?>

</tr>
<?php endwhile; ?>

</table>

</div>
</body>
</html>
