<?php
session_start();
require "../config/db.php";

/* Rider only */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'delivery_driver') {
    header("Location: ../index.php");
    exit;
}

$rider_id = $_SESSION['user_id'];
$msg = "";

/* ======================
   Fetch rider info
====================== */
$user = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT username, avatar 
FROM users 
WHERE id=$rider_id
"));

$avatar = $user['avatar']
    ? "../assets/avatars/" . htmlspecialchars($user['avatar'])
    : "../assets/avatars/default.png";

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

/* ======================
   Update avatar
====================== */
if (isset($_POST['update_avatar']) && isset($_FILES['avatar'])) {

    if ($_FILES['avatar']['error'] === 0) {

        $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $name = "rider_" . $rider_id . "." . $ext;
        $path = "../assets/avatars/" . $name;

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $path)) {
            mysqli_query($conn,"
                UPDATE users SET avatar='$name' WHERE id=$rider_id
            ");
            header("Location: profile.php");
            exit;
        }
    }
}

/* ======================
   Change password
====================== */
if (isset($_POST['change_password'])) {

    if (!empty($_POST['new_password'])) {

        $hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

        mysqli_query($conn,"
            UPDATE users SET password_hash='$hash' WHERE id=$rider_id
        ");

        $msg = "Password updated successfully.";
    }
}

/* ======================
   Chart data (30 days)
====================== */
$chart = mysqli_query($conn,"
SELECT DATE(completed_at) d, SUM(reward) total
FROM delivery
WHERE rider_id=$rider_id AND status='completed'
GROUP BY d
ORDER BY d
");

$dates = [];
$totals = [];

while ($c = mysqli_fetch_assoc($chart)) {
    $dates[] = $c['d'];
    $totals[] = $c['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Rider Profile</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{
  margin:0;
  background:#0f172a;
  font-family:Inter,Segoe UI;
  color:#f8fafc;
  padding:30px;
}
.card{
  background:#1e293b;
  border-radius:18px;
  padding:24px;
  margin-bottom:20px;
}
h2{margin-top:0}
.avatar{
  width:110px;height:110px;
  border-radius:50%;
  object-fit:cover;
  border:4px solid #22c55e;
}
input,button{
  padding:10px;
  width:100%;
  border-radius:10px;
  border:none;
  margin-top:10px;
}
button{
  background:#22c55e;
  color:white;
  font-weight:700;
  cursor:pointer;
}
.link{
  display:inline-block;
  margin-top:14px;
  color:#38bdf8;
  text-decoration:none;
}
</style>
</head>

<body>

<div class="card">
  <img class="avatar" src="<?= $avatar ?>"><br><br>
  <b><?= htmlspecialchars($user['username']) ?></b><br>
  Rider Level: <?= $level ?><br>
  Completed Orders: <?= $completed ?><br>
  Total Earnings: RM <?= number_format($income,2) ?>
</div>

<div class="card">
  <h2>Update Avatar</h2>
  <form method="post" enctype="multipart/form-data">
    <input type="file" name="avatar" accept="image/*">
    <button name="update_avatar">Update Avatar</button>
  </form>
</div>

<div class="card">
  <h2>Change Password</h2>
  <form method="post">
    <input type="password" name="new_password" placeholder="New Password">
    <button name="change_password">Change Password</button>
  </form>
  <?php if($msg): ?><p><?= $msg ?></p><?php endif; ?>
</div>

<div class="card">
  <h2>Earnings (Last 30 Days)</h2>
  <canvas id="chart"></canvas>
</div>

<a class="link" href="dashboard.php">← Back to Dashboard</a>

<script>
new Chart(document.getElementById('chart'),{
  type:'line',
  data:{
    labels:<?= json_encode($dates) ?>,
    datasets:[{
      label:'RM',
      data:<?= json_encode($totals) ?>,
      borderColor:'#22c55e',
      tension:.4
    }]
  }
});
</script>

</body>
</html>
