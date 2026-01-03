<?php
session_start();
require "config/db.php";

/* 必须登录 */
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* 获取用户全部反馈 */
$sql = mysqli_query($conn,"
    SELECT f.*, o.total_amount
    FROM feedback f
    JOIN orders o ON o.id = f.order_id
    WHERE f.user_id = $user_id
    ORDER BY f.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
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
  max-width:850px;
  margin:auto;
  padding:50px 20px;
}

h2{
  text-align:center;
  font-size:30px;
  margin-bottom:30px;
  display:flex;
  justify-content:center;
  align-items:center;
  gap:8px;
}

/* feedback card */
.card{
  background:#1e293b;
  padding:22px;
  margin-bottom:18px;
  border-radius:18px;
  box-shadow:0 10px 30px rgba(0,0,0,.35);
  border-left:6px solid #14b8a6;
}

/* rating */
.stars{
  color:#facc15;
  font-size:19px;
  font-weight:bold;
  margin-bottom:10px;
}

/* comment */
.comment{
  font-size:15px;
  color:#cbd5e1;
  white-space:pre-line;
}

/* admin reply */
.reply{
  margin-top:10px;
  padding:10px 12px;
  background:#064e3b;
  border-left:4px solid #22c55e;
  border-radius:8px;
  color:#a7f3d0;
}

/* Chat Btn */
.chat-btn{
  display:inline-block;
  margin-top:18px;
  background:#0ea5e9;
  padding:10px 14px;
  text-decoration:none;
  border-radius:10px;
  color:white;
  font-weight:bold;
}
.chat-btn:hover{background:#0284c7}

/* back btn */
.back-btn{
  display:block;
  width:220px;
  margin:35px auto 0;
  padding:12px;
  background:#14b8a6;
  text-align:center;
  color:white;
  font-weight:bold;
  border-radius:10px;
  text-decoration:none;
}
.back-btn:hover{background:#0d8d7e}

.date{
  font-size:13px;
  color:#94a3b8;
  margin-top:6px;
}
</style>
</head>

<body>

<div class="container">

<h2>⭐ My Feedback</h2>

<?php if(mysqli_num_rows($sql)==0): ?>
    <p style="text-align:center;color:#94a3b8;font-size:18px;margin-top:20px;">
        You haven't submitted any feedback yet.
    </p>

<?php else: ?>
<?php while($f=mysqli_fetch_assoc($sql)): ?>
<div class="card">

    <div class="stars"><?=str_repeat("⭐",$f['rating'])?></div>

    <div class="comment">
        <?=nl2br(htmlspecialchars($f['comment']))?>
    </div>

    <?php if($f['reply']): ?>
    <div class="reply">
        <b>Admin Reply:</b><br>
        <?=nl2br(htmlspecialchars($f['reply']))?>
    </div>
    <?php endif; ?>

    <div class="date">🕒 <?=$f['created_at']?></div>

    <a href="chat_feedback.php?id=<?=$f['id']?>" class="chat-btn">💬 View Chat / Continue Chat</a>

</div>
<?php endwhile; ?>
<?php endif; ?>

<a href="menu.php" class="back-btn">← Back to Menu</a>

</div>
</body>
</html>
