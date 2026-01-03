<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../index.php");
  exit;
}

$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Management</title>

<style>
:root{
  --panel:#ffffff;
  --soft:#f1f5f9;
  --text:#0f172a;
  --muted:#64748b;
  --accent:#14b8a6;
}

body{
  margin:0;
  font-family:Segoe UI,system-ui;
  background:linear-gradient(135deg,#facc15,#fbbf24);
  min-height:100vh;
  display:flex;
  justify-content:center;
  align-items:flex-start;
  padding:60px 20px;
}

.wrapper{
  width:1150px;
  background:var(--panel);
  border-radius:30px;
  box-shadow:0 50px 100px rgba(0,0,0,.25);
  padding:36px 42px 42px;
}

/* ===== Header ===== */
.header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:28px;
}
.header h2{
  margin:0;
  font-size:26px;
  display:flex;
  align-items:center;
  gap:10px;
}
.back-btn{
  background:var(--accent);
  color:white;
  text-decoration:none;
  padding:8px 16px;
  border-radius:999px;
  font-size:14px;
  font-weight:600;
}

/* ===== Table ===== */
.table-wrap{
  background:var(--soft);
  border-radius:22px;
  padding:18px;
}

table{
  width:100%;
  border-collapse:collapse;
  background:var(--panel);
  border-radius:16px;
  overflow:hidden;
}

th,td{
  padding:15px 18px;
  font-size:14px;
}

th{
  background:#f8fafc;
  color:#475569;
  font-weight:600;
}

tr:not(:last-child){
  border-bottom:1px solid #e5e7eb;
}

/* ===== Role Badge ===== */
.role{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:6px 14px;
  border-radius:999px;
  font-size:13px;
  font-weight:700;
  letter-spacing:.2px;
}

.role i{
  font-style:normal;
}

/* Admin */
.role.admin{
  background:linear-gradient(135deg,#fee2e2,#fecaca);
  color:#7f1d1d;
  box-shadow:0 0 0 1px #fecaca inset;
}

/* Customer */
.role.customer{
  background:linear-gradient(135deg,#dcfce7,#bbf7d0);
  color:#14532d;
  box-shadow:0 0 0 1px #bbf7d0 inset;
}

/* Restaurant Staff */
.role.restaurant_staff{
  background:linear-gradient(135deg,#e0e7ff,#c7d2fe);
  color:#1e3a8a;
  box-shadow:0 0 0 1px #c7d2fe inset;
}

/* Delivery Driver */
.role.delivery_driver{
  background:linear-gradient(135deg,#cffafe,#a5f3fc);
  color:#0f766e;
  box-shadow:0 0 0 1px #a5f3fc inset;
}

.footer{
  margin-top:24px;
  font-size:13px;
  color:var(--muted);
}
</style>
</head>

<body>

<div class="wrapper">

  <!-- ===== Header ===== -->
  <div class="header">
    <h2>👤 User Management</h2>
    <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
  </div>

  <!-- ===== Table ===== -->
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
          <th>Created At</th>
        </tr>
      </thead>
      <tbody>
      <?php while($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td>
            <?php
              $role = strtolower($row['role']);
              $label = ucwords(str_replace('_',' ', $role));

              $icon = match($role){
                'admin' => '🛡️',
                'customer' => '👤',
                'restaurant_staff' => '🍽️',
                'delivery_driver' => '🛵',
                default => '❓'
              };
            ?>
            <span class="role <?= $role ?>">
              <i><?= $icon ?></i>
              <?= $label ?>
            </span>
          </td>
          <td><?= $row['created_at'] ?></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <div class="footer">
    Showing all registered users
  </div>

</div>

</body>
</html>
