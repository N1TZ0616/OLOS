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
<title>Bank Card Payment</title>
<style>
body{
  margin:0;
  height:100vh;
  display:flex;
  justify-content:center;
  align-items:center;
  font-family:Segoe UI, system-ui;
  background:radial-gradient(circle at top,#0f172a,#020617);
  color:#e5e7eb;
}
.box{
  background:#020617;
  padding:40px;
  width:380px;
  border-radius:26px;
  box-shadow:0 45px 80px rgba(0,0,0,.7);
}
h2{text-align:center;margin-bottom:20px;}
input{
  width:100%;
  padding:12px;
  margin-top:12px;
  border-radius:12px;
  border:none;
  font-size:14px;
}
.btn{
  margin-top:22px;
  padding:12px;
  width:100%;
  border:none;
  border-radius:14px;
  background:#14b8a6;
  color:white;
  font-size:16px;
  cursor:pointer;
}
.back{
  margin-top:16px;
  display:block;
  text-align:center;
  color:#94a3b8;
  text-decoration:none;
  font-size:14px;
}
.back:hover{color:#e5e7eb;}
</style>
</head>

<body>
<div class="box">
  <h2>Bank Card Payment</h2>

  <form method="post" action="my_orders.php">
    <input type="text" placeholder="Card Number" required>
    <input type="text" placeholder="MM / YY" required>
    <input type="text" placeholder="CVV" required>

    <button class="btn">Pay Now</button>
  </form>

  <a class="back" href="checkout.php">← Back</a>
</div>
</body>
</html>
