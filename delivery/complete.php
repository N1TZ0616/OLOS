<?php
session_start();
require "../config/db.php";

$id=intval($_GET['id']);

mysqli_query($conn,"
UPDATE delivery
SET status='completed', completed_at=NOW()
WHERE id=$id
");

header("Location: success.php");
exit;
