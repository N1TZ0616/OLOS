<?php
session_start();

/* Login check */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/* Cart check */
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

/* Calculate total (仅显示用) */
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['qty'];
}

/* Handle payment choice */
if (isset($_POST['place_order'])) {
    $_SESSION['payment_method'] = $_POST['payment_method'] ?? 'alipay';

    if ($_SESSION['payment_method'] === 'alipay') {
        header("Location: alipay_pay.php");
        exit;
    }
    if ($_SESSION['payment_method'] === 'wechat') {
        header("Location: wechat_pay.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Checkout</title>
<style>
body{background:#020617;color:white;font-family:Segoe UI;display:flex;justify-content:center;align-items:center;height:100vh}
.box{background:#020617;padding:40px;width:380px;border-radius:22px;text-align:center;box-shadow:0 30px 60px rgba(0,0,0,.6)}
button{margin-top:20px;width:100%;padding:12px;border-radius:12px;border:none;background:#14b8a6;color:white;font-size:16px}
</style>
</head>
<body>
<div class="box">
<h2>Confirm Payment</h2>
<h3>RM <?= number_format($total,2) ?></h3>

<form method="post">
<label><input type="radio" name="payment_method" value="alipay" checked> AliPay</label><br><br>
<label><input type="radio" name="payment_method" value="wechat"> WeChat Pay</label><br>
<button name="place_order">Proceed to Pay</button>
</form>
</div>
</body>
</html>
