<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Delivery Completed</title>
<style>
body{background:#f8fafc;font-family:Inter;
display:flex;justify-content:center;align-items:center;height:100vh}
.card{background:#fff;padding:38px;width:420px;border-radius:16px;
box-shadow:0 20px 40px rgba(0,0,0,.15);text-align:center}
.icon{width:90px;height:90px;border-radius:50%;
background:#16a34a;color:#fff;font-size:38px;
display:flex;align-items:center;justify-content:center;margin:0 auto 20px}
</style>
</head>
<body>

<div class="card">
<div class="icon">✓</div>
<h2>Delivery Completed</h2>
<p>Great job! Reward has been added.</p>
<a href="dashboard.php">Back to Dashboard</a>
</div>

</body>
</html>
