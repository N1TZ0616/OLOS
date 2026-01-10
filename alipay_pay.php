<?php
session_start();
require "config/db.php";

if (!isset($_SESSION['user_id'], $_SESSION['cart'])) {
    header("Location: menu.php");
    exit;
}

$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['qty'];
}

/* 用户点击“我已支付” */
if (isset($_POST['paid'])) {

    $user_id = (int)$_SESSION['user_id'];
    $method = $_SESSION['payment_method'] ?? 'alipay';

    $stmt = $conn->prepare("
        INSERT INTO orders (user_id, total_amount, status, payment_method)
        VALUES (?, ?, 'Pending', ?)
    ");
    $stmt->bind_param("ids", $user_id, $total, $method);
    $stmt->execute();
    $stmt->close();

    unset($_SESSION['cart'], $_SESSION['payment_method']);

    header("Location: my_orders.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>AliPay</title>
<style>
body{background:#020617;color:white;font-family:Segoe UI;display:flex;justify-content:center;align-items:center;height:100vh}
.box{background:#020617;padding:40px;width:360px;border-radius:22px;text-align:center;box-shadow:0 30px 60px rgba(0,0,0,.6)}
img{width:220px;background:white;padding:12px;border-radius:14px}
button{margin-top:20px;width:100%;padding:12px;border-radius:12px;border:none;background:#14b8a6;color:white;font-size:16px}
</style>
</head>
<body>
<div class="box">
<h2>AliPay Payment</h2>
<p>Scan QR Code</p>
<img src="/assets/img/alipay.jpg">

<form method="post">
<button name="paid">I Have Paid</button>
</form>
</div>
</body>
</html>
