<?php
session_start();
require "config/db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$feedback_id = intval($_GET['id']);
$user_id     = $_SESSION['user_id'];
$role        = $_SESSION['role'] ?? 'customer';

/* 获取反馈信息 */
$fb = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT f.*, o.total_amount
    FROM feedback f 
    JOIN orders o ON o.id=f.order_id
    WHERE f.id=$feedback_id
"));

if(!$fb) die("Feedback not found");

/* 获取聊天记录 */
$chat = mysqli_query($conn,"
    SELECT * FROM feedback_chat
    WHERE feedback_id=$feedback_id
    ORDER BY created_at ASC
");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Feedback Chat</title>

<style>
body{margin:0;background:#0f172a;color:#e2e8f0;font-family:Segoe UI,system-ui;
display:flex;justify-content:center;align-items:center;min-height:100vh;padding:30px;}

.box{width:600px;background:#1e293b;padding:25px;border-radius:16px;
box-shadow:0 20px 60px rgba(0,0,0,.6);}

.header{font-size:22px;font-weight:bold;margin-bottom:10px;}
.order{color:#94a3b8;margin-bottom:15px;}

.chat-box{height:400px;overflow-y:auto;background:#0f172a;padding:15px;
border-radius:12px;margin-bottom:15px;}

.msg{margin-bottom:15px;max-width:80%;padding:10px 15px;border-radius:10px;
font-size:15px;white-space:pre-wrap;}

.customer{background:#2563eb;color:white;margin-right:auto;}
.admin{background:#059669;color:white;margin-left:auto;text-align:right;}

textarea{width:100%;height:70px;padding:10px;border-radius:10px;
resize:none;margin-bottom:10px;}

.btn{width:100%;padding:12px;border:none;border-radius:10px;background:#14b8a6;
color:white;font-size:16px;font-weight:bold;}
.btn:hover{background:#0d8d7e}
</style>
</head>

<body>
<div class="box">

<div class="header">⭐ Feedback Chat</div>
<div class="order">Order #<?= $fb['order_id'] ?> — RM <?= $fb['total_amount'] ?></div>

<div class="chat-box" id="chat">
<?php while($m=mysqli_fetch_assoc($chat)): ?>
    <div class="msg <?= ($m['sender']=='admin')?'admin':'customer' ?>">
        <?= htmlspecialchars($m['message']) ?><br>
        <small><?= $m['created_at'] ?></small>
    </div>
<?php endwhile; ?>
</div>

<form method="post" action="feedback_chat_send.php">
    <input type="hidden" name="feedback_id" value="<?= $feedback_id ?>">
    <input type="hidden" name="sender" value="<?= ($role=='admin')?'admin':'customer' ?>">
    <textarea name="message" placeholder="Type message..." required></textarea>
    <button class="btn">Send</button>
</form>

</div>

<script>
let box=document.getElementById("chat");
box.scrollTop=box.scrollHeight;
</script>
</body>
</html>
