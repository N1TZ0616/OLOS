<?php
session_start();
require "config/db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$order_id = (int)$_GET['order_id'];
$user_id  = $_SESSION['user_id'];

/* 是否存在反馈 */
$check = mysqli_query($conn,"SELECT * FROM feedback WHERE order_id=$order_id AND user_id=$user_id");
$feedback = mysqli_fetch_assoc($check);

/* ============================
   提交新评价
============================ */
if(isset($_POST['send'])){
    $rating  = intval($_POST['rating']);
    $comment = mysqli_real_escape_string($conn,$_POST['comment']);

    mysqli_query($conn,"
        INSERT INTO feedback(order_id,user_id,rating,comment,created_at)
        VALUES($order_id,$user_id,$rating,'$comment',NOW())
    ");

    echo "<script>alert('Thank you for your feedback!');location='my_orders.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Feedback</title>

<style>
body{
    margin:0; background:url('assets/img/bg-orderingol.jpg') center/cover fixed;
    backdrop-filter: blur(6px);
    font-family:Segoe UI,system-ui; 
    display:flex;justify-content:center;align-items:center;min-height:100vh;
    color:white;
}
.box{
    width:460px;padding:40px;
    background:rgba(0,0,0,.55);
    border-radius:22px;
    box-shadow:0 20px 60px rgba(0,0,0,.6);
    text-align:center;
}
h2{margin-bottom:10px;font-size:28px;font-weight:700}

/* 星星 */
.stars i{
    font-size:40px;color:#475569;cursor:pointer;transition:.3s;
}
.stars i.active{color:#fbbf24;text-shadow:0 0 12px #fbbf24;}

/* 文本框 */
textarea{
    width:100%;height:120px;border-radius:12px;border:none;resize:none;
    padding:14px;font-size:15px;margin-top:15px;
}

/* 按钮 */
.btn{
    width:100%;padding:14px;margin-top:22px;
    background:#22c55e;border:none;color:white;font-size:17px;
    border-radius:12px;font-weight:bold;cursor:pointer;transition:.3s;
}
.btn:hover{background:#16a34a}

/* 已提交评价展示 */
.feedback-box{
    background:rgba(255,255,255,.12);
    border-radius:12px;padding:18px;margin-top:20px;text-align:left;
}
.label{color:#facc15;font-weight:bold;margin-bottom:6px}
.admin-reply{
    background:rgba(34,197,94,.25);
    padding:14px;border-radius:10px;margin-top:10px;
    border-left:4px solid #22c55e;
}
</style>
</head>

<body>

<div class="box">

<?php if(!$feedback): ?> 

<h2>⭐ Rate Your Order</h2>

<form method="post">
<input type="hidden" name="rating" id="rating" required>
<div class="stars" id="stars">
  <i data-v="1">★</i><i data-v="2">★</i><i data-v="3">★</i><i data-v="4">★</i><i data-v="5">★</i>
</div>

<textarea name="comment" placeholder="Share your dining experience..." required></textarea>
<button class="btn" name="send">Submit Feedback</button>
</form>

<?php else: ?> 

<h2>✨ Feedback Submitted</h2>

<div class="feedback-box">
    <div class="label">Your Rating:</div>
    <?= str_repeat("⭐",$feedback['rating']) ?><br><br>

    <div class="label">Your Comment:</div>
    <?= nl2br(htmlspecialchars($feedback['comment'])) ?>

    <?php if($feedback['admin_reply']): ?>
        <div class="admin-reply">
            <b>Admin Reply:</b><br>
            <?= nl2br(htmlspecialchars($feedback['admin_reply'])) ?><br>
            <small>🕒 <?= $feedback['reply_time']?></small>
        </div>
    <?php else: ?>
        <p style="margin-top:12px;color:#cbd5e1">Waiting for restaurant response...</p>
    <?php endif; ?>
</div>

<a href="my_orders.php" class="btn" style="margin-top:25px;background:#0ea5e9">Return to Orders</a>

<?php endif; ?>

</div>

<script>
let rating = 0;
document.querySelectorAll("#stars i").forEach(star=>{
    star.onclick = function(){
        rating=this.dataset.v;
        document.getElementById("rating").value=rating;
        document.querySelectorAll("#stars i").forEach(s=>s.classList.remove("active"));
        this.classList.add("active");
        let p=this.previousElementSibling;
        while(p){p.classList.add("active");p=p.previousElementSibling;}
    }
});
</script>

</body>
</html>
