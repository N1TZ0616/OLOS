<?php
session_start();
require "../config/db.php";

/* ======================
   LOGIN + ROLE CHECK
====================== */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurant_staff') {
    header("Location: ../index.php");
    exit;
}

/* ======================
   GET ORDER ID
====================== */
if (!isset($_GET['order_id'])) {
    die("Order ID not provided.");
}

$order_id = (int)$_GET['order_id'];

/* ======================
   GET STAFF RESTAURANT
====================== */
$user_id = $_SESSION['user_id'];

$sql = "SELECT restaurant_id 
        FROM users 
        WHERE id = $user_id 
          AND role = 'restaurant_staff'
        LIMIT 1";

$res = mysqli_query($conn, $sql);
$staff = mysqli_fetch_assoc($res);

if (!$staff || !$staff['restaurant_id']) {
    die("Restaurant not assigned.");
}

$restaurant_id = $staff['restaurant_id'];

/* ======================
   FETCH ORDER (SECURITY)
====================== */
$sql = "
SELECT o.*, u.username
FROM orders o
JOIN users u ON o.user_id = u.id
WHERE o.id = $order_id
  AND o.restaurant_id = $restaurant_id
LIMIT 1
";

$orderResult = mysqli_query($conn, $sql);
$order = mysqli_fetch_assoc($orderResult);

if (!$order) {
    die("Order not found or access denied.");
}

/* ======================
   FETCH ORDER ITEMS
====================== */
$sql = "
SELECT 
  m.name,
  od.quantity,
  od.unit_price,
  od.subtotal
FROM order_details od
JOIN menu m ON od.menu_item_id = m.id
WHERE od.order_id = $order_id
";

$items = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Order Details</title>

<style>
body{
  font-family:Segoe UI, Arial;
  background:#f4f4f4;
  padding:30px;
}
.box{
  background:white;
  padding:25px;
  border-radius:10px;
  max-width:700px;
  margin:auto;
  box-shadow:0 8px 20px rgba(0,0,0,.15);
}
table{
  width:100%;
  border-collapse:collapse;
  margin-top:15px;
}
th, td{
  padding:10px;
  border-bottom:1px solid #ddd;
  text-align:left;
}
th{
  background:#F4E04D;
}
.total{
  text-align:right;
  font-size:18px;
  font-weight:bold;
}
a{
  text-decoration:none;
  color:white;
}
.back{
  display:inline-block;
  margin-top:20px;
  background:#17C3B2;
  padding:10px 16px;
  border-radius:6px;
}
</style>
</head>

<body>

<div class="box">

<h2>📦 Order #<?= $order['id'] ?></h2>
<p><strong>Customer:</strong> <?= htmlspecialchars($order['username']) ?></p>
<p><strong>Status:</strong> <?= $order['status'] ?></p>

<table>
<tr>
  <th>Item</th>
  <th>Qty</th>
  <th>Unit Price</th>
  <th>Subtotal</th>
</tr>

<?php while ($i = mysqli_fetch_assoc($items)): ?>
<tr>
  <td><?= htmlspecialchars($i['name']) ?></td>
  <td><?= $i['quantity'] ?></td>
  <td>RM <?= number_format($i['unit_price'],2) ?></td>
  <td>RM <?= number_format($i['subtotal'],2) ?></td>
</tr>
<?php endwhile; ?>

<tr>
  <td colspan="3" class="total">Total</td>
  <td class="total">RM <?= number_format($order['total_amount'],2) ?></td>
</tr>
</table>

<a class="back" href="orders.php">← Back to Orders</a>

</div>

</body>
</html>
