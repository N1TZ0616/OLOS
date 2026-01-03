<?php
session_start();
require "config/db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = intval($_GET['order_id'] ?? 0);

/* 验证订单是否属于当前用户且为 pending */
$check = mysqli_query($conn,"
    SELECT id FROM orders 
    WHERE id=$order_id 
      AND user_id=$user_id 
      AND status='pending'
");

if(mysqli_num_rows($check)==1){
    mysqli_query($conn,"
        UPDATE orders 
        SET status='cancelled' 
        WHERE id=$order_id
    ");
}

header("Location: my_orders.php");
exit;
