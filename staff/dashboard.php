<?php
session_start();

/* ======================
   LOGIN + ROLE CHECK
====================== */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurant_staff') {
    header("Location: ../index.php");
    exit;
}

/* ======================
   USER INFO (UI ONLY)
====================== */
$username = $_SESSION['username'] ?? 'Staff';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Restaurant Staff Dashboard</title>

<style>
*{box-sizing:border-box}

body{
  margin:0;
  font-family:Segoe UI, Arial;
  background:linear-gradient(135deg,#F4E04D,#F7D046);
  min-height:100vh;
}

/* ===== Top Bar ===== */
.topbar{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:20px 40px;
}

.topbar h1{
  margin:0;
  font-size:22px;
  font-weight:600;
}

/* ===== User Area ===== */
.user-area{
  display:flex;
  align-items:center;
  gap:14px;
}

.user-name{
  font-size:14px;
  font-weight:500;
  color:#333;
}

.logout{
  font-size:13px;
  padding:6px 16px;
  background:#333;
  color:white;
  border-radius:20px;
  text-decoration:none;
  transition:.2s;
}

.logout:hover{
  background:#000;
}

/* ===== Main Card ===== */
.main{
  display:flex;
  justify-content:center;
  margin-top:90px;
}

.card{
  background:white;
  width:420px;
  padding:42px;
  border-radius:18px;
  text-align:center;
  box-shadow:0 20px 45px rgba(0,0,0,.18);
}

.card h2{
  margin-top:0;
  font-size:22px;
}

.card p{
  color:#666;
  font-size:14px;
  margin:18px 0 34px;
  line-height:1.6;
}

.card a{
  display:inline-block;
  padding:14px 34px;
  background:#17C3B2;
  color:white;
  text-decoration:none;
  border-radius:30px;
  font-size:15px;
  transition:.2s;
}

.card a:hover{
  background:#139e91;
}
</style>
</head>

<body>

<!-- ===== Top Bar ===== -->
<div class="topbar">
  <h1>🍳 Restaurant Staff Dashboard</h1>

  <div class="user-area">
    <span class="user-name">
      👤 <?= htmlspecialchars($username) ?>
    </span>
    <a class="logout" href="../logout.php"
       onclick="return confirm('Are you sure you want to logout?');">
       Logout
    </a>
  </div>
</div>

<!-- ===== Main Content ===== -->
<div class="main">
  <div class="card">
    <h2>Manage Orders</h2>
    <p>
      View, prepare, and manage incoming customer orders
      for your restaurant in real time.
    </p>
    <a href="orders.php">Go to Orders</a>
  </div>
</div>

</body>
</html>
