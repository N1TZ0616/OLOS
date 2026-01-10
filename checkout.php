<?php
session_start();
require "config/db.php";

/* ======================
   LOGIN CHECK
====================== */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/* ======================
   CART CHECK
====================== */
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$cart = $_SESSION['cart'];

/* ======================
   CALCULATE TOTAL
====================== */
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['qty'];
}

/* ======================
   HANDLE PLACE ORDER
====================== */
if (isset($_POST['place_order'])) {

    $method = $_POST['payment_method'] ?? 'alipay';

    /* 🔥 核心修复：创建订单 */
    $stmt = $mysqli->prepare("
        INSERT INTO orders (user_id, total_amount, status, payment_method, created_at)
        VALUES (?, ?, 'Pending', ?, NOW())
    ");
    $stmt->bind_param("ids", $user_id, $total, $method);
    $stmt->execute();

    if ($stmt->affected_rows <= 0) {
        die("Failed to create order: " . $stmt->error);
    }

    /* 拿到新订单 ID */
    $order_id = $stmt->insert_id;
    $stmt->close();

    /* 清空购物车 */
    unset($_SESSION['cart']);

    /* 记录当前订单（给支付页用） */
    $_SESSION['last_order_id'] = $order_id;

    /* 跳转支付 */
    if ($method === 'alipay') {
        header("Location: alipay_pay.php");
        exit;
    }

    if ($method === 'wechat') {
        header("Location: wechat_pay.php");
        exit;
    }

    if ($method === 'card') {
        header("Location: card_pay.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout</title>

<style>
:root{--primary:#14b8a6;--bg:#020617;--text:#e5e7eb;}
body{
  margin:0;
  background:radial-gradient(circle at top,#0f172a,#020617);
  font-family:Segoe UI, system-ui;
  color:var(--text);
  display:flex;
  justify-content:center;
  align-items:center;
  height:100vh;
}
.box{
  background:#020617;
  padding:45px;
  width:420px;
  border-radius:28px;
  box-shadow:0 45px 80px rgba(0,0,0,.7);
  text-align:center;
}
.btn{
  background:var(--primary);
  padding:12px;
  border-radius:14px;
  color:white;
  width:100%;
  font-size:16px;
  border:none;
  cursor:pointer;
  margin-top:22px;
}
.option{
  padding:14px;
  border-radius:14px;
  border:1px solid #1e293b;
  margin-top:14px;
  cursor:pointer;
}
.option input{display:none}
.option:hover{border-color:#14b8a6}
.option input:checked + span{
  color:#22c55e;
  font-weight:bold;
}
.back{
  display:block;
  margin-top:16px;
  color:#94a3b8;
  text-decoration:none;
  font-size:14px;
}
.back:hover{color:#e5e7eb;}
</style>
</head>

<body>

<div class="box">
  <h2>Confirm Payment</h2>
  <h3 style="margin-top:6px;">RM <?= number_format($total,2) ?></h3>

  <form method="post">
    <label class="option">
      <input type="radio" name="payment_method" value="alipay" checked>
      <span>AliPay</span>
    </label>

    <label class="option">
      <input type="radio" name="payment_method" value="wechat">
      <span>WeChat Pay</span>
    </label>

    <label class="option">
      <input type="radio" name="payment_method" value="card">
      <span>Bank Card</span>
    </label>

    <button class="btn" name="place_order">Pay Now</button>
  </form>

  <a class="back" href="cart.php">← Back to Cart</a>
</div>

</body>
</html>