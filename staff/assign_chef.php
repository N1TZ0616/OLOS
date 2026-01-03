<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurant_staff') {
    die("Unauthorized");
}

if (!isset($_POST['order_id'], $_POST['chef_id'])) {
    die("Missing data");
}

date_default_timezone_set("Asia/Kuala_Lumpur");

$order_id = (int)$_POST['order_id'];
$chef_id  = (int)$_POST['chef_id'];

/* =================================================
   1️⃣ BLOCK BUSY CHEF (One chef, one order)
================================================= */
$check = mysqli_query($conn, "
SELECT 1
FROM orders
WHERE chef_name = (SELECT name FROM chefs WHERE id = $chef_id)
  AND status = 'Preparing'
");

if (mysqli_num_rows($check) > 0) {
    die("Chef is already busy.");
}

/* =================================================
   2️⃣ CALCULATE COOKING TIME
   7 minutes per dish
================================================= */
$res = mysqli_query($conn, "
SELECT SUM(quantity) total_qty
FROM order_details
WHERE order_id = $order_id
");

$row = mysqli_fetch_assoc($res);
$total_qty = max(1, (int)$row['total_qty']);

$minutes = $total_qty * 7;

/* 👉 关键：一定要算出 prep_end_time */
$prep_end_time = date("Y-m-d H:i:s", strtotime("+$minutes minutes"));

/* =================================================
   3️⃣ ASSIGN CHEF + SET TIME
   （这是你之前缺失的地方）
================================================= */
mysqli_query($conn, "
UPDATE orders
SET chef_name = (SELECT name FROM chefs WHERE id = $chef_id),
    status = 'Preparing',
    prep_end_time = '$prep_end_time'
WHERE id = $order_id
");

/* =================================================
   4️⃣ UPDATE CHEF STATUS
================================================= */
mysqli_query($conn, "
UPDATE chefs
SET status = 'Busy'
WHERE id = $chef_id
");

header("Location: orders.php");
exit;


