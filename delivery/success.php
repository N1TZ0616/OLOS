<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Delivery Completed</title>

<style>
:root{
  --bg:#0f172a;
  --card:#1e293b;
  --green:#22c55e;
  --text:#f8fafc;
  --muted:#94a3b8;
}

*{box-sizing:border-box;font-family:Inter,Segoe UI,Arial;}

body{
  margin:0;
  height:100vh;
  background:radial-gradient(circle at top, #1e293b, var(--bg));
  display:flex;
  justify-content:center;
  align-items:center;
  color:var(--text);
}

/* Card */
.card{
  background:var(--card);
  width:420px;
  padding:42px 36px;
  border-radius:22px;
  box-shadow:0 30px 60px rgba(0,0,0,.55);
  text-align:center;
  animation:pop .45s ease;
}

@keyframes pop{
  from{opacity:0;transform:scale(.9)}
  to{opacity:1;transform:scale(1)}
}

/* Icon */
.icon{
  width:96px;
  height:96px;
  border-radius:50%;
  background:linear-gradient(135deg,#16a34a,#22c55e);
  color:white;
  font-size:42px;
  display:flex;
  align-items:center;
  justify-content:center;
  margin:0 auto 22px;
  box-shadow:0 0 0 10px rgba(34,197,94,.15);
}

/* Text */
h2{
  margin:0 0 10px;
  font-size:26px;
}

p{
  margin:0 0 26px;
  color:var(--muted);
  font-size:15px;
}

/* Button */
.btn{
  display:inline-block;
  padding:12px 24px;
  background:linear-gradient(135deg,#2563eb,#3b82f6);
  color:white;
  border-radius:999px;
  text-decoration:none;
  font-weight:700;
  transition:.25s;
}
.btn:hover{
  transform:translateY(-2px);
  box-shadow:0 12px 25px rgba(59,130,246,.45);
}
</style>
</head>

<body>

<div class="card">
  <div class="icon">✓</div>
  <h2>Delivery Completed</h2>
  <p>Great job! Your reward has been successfully added to your earnings.</p>
  <a class="btn" href="dashboard.php">Back to Dashboard</a>
</div>

</body>
</html>
