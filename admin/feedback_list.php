<?php
session_start();
require "../config/db.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="admin"){
    header("Location: ../login.php");exit;
}

$result=mysqli_query($conn,"
    SELECT f.*,u.username,o.total_amount
    FROM feedback f
    JOIN users u ON u.id=f.user_id
    JOIN orders o ON o.id=f.order_id
    ORDER BY f.created_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Feedback Center</title>

<style>
body{background:#0f172a;color:white;font-family:Segoe UI;margin:0;padding:40px;}
.box{width:1100px;margin:auto;}
table{width:100%;border-collapse:collapse;margin-top:20px;}
th,td{padding:12px;border-bottom:1px solid #334155;text-align:center;}
th{background:#1e293b;}
.btn{
 background:#22c55e;padding:6px 14px;border-radius:8px;color:white;text-decoration:none;
}
.view{background:#0ea5e9;}
</style>
</head>

<body>
<div class="box">

<h2>⭐ Customer Feedback</h2>

<table>
<tr>
<th>Order</th><th>User</th><th>Rating</th><th>Comment</th><th>Chat</th><th>Time</th>
</tr>

<?php while($f=mysqli_fetch_assoc($result)){ ?>
<tr>
<td>#<?=$f['order_id']?></td>
<td><?=$f['username']?></td>
<td><?=str_repeat("⭐",$f['rating'])?></td>
<td><?=$f['comment']?></td>
<td><a class="btn" href="../chat_feedback.php?id=<?=$f['id']?>">Open Chat</a></td>
<td><?=$f['created_at']?></td>
</tr>
<?php } ?>

</table>
</div>
</body>
</html>
