<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurant_staff') {
    header("Location: ../index.php");
    exit;
}

date_default_timezone_set("Asia/Kuala_Lumpur");

/* =================================================
   1️⃣ AUTO COMPLETE ORDERS
================================================= */
$now = date("Y-m-d H:i:s");

mysqli_query($conn, "
UPDATE orders
SET status = 'Ready'
WHERE status = 'Preparing'
  AND prep_end_time IS NOT NULL
  AND prep_end_time <= '$now'
");

/* =================================================
   2️⃣ SYNC CHEF STATUS
================================================= */
mysqli_query($conn, "
UPDATE chefs c
SET status = 'Busy'
WHERE EXISTS (
    SELECT 1 FROM orders o
    WHERE o.chef_name = c.name
      AND o.status = 'Preparing'
)
");

mysqli_query($conn, "
UPDATE chefs c
SET status = 'Available'
WHERE NOT EXISTS (
    SELECT 1 FROM orders o
    WHERE o.chef_name = c.name
      AND o.status = 'Preparing'
)
");

/* =================================================
   3️⃣ GET STAFF RESTAURANT
================================================= */
$user_id = $_SESSION['user_id'];
$res = mysqli_query($conn, "SELECT restaurant_id FROM users WHERE id = $user_id");
$staff = mysqli_fetch_assoc($res);
$restaurant_id = $staff['restaurant_id'];

/* =================================================
   4️⃣ SUMMARY COUNTS
================================================= */
$total_chefs = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) c FROM chefs")
)['c'];

$busy_chefs = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(DISTINCT chef_name) c
        FROM orders
        WHERE status = 'Preparing'
          AND chef_name IS NOT NULL
    ")
)['c'];

$available_chefs = $total_chefs - $busy_chefs;

/* =================================================
   5️⃣ AVAILABLE CHEFS
================================================= */
$chef_list = mysqli_query($conn, "
SELECT *
FROM chefs
WHERE status = 'Available'
");

/* =================================================
   6️⃣ FETCH ORDERS
================================================= */
$orders = mysqli_query($conn, "
SELECT o.*, u.username
FROM orders o
JOIN users u ON o.user_id = u.id
WHERE o.restaurant_id = $restaurant_id
ORDER BY o.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Restaurant Orders</title>

<meta http-equiv="refresh" content="10">

<style>
body{
  font-family:Segoe UI, Arial;
  background:#f5f6f8;
  padding:30px;
}
.summary{
  display:flex;
  gap:20px;
  margin-bottom:25px;
}
.stat{
  background:#fff;
  padding:18px 26px;
  border-radius:14px;
  box-shadow:0 6px 18px rgba(0,0,0,.08);
}
.card{
  background:#fff;
  padding:22px;
  border-radius:16px;
  margin-bottom:22px;
  box-shadow:0 10px 25px rgba(0,0,0,.08);
}
.card.preparing{border-left:6px solid #f39c12}
.card.ready{border-left:6px solid #2ecc71}
.card.pending{border-left:6px solid #bdc3c7}

.header{
  display:flex;
  justify-content:space-between;
  margin-bottom:14px;
}
.badge{
  padding:6px 14px;
  border-radius:20px;
  font-size:12px;
  color:#fff;
}
.badge.preparing{background:#f39c12}
.badge.ready{background:#2ecc71}
.badge.pending{background:#95a5a6}

.info{
  display:grid;
  grid-template-columns:1fr 1fr 1fr;
  gap:12px;
  margin-bottom:14px;
}
.time{
  font-weight:600;
}
.actions{
  display:flex;
  gap:12px;
}
select,button{
  padding:8px 14px;
  border-radius:8px;
}
button{
  background:#17C3B2;
  color:#fff;
  border:none;
  cursor:pointer;
}
</style>
</head>

<body>

<h2>🍳 Restaurant Orders</h2>

<div class="summary">
  <div class="stat"><strong><?= $total_chefs ?></strong><br>Total Chefs</div>
  <div class="stat"><strong><?= $busy_chefs ?></strong><br>Busy</div>
  <div class="stat"><strong><?= $available_chefs ?></strong><br>Available</div>
</div>

<?php while ($o = mysqli_fetch_assoc($orders)):
  $status = $o['status'];
  $cls = strtolower($status);
?>

<div class="card <?= $cls ?>">
  <div class="header">
    <strong>Order #<?= $o['id'] ?></strong>
    <span class="badge <?= $cls ?>"><?= strtoupper($status) ?></span>
  </div>

  <div class="info">
    <div>
      Customer<br>
      <?= $o['username'] ?>
    </div>

    <div>
      Chef<br>
      <?php if ($status === 'Preparing' || $status === 'Ready'): ?>
        <?= $o['chef_name'] ?>
      <?php else: ?>
        —
      <?php endif; ?>
    </div>

    <div>
      Time<br>
      <?php if ($status === 'Preparing'):

        if (!empty($o['prep_end_time'])) {
            $left = max(0, ceil((strtotime($o['prep_end_time']) - strtotime($now)) / 60));
        } else {
            $left = 0;
        }

      ?>
        <span class="time" style="color:#e67e22">
          <?= $left ?> min left
        </span>
      <?php elseif ($status === 'Ready'): ?>
        <span class="time" style="color:#2ecc71">Done</span>
      <?php else: ?>
        <span class="time" style="color:#999">Not started</span>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($status === 'Pending'): ?>
  <form class="actions" method="post" action="assign_chef.php">
    <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
    <select name="chef_id" required>
      <option value="">Select Chef</option>
      <?php while ($c = mysqli_fetch_assoc($chef_list)): ?>
        <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
      <?php endwhile; ?>
    </select>
    <button>Assign</button>
  </form>
  <?php endif; ?>
</div>

<?php endwhile; ?>

</body>
</html>
