<?php
session_start();
require "../config/db.php";

$rider_id = $_SESSION['user_id'];

$sql = "
SELECT COUNT(*) AS total_orders, SUM(reward) AS total_earnings
FROM delivery
WHERE rider_id=$rider_id AND status='completed'
";
$data = mysqli_fetch_assoc(mysqli_query($conn, $sql));
?>

<!DOCTYPE html>
<html>
<head>
<title>My Earnings</title>
<style>
body{text-align:center;font-family:Arial;padding:80px;background:#f4f6f9;}
.box{background:#fff;padding:40px;border-radius:10px;display:inline-block;}
</style>
</head>
<body>

<div class="box">
<h2>My Earnings</h2>
<p>Completed Orders: <?= $data['total_orders'] ?? 0 ?></p>
<p>Total Earnings: RM <?= number_format($data['total_earnings'] ?? 0,2) ?></p>
<a href="dashboard.php">Back</a>
</div>

</body>
</html>
