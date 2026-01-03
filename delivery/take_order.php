<?php
session_start();
require "../config/db.php";

$rider_id = $_SESSION['user_id'];
$order_id = intval($_GET['order_id']);

mysqli_query($conn, "
INSERT INTO delivery (order_id, rider_id)
VALUES ($order_id, $rider_id)
");

mysqli_query($conn, "
UPDATE orders SET status='delivering'
WHERE id=$order_id
");

header("Location: dashboard.php");
