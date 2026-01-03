<?php
session_start();
require "../config/db.php";

$id=intval($_GET['id']);

mysqli_query($conn,"
UPDATE delivery
SET status='delivering', started_at=NOW()
WHERE id=$id
");
header("Location: dashboard.php");
exit;
