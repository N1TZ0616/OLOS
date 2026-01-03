<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'delivery_driver') {
    header("Location: ../index.php");
    exit;
}

$rider_id = $_SESSION['user_id'];

/* Available Orders */
$orders = mysqli_query($conn,"
SELECT o.id, o.delivery_address, o.total_amount
FROM orders o
LEFT JOIN delivery d ON d.order_id=o.id
WHERE d.id IS NULL
ORDER BY o.created_at DESC
");

/* Active Delivery */
$my = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT d.*, o.delivery_address, o.total_amount,
TIMESTAMPDIFF(MINUTE, d.started_at, NOW()) AS mins
FROM delivery d
JOIN orders o ON o.id=d.order_id
WHERE d.rider_id=$rider_id AND d.status!='completed'
LIMIT 1
"));

/* KPI */
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
<html>
<head>
<meta charset="UTF-8">
<title>Rider Dashboard</title>

<style>
:root{
  --bg:#f8fafc;
  --card:#fff;
  --text:#0f172a;
  --muted:#64748b;
  --green:#22c55e;
  --blue:#2563eb;
}
body.dark{
  --bg:#0f172a;
  --card:#1e293b;
  --text:#f8fafc;
  --muted:#94a3b8;
}
body{
  margin:0;
  font-family:Inter,Segoe UI,Arial;
  background:var(--bg);
  color:var(--text);
  padding:32px;
  transition:.3s;
}
h2{margin:26px 0 12px}

.card{
  background:var(--card);
  border-radius:18px;
  padding:22px;
  box-shadow:0 20px 40px rgba(0,0,0,.08);
  margin-bottom:18px;
  animation:fade .4s ease;
}
@keyframes fade{
  from{opacity:0;transform:translateY(10px)}
  to{opacity:1}
}
.btn{
  padding:10px 18px;
  border-radius:12px;
  color:white;
  font-weight:700;
  text-decoration:none;
  display:inline-block;
}
.green{background:linear-gradient(135deg,#16a34a,#22c55e)}
.blue{background:#2563eb}
.red{background:#dc2626}

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
  background:#e5e7eb;
}
.step.active{background:#22c55e;color:white}

.toggle{
  position:fixed;
  top:20px;
  right:20px;
  cursor:pointer;
}
</style>
</head>

<body>

<div class="toggle" onclick="toggleDark()">🌙</div>

<!-- KPI -->
<div class="card">
<b>Rider Level: <?= $level ?></b><br>
Completed Orders: <?= $completed ?><br>
Total Earnings: RM <?= number_format($income,2) ?><br>
<a href="wallet.php">View Earnings →</a>
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
<h2>🚴 Active Delivery</h2>

<?php if($my): ?>
<div class="card">
<b>Order #<?= $my['order_id'] ?></b><br>
<?= htmlspecialchars($my['delivery_address']) ?><br><br>

<div class="timeline">
  <div class="step active">Accepted</div>
  <div class="step <?= $my['status']!='accepted'?'active':'' ?>">Delivering</div>
  <div class="step">Completed</div>
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

<script>
function toggleDark(){
  document.body.classList.toggle('dark');
  localStorage.setItem('dark',document.body.classList.contains('dark'));
}
if(localStorage.getItem('dark')==='true') document.body.classList.add('dark');
</script>

</body>
</html>
