<?php
session_start();
require "../config/db.php";

/* Rider only */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'delivery_driver') {
    header("Location: ../index.php");
    exit;
}

$rider_id = $_SESSION['user_id'];

/* ======================
   Available Orders
====================== */
$orders = mysqli_query($conn,"
SELECT o.id, o.delivery_address, o.total_amount
FROM orders o
LEFT JOIN delivery d ON d.order_id=o.id
WHERE d.id IS NULL
ORDER BY o.created_at DESC
");

/* ======================
   Active Delivery
====================== */
$my = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT d.*, o.delivery_address, o.total_amount,
TIMESTAMPDIFF(MINUTE, d.started_at, NOW()) AS mins
FROM delivery d
JOIN orders o ON o.id=d.order_id
WHERE d.rider_id=$rider_id AND d.status!='completed'
LIMIT 1
"));

/* ======================
   KPI
====================== */
$kpi = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT 
COUNT(*) completed,
SUM(reward) income
FROM delivery
WHERE rider_id=$rider_id AND status='completed'
"));

$completed = $kpi['completed'] ?? 0;
$income = $kpi['income'] ?? 0;

$level = "Bronze";
if ($completed >= 20) $level = "Silver";
if ($completed >= 50) $level = "Gold";
if ($completed >= 100) $level = "Platinum";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Rider Dashboard</title>

<style>
:root{
  --bg:#0f172a;
  --card:#1e293b;
  --text:#f8fafc;
  --muted:#94a3b8;
  --green:#22c55e;
  --blue:#2563eb;
  --red:#dc2626;
}
body{
  margin:0;
  font-family:Inter,Segoe UI,Arial;
  background:var(--bg);
  color:var(--text);
  padding:32px;
}
h2{margin:28px 0 14px}

/* Header */
.header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:26px;
}
.header h1{margin:0;font-size:28px}
.nav a{
  text-decoration:none;
  color:white;
  padding:10px 18px;
  border-radius:999px;
  margin-left:10px;
  font-weight:600;
}
.nav .profile{background:#334155}
.nav .logout{background:var(--red)}

/* Card */
.card{
  background:var(--card);
  border-radius:18px;
  padding:22px;
  box-shadow:0 20px 40px rgba(0,0,0,.35);
  margin-bottom:18px;
  animation:fade .4s ease;
}
@keyframes fade{
  from{opacity:0;transform:translateY(10px)}
  to{opacity:1}
}

/* Buttons */
.btn{
  padding:10px 18px;
  border-radius:12px;
  color:white;
  font-weight:700;
  text-decoration:none;
  display:inline-block;
}
.green{background:linear-gradient(135deg,#16a34a,#22c55e)}
.blue{background:var(--blue)}
.red{background:var(--red)}

/* Timeline */
.timeline{
  display:flex;
  gap:10px;
  margin-top:14px;
}
.step{
  flex:1;
  text-align:center;
  padding:8px;
  border-radius:999px;
  font-size:12px;
  background:#334155;
}
.step.active{background:var(--green)}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
  <h1>🚴 Rider Dashboard</h1>
  <div class="nav">
    <a class="profile" href="profile.php">👤 My Account</a>
    <a class="logout" href="../logout.php">Logout</a>
  </div>
</div>

<!-- KPI -->
<div class="card">
  <b>Rider Level:</b> <?= $level ?><br>
  <b>Completed Orders:</b> <?= $completed ?><br>
  <b>Total Earnings:</b> RM <?= number_format($income,2) ?><br>
</div>

<!-- Available Orders -->
<h2>📦 Available Orders</h2>

<?php while($o=mysqli_fetch_assoc($orders)): ?>
<div class="card">
  <b>Order #<?= $o['id'] ?></b><br>
  <?= htmlspecialchars($o['delivery_address']) ?><br>
  RM <?= number_format($o['total_amount'],2) ?><br><br>
  <a class="btn green" href="accept.php?order_id=<?= $o['id'] ?>">Accept</a>
</div>
<?php endwhile; ?>

<!-- Active Delivery -->
<h2>🚚 Active Delivery</h2>

<?php if($my): ?>
<div class="card">
  <b>Order #<?= $my['order_id'] ?></b><br>
  <?= htmlspecialchars($my['delivery_address']) ?><br><br>

  <div class="timeline">
    <div class="step active">Accepted</div>
    <div class="step <?= $my['status']!='accepted'?'active':'' ?>">Delivering</div>
    <div class="step <?= $my['status']=='completed'?'active':'' ?>">Completed</div>
  </div>

  <?php if($my['status']=='delivering'): ?>
    <p>⏱ Time on delivery: <b><?= max(1,$my['mins']) ?> min</b></p>
    <a class="btn red" href="complete.php?id=<?= $my['id'] ?>">Complete</a>
  <?php elseif($my['status']=='accepted'): ?>
    <a class="btn blue" href="start.php?id=<?= $my['id'] ?>">Start Delivery</a>
  <?php endif; ?>
</div>
<?php else: ?>
<div class="card">No active delivery</div>
<?php endif; ?>

</body>
</html>
