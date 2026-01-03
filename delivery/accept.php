<?php
session_start();
require "../config/db.php";

$rider_id = $_SESSION['user_id'];
$order_id = intval($_GET['order_id']);

mysqli_query($conn,"
INSERT INTO delivery (order_id,rider_id)
VALUES ($order_id,$rider_id)
");
?>
<!DOCTYPE html>
<html>
<head>
<title>Order Accepted</title>
<style>
body{background:#f8fafc;font-family:Inter;display:flex;
justify-content:center;align-items:center;height:100vh}
.card{background:#fff;padding:36px;width:420px;border-radius:16px;
box-shadow:0 20px 40px rgba(0,0,0,.15);text-align:center}
.icon{width:90px;height:90px;border-radius:50%;
background:#16a34a;color:#fff;font-size:40px;
display:flex;align-items:center;justify-content:center;
margin:0 auto 20px;animation:pop .5s}
@keyframes pop{from{transform:scale(.6)}to{transform:scale(1)}}
.bar{height:10px;background:#2563eb;border-radius:999px;
animation:load 6s linear forwards}
@keyframes load{from{width:100%}to{width:0%}}
</style>
</head>
<body>

<div class="card">
<div class="icon">✓</div>
<h2>Order Accepted</h2>
<p>Order #<?= $order_id ?> assigned to you</p>
<div style="background:#e5e7eb;border-radius:999px">
<div class="bar"></div>
</div>
<p>Redirecting...</p>
</div>

<script>
setTimeout(()=>location.href='dashboard.php',6000)
</script>

</body>
</html>
