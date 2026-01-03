<?php
session_start();
require "config/db.php";

if(!isset($_SESSION['user_id'])){ 
    header("Location: login.php"); 
    exit; 
}

$order_id = $_GET['order_id'] ?? 0;
$q = mysqli_query($conn,"SELECT * FROM orders WHERE id=$order_id");
$order = mysqli_fetch_assoc($q);

if(!$order){ die("Order Not Found"); }

/* ===========================
   STATUS映射数据库→流程进度
=========================== */
$db_status = strtolower($order['status']); // 防止ENUM大小写问题

$map = [
    'pending'     => 0,
    'preparing'   => 1,
    'ready'       => 2, // 若不需要Ready可和Preparing合并
    'delivering'  => 3,
    'completed'   => 4,
    'cancelled'   => 4  // 取消就直接视为终止
];

$current_step = $map[$db_status] ?? 0;

$steps = ["Order Placed","Preparing","Ready","Delivering","Completed"];

/* 完成订单自动跳评价 */
if($db_status === "completed"){
    header("Refresh:2; url=feedback.php?order_id=$order_id");
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Order Tracking</title>

<style>
body{
  margin:0;
  background:#0f172a;
  font-family:Segoe UI, sans-serif;
  color:#e2e8f0;
  display:flex;
  justify-content:center;
  align-items:center;
  min-height:100vh;
}

.container{
  background:#1e293b;
  padding:40px 60px;
  border-radius:20px;
  width:460px;
  box-shadow:0 0 50px rgba(0,0,0,.4);
  animation:fade .8s ease;
}
@keyframes fade{from{opacity:0;transform:translateY(20px);}to{opacity:1;}}

h2{text-align:center;margin-bottom:10px;font-size:26px;}
#order{text-align:center;margin-bottom:35px;color:#94a3b8;}

.track{
  border-left:3px solid #34495e;
  margin-left:25px;
  padding-left:25px;
}

.step{
  position:relative;
  margin-bottom:42px;
  font-size:16px;
}

.circle{
  width:18px;height:18px;border-radius:50%;
  background:#64748b;
  position:absolute;left:-35px;top:2px;
  transition:.4s;
}

/* 过往步骤 */
.completed .circle{background:#22c55e;box-shadow:0 0 12px #22c55e;}
.completed span{color:#22c55e;font-weight:bold;}

/* 当前步骤 */
.active .circle{
  background:#38bdf8;
  box-shadow:0 0 15px #38bdf8;
  animation:pulse 1.2s infinite;
}
@keyframes pulse{
  0%{transform:scale(1);}
  50%{transform:scale(1.25);}
  100%{transform:scale(1);}
}

.finish-box{
  text-align:center;
  margin-top:20px;
}
.finish-btn{
  background:#22c55e;
  padding:12px 20px;
  border-radius:10px;
  text-decoration:none;
  color:#fff;
  font-weight:bold;
  transition:.25s;
}
.finish-btn:hover{background:#16a34a;}
.note{text-align:center;color:#94a3b8;margin-top:18px;}
</style>
</head>

<body>
<div class="container">

<h2>🛵 Delivery Tracking</h2>
<div id="order">Order #<?= $order_id ?></div>

<div class="track">
<?php
foreach($steps as $i=>$text){
    $class = ($i < $current_step) ? "step completed" :
             (($i == $current_step) ? "step active" : "step");
    echo "<div class='$class'>
            <div class='circle'></div>
            <span>$text</span>
          </div>";
}
?>
</div>

<?php if($db_status=='completed'): ?>
<div class="finish-box">
  <a class="finish-btn" href="feedback.php?order_id=<?= $order_id ?>">✨ Leave Feedback</a>
</div>

<?php else: ?>
<p class="note">⏳ Waiting for restaurant / rider updates...</p>
<p class="note" style="margin-top:5px;">Auto update enabled</p>
<?php endif; ?>

</div>

<script>
// 自动每3秒获取订单状态（无刷新实时更新）
setInterval(() => {
    fetch("order_status_api.php?order_id=<?= $order_id ?>")
    .then(res => res.json())
    .then(data => {
        if(data.status){
            if(data.status.toLowerCase() === "completed"){
                window.location.href = "feedback.php?order_id=<?= $order_id ?>";
            } else {
                location.reload();
            }
        }
    });
},3000);
</script>

</body>
</html>
