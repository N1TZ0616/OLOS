<?php
require "config/db.php";

$order_id = (int)($_GET['order_id'] ?? 0);

$q = mysqli_query($conn,"SELECT status FROM orders WHERE id=$order_id LIMIT 1");
$res = mysqli_fetch_assoc($q);

echo json_encode([
    "status" => $res['status'] ?? null
]);
?>
