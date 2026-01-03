<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../index.php");
  exit;
}

/* ===== Stats ===== */
$total_orders = mysqli_fetch_assoc(mysqli_query($conn,
  "SELECT COUNT(*) c FROM orders"))['c'];

$today_orders = mysqli_fetch_assoc(mysqli_query($conn,
  "SELECT COUNT(*) c FROM orders 
   WHERE created_at >= NOW() - INTERVAL 24 HOUR"))['c'];

$total_users = mysqli_fetch_assoc(mysqli_query($conn,
  "SELECT COUNT(*) c FROM users"))['c'];

$total_revenue = mysqli_fetch_assoc(mysqli_query($conn,
  "SELECT IFNULL(SUM(total_amount),0) s 
   FROM orders WHERE status='Completed'"))['s'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
:root{
  --panel:#ffffff;
  --soft:#f8fafc;
  --text:#0f172a;
  --muted:#64748b;
  --accent:#14b8a6;
}
body.dark{
  --panel:#020617;
  --soft:#0f172a;
  --text:#e5e7eb;
  --muted:#9ca3af;
}
body{
  margin:0;
  font-family:Segoe UI,system-ui;
  background:linear-gradient(135deg,#0f172a,#111827);
  min-height:100vh;
  display:flex;
  justify-content:center;
  align-items:center;
}
.wrapper{
  width:1250px;
  background:var(--panel);
  border-radius:30px;
  padding:45px 60px;
  box-shadow:0 40px 90px rgba(0,0,0,.35);
  color:var(--text);
}
.topbar{
  display:flex;
  justify-content:space-between;
  align-items:center;
}
.topbar-right{
  display:flex;
  align-items:center;
  gap:12px;
}
.toggle{
  cursor:pointer;
  padding:10px 14px;
  border-radius:12px;
  background:var(--soft);
}
.logout-btn{
  padding:10px 16px;
  border-radius:12px;
  background:#ef4444;
  color:white;
  text-decoration:none;
  font-size:14px;
  font-weight:600;
}
.logout-btn:hover{background:#dc2626;}

.stats{
  display:grid;
  grid-template-columns:repeat(4,1fr);
  gap:24px;
  margin:40px 0;
}
.stat{
  background:var(--soft);
  border-radius:20px;
  padding:26px;
  text-align:center;
}
.stat .value{font-size:28px;font-weight:700;}
.stat .label{color:var(--muted);font-size:14px;margin-top:6px;}

.range{
  display:flex;
  gap:12px;
  margin-bottom:20px;
}
.range button{
  border:none;
  padding:8px 16px;
  border-radius:12px;
  background:var(--soft);
  cursor:pointer;
}
.range button.active{
  background:var(--accent);
  color:white;
}

.grid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:30px;
}
.box{
  background:var(--soft);
  border-radius:24px;
  padding:26px;
}
.updated{
  margin-top:10px;
  font-size:13px;
  color:var(--muted);
}

/* ===== Buttons Section ===== */
.actions{
  margin-top:40px;
  display:flex;
  justify-content:center;
  gap:18px;
}
.actions a{
  width:260px;
  padding:14px;
  text-align:center;
  border-radius:14px;
  background:var(--accent);
  color:white;
  text-decoration:none;
  font-size:15px;
  font-weight:600;
  transition:.2s;
}
.actions a:hover{
  background:#0fa18d;
}
</style>
</head>

<body>
<div class="wrapper">

<div class="topbar">
  <h1>📊 Admin Dashboard</h1>

  <div class="topbar-right">
    <div class="toggle" onclick="toggleMode()">🌙 / ☀️</div>
    <a href="../logout.php" class="logout-btn">🚪 Logout</a>
  </div>
</div>

<div class="stats">
  <div class="stat"><div class="value"><?= $total_orders ?></div><div class="label">Total Orders</div></div>
  <div class="stat"><div class="value"><?= $today_orders ?></div><div class="label">Orders (24h)</div></div>
  <div class="stat"><div class="value"><?= $total_users ?></div><div class="label">Total Users</div></div>
  <div class="stat"><div class="value">RM <?= number_format($total_revenue,2) ?></div><div class="label">Revenue</div></div>
</div>

<div class="range">
  <button class="active" onclick="setRange('today',this)">Today</button>
  <button onclick="setRange('7',this)">7 Days</button>
  <button onclick="setRange('30',this)">30 Days</button>
</div>

<div class="grid">
  <div class="box"><h3>Revenue (RM)</h3><canvas id="revChart"></canvas></div>
  <div class="box"><h3>Orders Count</h3><canvas id="orderChart"></canvas></div>
</div>

<div class="updated" id="lastUpdate">⏱ Waiting for update…</div>

<!-- ========= ⭐ Added Third Button Here ========= -->
<div class="actions">
  <a href="orders.php">📦 Manage Orders</a>
  <a href="users.php">👤 View Users</a>
  <a href="feedback_list.php">⭐ View Feedback</a>
</div>

</div>

<script>
let range='today';

const revChart=new Chart(document.getElementById('revChart'),{
  type:'line',data:{labels:[],datasets:[{data:[],borderWidth:3,tension:.4}]},
  options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
});
const orderChart=new Chart(document.getElementById('orderChart'),{
  type:'line',data:{labels:[],datasets:[{data:[],borderWidth:3,tension:.4}]},
  options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
});

function load(){
  fetch(`revenue_data.php?range=${range}`).then(r=>r.json()).then(j=>{
    revChart.data.labels=j.labels;
    revChart.data.datasets[0].data=j.revenue;
    orderChart.data.labels=j.labels;
    orderChart.data.datasets[0].data=j.orders;
    revChart.update(); orderChart.update();
    document.getElementById('lastUpdate').innerText='⏱ Updated at '+j.updated;
  });
}
function setRange(r,el){
  range=r;
  document.querySelectorAll('.range button').forEach(b=>b.classList.remove('active'));
  el.classList.add('active');
  load();
}
function toggleMode(){
  document.body.classList.toggle('dark');
  localStorage.setItem('dark',document.body.classList.contains('dark'));
}
if(localStorage.getItem('dark')==='true') document.body.classList.add('dark');

load(); setInterval(load,5000);
</script>
</body>
</html>
