<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

/* Update status */
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $status   = $_POST['status'];
    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$order_id");
}

/* Search & filter */
$search = $_GET['search'] ?? '';
$filter = $_GET['status'] ?? '';

$where = "1";
if ($search !== '') {
    $search = mysqli_real_escape_string($conn,$search);
    $where .= " AND (o.id LIKE '%$search%' OR u.username LIKE '%$search%')";
}
if ($filter !== '') {
    $where .= " AND o.status='$filter'";
}

/* Fetch orders */
$sql = "
SELECT o.*, u.username
FROM orders o
JOIN users u ON o.user_id = u.id
WHERE $where
ORDER BY o.created_at DESC
";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin | Orders</title>

<style>
:root{
  --bg:#F4E04D;
  --card:#fff;
  --primary:#17C3B2;
  --border:#e5e7eb;
  --shadow:0 14px 40px rgba(0,0,0,.15);
}
body{
  margin:0;
  font-family:Segoe UI, Arial;
  background:var(--bg);
}
.container{
  max-width:1200px;
  margin:40px auto;
  background:var(--card);
  padding:30px;
  border-radius:22px;
  box-shadow:var(--shadow);
}
h2{margin:0 0 20px}

/* FILTER BAR */
.filters{
  display:flex;
  gap:12px;
  margin-bottom:20px;
}
input, select{
  padding:10px;
  border-radius:10px;
  border:1px solid var(--border);
}
button{
  padding:10px 18px;
  background:var(--primary);
  border:none;
  color:white;
  border-radius:10px;
  cursor:pointer;
}

/* TABLE */
table{
  width:100%;
  border-collapse:collapse;
}
th,td{
  padding:14px;
  border-bottom:1px solid var(--border);
  text-align:center;
}
th{
  background:#f9fafb;
  text-transform:uppercase;
  font-size:13px;
}

/* STATUS BADGE */
.badge{
  padding:6px 12px;
  border-radius:999px;
  font-size:12px;
  font-weight:600;
  display:inline-block;
}
.pending{background:#fde68a;color:#92400e;}
.confirmed{background:#bfdbfe;color:#1e3a8a;}
.preparing{background:#fca5a5;color:#7f1d1d;}
.delivering{background:#a7f3d0;color:#065f46;}
.completed{background:#bbf7d0;color:#14532d;}
.cancelled{background:#fecaca;color:#7f1d1d;}

.back{
  display:inline-block;
  margin-bottom:20px;
  text-decoration:none;
  color:white;
  background:var(--primary);
  padding:10px 18px;
  border-radius:10px;
}
</style>
</head>

<body>
<div class="container">

<a class="back" href="dashboard.php">← Dashboard</a>

<h2>📦 Manage Orders</h2>

<form class="filters" method="get">
  <input type="text" name="search" placeholder="Search ID / User" value="<?= htmlspecialchars($search) ?>">
  <select name="status">
    <option value="">All Status</option>
    <?php foreach(['pending','confirmed','preparing','delivering','completed','cancelled'] as $s): ?>
      <option value="<?= $s ?>" <?= $filter===$s?'selected':'' ?>>
        <?= ucfirst($s) ?>
      </option>
    <?php endforeach; ?>
  </select>
  <button>Filter</button>
</form>

<table>
<tr>
  <th>ID</th>
  <th>User</th>
  <th>Total</th>
  <th>Status</th>
  <th>Update</th>
</tr>

<?php while($o=mysqli_fetch_assoc($result)): ?>
<tr>
  <td>#<?= $o['id'] ?></td>
  <td><?= htmlspecialchars($o['username']) ?></td>
  <td>RM <?= number_format($o['total_amount'],2) ?></td>
  <td>
    <span class="badge <?= $o['status'] ?>">
      <?= ucfirst($o['status']) ?>
    </span>
  </td>
  <td>
    <form method="post">
      <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
      <select name="status">
        <?php foreach(['pending','confirmed','preparing','delivering','completed','cancelled'] as $s): ?>
          <option value="<?= $s ?>" <?= $o['status']===$s?'selected':'' ?>>
            <?= ucfirst($s) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <button name="update_status">Save</button>
    </form>
  </td>
</tr>
<?php endwhile; ?>
</table>

</div>
</body>
</html>
