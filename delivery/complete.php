<?php
session_start();
require "../config/db.php";

/* Rider login check */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$id = (int)$_GET['id'];

/* ======================
   Get order_id
====================== */
$res = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT order_id FROM delivery WHERE id = $id"
));

if (!$res) {
    die("Delivery not found.");
}

$order_id = $res['order_id'];

/* ======================
   Complete delivery
====================== */
mysqli_query($conn, "
UPDATE delivery
SET status = 'completed', completed_at = NOW()
WHERE id = $id
");

/* ======================
   🔥 Sync order status
====================== */
mysqli_query($conn, "
UPDATE orders
SET status = 'Completed'
WHERE id = $order_id
");

/* ======================
   Redirect
====================== */
header("Location: success.php");
exit;
