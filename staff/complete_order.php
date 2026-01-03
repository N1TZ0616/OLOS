<?php
session_start();
require "../config/db.php";

$order_id = (int)$_POST['order_id'];

/* Get chef */
$res = mysqli_query($conn,"
SELECT chef_name FROM orders WHERE id=$order_id
");
$row = mysqli_fetch_assoc($res);
$chef_name = $row['chef_name'];

/* Set order Ready */
mysqli_query($conn,"
UPDATE orders SET status='Ready' WHERE id=$order_id
");

/* Release chef */
mysqli_query($conn,"
UPDATE chefs SET status='Available' WHERE name='$chef_name'
");

header("Location: orders.php");
exit;
