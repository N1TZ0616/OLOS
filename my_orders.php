<?php
session_start();
require "config/db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = mysqli_query($conn,"
    SELECT * FROM orders 
    WHERE user_id=$user_id 
    ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Orders</title>

<style>
body{
  margin:0;
  background:#0b1221;
  font-family:Segoe UI,system-ui;
  color:#e2e8f0;
}

.wrapper{
  text-align:center;
  margin-top:35px;
}

h2{
  font-size:34px;
  margin-bottom:40px;
  display:flex;
  align-items:center;
  justify-content:center;
  gap:10px;
}
h2::before{
  content:"📦";
}

.container{
  max-width:900px;
  margin:0 auto 80px;
  padding:0 20px;
}

.card{
  background:#1e293b;
  padding:24px 26px;
  border-radius:22px;
  margin-bottom:26px;
  display:flex;
  justify-content:space-between;
  align-items:center;
  box-shadow:0 15px 45px rgba(0,0,0,.45);
  border-left:6px solid #10b981;
}

.left{ text-align:left; }
.order-id{ font-size:20px; font-weight:bold; margin-bottom:6px; }

.badge{
  padding:6px 14px;
  border-radius:12px;
  font-size:13px;
  font-weight:bold;
  display:inline-block;
}
.completed{background:#bbf7d0;color:#064e3b}
.delivering{background:#a7f3d0;color:#065f46}
.preparing{background:#fca5a5;color:#7f1d1d}
.pending{background:#fde68a;color:#92400e}
.ready{background:#bfdbfe;color:#1e3a8a}
.cancelled{background:#fecaca;color:#7f1d1d}

.date{
  color:#9ca3af;
  font-size:14px;
  margin-top:6px;
}

.view-btn{
  background:#14b8a6;
  padding:12px 18px;
  border-radius:10px;
  text-decoration:none;
  color:white;
  font-weight:bold;
  transition:.3s;
  display:inline-block;
  margin-top:12px;
}
.view-btn:hover{ background:#0d9488 }

/* Cancel button */
.cancel-btn{
  background:#ef4444;
  padding:10px 16px;
  border-radius:10px;
  text-decoration:none;
  color:white;
  font-weight:bold;
  display:inline-block;
  margin-top:10px;
}
.cancel-btn:hover{ background:#dc2626 }

/* Back button */
.back-btn{
  position:fixed;
  top:30px;
  left:30px;
  background:#14b8a6;
  color:white;
  text-decoration:none;
  padding:10px 18px;
  border-radius:12px;
  font-weight:600;
  font-size:15px;
}
.back-btn:hover{ background:#0d9488 }
</style>
</head>

<body>

<a href="menu.php" class="back-btn">← Back to Menu</a>

<div class="wrapper">
  <h2>My Orders</h2>
</div>

<div class="container">

<?php if(mysqli_num_rows($sql)==0): ?>

  <p style="text-align:center;color:#94a3b8;font-size:18px;margin-top:30px;">
    No orders yet.
  </p>

<?php else: ?>

<?php while($o=mysqli_fetch_assoc($sql)): ?>
<div class="card">

  <div class="left">
    <div class="order-id">Order #<?= $o['id'] ?></div>

    <span class="badge <?= strtolower($o['status']) ?>">
      <?= ucfirst($o['status']) ?>
    </span>

    <div class="date">
      📅 <?= date("Y-m-d h:i A", strtotime($o['created_at'])) ?>
    </div>

    <a class="view-btn" href="track_order.php?order_id=<?= $o['id'] ?>">
      View Delivery Status ➜
    </a>

    <!-- ⭐ 关键修复：大小写不敏感判断 -->
    <?php if (strtolower($o['status']) === 'pending'): ?>
      <a class="cancel-btn"
         href="cancel_order.php?order_id=<?= $o['id'] ?>"
         onclick="return confirm('Cancel this order?');">
         Cancel Order
      </a>
    <?php endif; ?>

  </div>

  <div style="font-size:18px;font-weight:bold">
    RM <?= number_format($o['total_amount'],2) ?>
  </div>

</div>
<?php endwhile; ?>

<?php endif; ?>

</div>
</body>
</html>
