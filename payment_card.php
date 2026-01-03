<?php
session_start();
require "config/db.php";

if (!isset($_GET['order_id'])) {
    header("Location: menu.php");
    exit;
}

$order_id = intval($_GET['order_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Bank Card Payment</title>

<style>
body{
  margin:0;
  font-family:Segoe UI;
  background:#020617;
  color:#e5e7eb;
  display:flex;
  align-items:center;
  justify-content:center;
  min-height:100vh;
}

.card{
  background:#020617;
  border-radius:32px;
  padding:40px;
  width:420px;
  box-shadow:0 30px 80px rgba(0,0,0,.6);
}

h2{text-align:center}

label{
  display:block;
  margin:14px 0 6px;
  font-size:14px;
  color:#94a3b8;
}

input{
  width:100%;
  padding:12px;
  border-radius:12px;
  border:1px solid #1e293b;
  background:#020617;
  color:white;
}

.row{
  display:flex;
  gap:12px;
}

.btn{
  margin-top:22px;
  width:100%;
  padding:14px;
  border-radius:16px;
  background:linear-gradient(135deg,#14b8a6,#06b6d4);
  color:white;
  font-weight:700;
  border:none;
  cursor:pointer;
}
</style>
</head>

<body>

<div class="card">
  <h2>Bank Card Payment</h2>

  <form action="checkout.php" method="get">
    <label>Card Number</label>
    <input placeholder="1234 5678 9012 3456" required>

    <label>Card Holder Name</label>
    <input placeholder="JOHN DOE" required>

    <div class="row">
      <div>
        <label>Expiry</label>
        <input placeholder="MM/YY" required>
      </div>
      <div>
        <label>CVV</label>
        <input placeholder="123" required>
      </div>
    </div>

    <button class="btn" type="submit">
      Confirm Payment
    </button>

    <input type="hidden" name="success" value="1">
    <input type="hidden" name="order_id" value="<?= $order_id ?>">
    <input type="hidden" name="pay" value="card">
  </form>
</div>

</body>
</html>
