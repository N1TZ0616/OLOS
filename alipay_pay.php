<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>AliPay Payment</title>
<style>
body{
  margin:0;height:100vh;
  display:flex;justify-content:center;align-items:center;
  font-family:Segoe UI, system-ui;
  background:radial-gradient(circle at top,#0f172a,#020617);
  color:#e5e7eb;
}
.box{
  background:#020617;
  padding:40px;width:380px;
  border-radius:26px;
  box-shadow:0 45px 80px rgba(0,0,0,.7);
  text-align:center;
}
img{
  width:220px;
  margin:22px auto;
  display:block;
  background:white;
  padding:12px;
  border-radius:16px;
}
.btn{
  margin-top:20px;
  padding:12px;width:100%;
  border:none;border-radius:14px;
  background:#14b8a6;color:white;
  font-size:16px;cursor:pointer;
}
.back{
  margin-top:16px;display:block;
  color:#94a3b8;text-decoration:none;
}
.back:hover{color:#e5e7eb;}
</style>
</head>

<body>
<div class="box">
  <h2>AliPay Payment</h2>
  <p>Scan the QR code to pay</p>

  <!-- ✅ 公网可用 -->
  <img src="/assets/img/alipay.jpg" alt="AliPay QR Code">

  <form method="post" action="my_orders.php">
    <button class="btn">I Have Paid</button>
  </form>

  <a class="back" href="checkout.php">← Back</a>
</div>
</body>
</html>
