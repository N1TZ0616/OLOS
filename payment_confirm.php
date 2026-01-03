<?php
session_start();

if (!isset($_GET['order_id'], $_GET['pay'])) {
    die("Missing parameters");
}

$order_id = (int)$_GET['order_id'];
$pay = $_GET['pay'];

/*
|--------------------------------------------------------------------------
| 自动计算项目根 URL（核心）
|--------------------------------------------------------------------------
*/
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'];
$dir    = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

$baseURL = $scheme . '://' . $host . $dir;

/*
|--------------------------------------------------------------------------
| QR 图片映射（不用关心盘符）
|--------------------------------------------------------------------------
*/
$qr_map = [
    'alipay' => $baseURL . '/assets/qr/alipay.jpg',
    'wechat' => $baseURL . '/assets/qr/wechat.jpg',
];

$qr = $qr_map[$pay] ?? $qr_map['alipay'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= strtoupper($pay) ?> Payment</title>
<style>
body{
  margin:0;
  font-family:Segoe UI,system-ui;
  background:#020617;
  color:#e5e7eb;
  display:flex;
  align-items:center;
  justify-content:center;
  min-height:100vh;
}
.card{
  width:420px;
  padding:42px;
  border-radius:32px;
  background:#020617;
  box-shadow:0 40px 90px rgba(0,0,0,.7);
  text-align:center;
}
.qr{
  margin:26px 0;
}
.qr img{
  width:220px;
  height:220px;
  background:#fff;
  padding:14px;
  border-radius:18px;
  object-fit:contain;
}
.btn{
  display:block;
  margin-top:24px;
  padding:14px;
  border-radius:18px;
  background:#14b8a6;
  color:#fff;
  text-decoration:none;
  font-weight:700;
}
</style>
</head>
<body>

<div class="card">
  <h2><?= strtoupper($pay) ?> Payment</h2>
  <p>Scan QR code to pay</p>

  <div class="qr">
    <!-- ✅ 一定能加载的 URL -->
    <img src="<?= htmlspecialchars($qr) ?>" alt="QR Code">
  </div>

  <p>Order ID: #<?= $order_id ?></p>

  <a class="btn"
     href="<?= $baseURL ?>/checkout.php?success=1&order_id=<?= $order_id ?>&pay=<?= $pay ?>">
     I Have Completed Payment
  </a>
</div>

</body>
</html>
