<?php
session_start();
require "../config/db.php";

$rider_id=$_SESSION['user_id'];

$res=mysqli_query($conn,"
SELECT order_id,reward,completed_at
FROM delivery
WHERE rider_id=$rider_id AND status='completed'
ORDER BY completed_at DESC
");

$total=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT SUM(reward) t FROM delivery
WHERE rider_id=$rider_id AND status='completed'
"))['t'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
<title>My Earnings</title>
<style>
body{font-family:Inter;background:#f8fafc;padding:30px}
.card{background:#fff;padding:24px;border-radius:16px;
box-shadow:0 20px 40px rgba(0,0,0,.1)}
.row{display:flex;justify-content:space-between;padding:10px 0;
border-bottom:1px solid #e5e7eb}
.total{font-size:28px;color:#22c55e;font-weight:800}
</style>
</head>
<body>

<div class="card">
<h2>💰 My Earnings</h2>
<div class="total">RM <?= number_format($total,2) ?></div>

<?php while($r=mysqli_fetch_assoc($res)): ?>
<div class="row">
<span>Order #<?= $r['order_id'] ?></span>
<span>RM <?= number_format($r['reward'],2) ?></span>
</div>
<?php endwhile; ?>

<br>
<a href="dashboard.php">← Back</a>
</div>

</body>
</html>
