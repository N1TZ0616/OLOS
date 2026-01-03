<?php
require "../config/db.php";
header("Content-Type: application/json");

$range = $_GET['range'] ?? 'today';

if ($range === 'today') {
  $where = "created_at >= NOW() - INTERVAL 24 HOUR";
  $label = "DATE_FORMAT(created_at,'%H:00')";
  $group = "HOUR(created_at)";
}
elseif ($range === '7') {
  $where = "created_at >= CURDATE() - INTERVAL 7 DAY";
  $label = "DATE(created_at)";
  $group = "DATE(created_at)";
}
else {
  $where = "created_at >= CURDATE() - INTERVAL 30 DAY";
  $label = "DATE(created_at)";
  $group = "DATE(created_at)";
}

/* ===== Revenue + Orders (same rows, always aligned) ===== */
$q = mysqli_query($conn,"
  SELECT
    $label AS label,
    SUM(CASE WHEN status='Completed' THEN total_amount ELSE 0 END) AS revenue,
    COUNT(*) AS orders
  FROM orders
  WHERE $where
  GROUP BY $group
  ORDER BY label
");

$labels=[]; $revenue=[]; $orders=[];

while($r=mysqli_fetch_assoc($q)){
  $labels[]=$r['label'];
  $revenue[]=(float)$r['revenue'];
  $orders[]=(int)$r['orders'];
}

echo json_encode([
  "labels"=>$labels,
  "revenue"=>$revenue,
  "orders"=>$orders,
  "updated"=>date("Y-m-d H:i:s")
]);
