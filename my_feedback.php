<?php
session_start();
require "config/db.php";

/* 必须登录 */
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* 读取当前用户所有反馈 + admin回复 */
$q = mysqli_query($conn,"
    SELECT f.*, o.total_amount, o.created_at AS order_time
    FROM feedback f
    JOIN orders o ON f.order_id=o.id
    WHERE f.user_id=$user_id
    ORDER BY f.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Feedback</title>

<style>
body{
    margin:0;
    background:radial-gradient(circle at top,#0f172a,#020617);
    font-family:Segoe UI,system-ui;
    color:#e2e8f0;
}
.container{
    max-width:900px;
    margin:60px auto;
    background:#1e293b;
    padding:40px;
    border-radius:20px;
    box-shadow:0 25px 70px rgba(0,0,0,.55);
}

/* Title */
h2{
    text-align:center;
    margin-bottom:25px;
    font-size:30px;
    color:#38bdf8;
}

/* back button */
.back{
    display:inline-block;
    margin-bottom:18px;
    background:#14b8a6;
    padding:10px 18px;
    color:white;
    border-radius:10px;
    text-decoration:none;
    font-weight:bold;
}

/* Feedback Card */
.card{
    background:#0f172a;
    padding:22px;
    border-radius:16px;
    margin-bottom:16px;
    border-left:5px solid #14b8a6;
}
.order{
    font-size:18px;
    font-weight:bold;
}
.price{color:#38bdf8;}
.date{color:#94a3b8;font-size:14px;margin-top:6px;}
.rating{color:#facc15;font-size:18px;margin-top:6px;}
.comment{
    background:#1e293b;
    padding:12px;
    border-radius:10px;
    margin-top:12px;
    color:#e2e8f0;
}
.reply{
    background:#22c55e22;
    border-left:4px solid #22c55e;
    padding:12px;
    margin-top:10px;
    border-radius:10px;
    color:#bef7c5;
    font-style:italic;
}
.empty{
    text-align:center;
    margin-top:40px;
    color:#94a3b8;
    font-size:18px;
}
</style>
</head>

<body>

<div class="container">
<a href="menu.php" class="back">← Back to Menu</a>
<h2>⭐ My Feedback</h2>

<?php if(mysqli_num_rows($q)==0): ?>
<p class="empty">You have not submitted any feedback yet.</p>

<?php else: while($f=mysqli_fetch_assoc($q)): ?>
<div class="card">

    <div class="order">Order #<?= $f['order_id'] ?> —
        <span class="price">RM <?= number_format($f['total_amount'],2) ?></span>
    </div>

    <div class="rating"><?= str_repeat("⭐", $f['rating']) ?></div>

    <div class="comment"><?= nl2br($f['comment']) ?></div>

    <?php if($f['reply']!=""){ ?>
        <div class="reply">💬 Admin Reply: <?= nl2br($f['reply']) ?></div>
    <?php } else { ?>
        <div class="reply" style="opacity:.6;">Awaiting admin response...</div>
    <?php } ?>

    <div class="date">📅 <?= date("Y-m-d h:i A", strtotime($f['order_time'])) ?></div>

</div>
<?php endwhile; endif; ?>

</div>

</body>
</html>
