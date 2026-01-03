<?php
session_start();
require "config/db.php";

/* ======================
   必须登录（放最前面）
====================== */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* ======================
   Fetch user avatar
====================== */
$userInfo = mysqli_fetch_assoc(
    mysqli_query(
        $mysqli,
        "SELECT avatar FROM users WHERE id = $user_id LIMIT 1"
    )
);

$avatar = (!empty($userInfo['avatar']))
    ? "assets/avatars/" . htmlspecialchars($userInfo['avatar'])
    : "assets/avatars/default.png";

/* ======================
   未读反馈数量
====================== */
$unreadRow = mysqli_fetch_assoc(
    mysqli_query(
        $mysqli,
        "SELECT COUNT(*) AS c 
         FROM feedback 
         WHERE user_id = $user_id AND user_unread = 1"
    )
);
$unread = $unreadRow['c'] ?? 0;

/* ======================
   TODAY PICKS
====================== */
$today = mysqli_query(
    $mysqli,
    "SELECT * FROM menu
     WHERE available = 1
     ORDER BY RAND()
     LIMIT 4"
);

/* ======================
   CATEGORIES
====================== */
$categories = mysqli_query(
    $mysqli,
    "SELECT category, MIN(image) AS image
     FROM menu
     WHERE available = 1
     GROUP BY category
     ORDER BY category"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>orderingol | Menu</title>

<style>
:root{
  --accent:#17C3B2;
  --yellow:#FDE68A;
  --btn:#0ea5e9;
}

/* 红点样式 */
.red-dot{
  display:inline-block;
  width:10px;height:10px;background:#ff3b30;
  border-radius:50%;margin-left:6px;
}

/* 用户头像 */
.user-avatar{
  width:34px;
  height:34px;
  border-radius:50%;
  object-fit:cover;
  border:2px solid #17C3B2;
}

/* ------原样式保持不变------ */
body{
  margin:0;
  font-family:Segoe UI, system-ui;
  background:
    linear-gradient(rgba(0,0,0,.55), rgba(0,0,0,.55)),
    url("assets/img/bg-orderingol.jpg") center / cover fixed;
  color:white;
}
.header{padding:22px 48px;display:flex;justify-content:space-between;align-items:center;}
.header-left h2{margin:0;font-size:28px;font-weight:700;}

.header-right{display:flex;align-items:center;gap:12px;}
.menu-btn{
  background:#ffffff22;backdrop-filter:blur(6px);
  padding:10px 18px;border-radius:30px;color:white;
  font-weight:600;text-decoration:none;transition:.25s;
  display:flex;align-items:center;gap:8px;
}
.menu-btn:hover{background:#ffffff55;transform:translateY(-2px);}
.logout{background:var(--accent);padding:10px 18px;border-radius:30px;color:white;font-weight:600;text-decoration:none;}
.logout:hover{background:#0fa399;}

.container{max-width:1300px;margin:40px auto 80px;padding:0 40px;}
.section-title{font-size:26px;margin:50px 0 22px;display:flex;align-items:center;gap:10px;}
.section-title::before{content:"";width:6px;height:28px;background:#facc15;border-radius:4px;}

.picks{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:28px;}
.pick{background:var(--yellow);border-radius:26px;padding:16px;color:#111827;box-shadow:0 20px 40px rgba(0,0,0,.45);}
.pick img{width:100%;height:170px;object-fit:cover;border-radius:18px;background:white;}
.pick h4{margin:14px 0 6px;}
.pick-price{font-weight:700;margin-bottom:10px;}
.add-btn{width:100%;padding:10px;border:none;border-radius:12px;background:var(--accent);color:white;font-weight:600;cursor:pointer;}
.add-btn:hover{background:#0fa399;}

.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:36px;}
.cat-card{position:relative;height:260px;border-radius:32px;overflow:hidden;box-shadow:0 30px 60px rgba(0,0,0,.55);transition:.3s;}
.cat-card:hover{transform:translateY(-6px);}
.cat-card img{width:100%;height:100%;object-fit:cover;}
.cat-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.7),rgba(0,0,0,.15));}
.cat-title{position:absolute;left:26px;bottom:26px;font-size:28px;font-weight:700;text-transform:capitalize;}

@media(max-width:600px){
  .header,.container{padding-left:20px;padding-right:20px;}
  .header-right{flex-wrap:wrap;}
}
</style>
</head>

<body>

<div class="header">
  <div class="header-left">
    <h2>🍔 orderingol Menu</h2>
  </div>

  <div class="header-right">

      <!-- My Account（显示头像） -->
      <a class="menu-btn" href="profile.php">
        <img src="<?= $avatar ?>" class="user-avatar">
        My Account
      </a>

      <a class="menu-btn" href="my_orders.php">📦 My Orders</a>

      <a class="menu-btn" href="user_feedback_history.php">
          ⭐ My Feedback
          <?php if($unread > 0): ?>
            <span class="red-dot"></span>
          <?php endif; ?>
      </a>

      <a class="logout" href="logout.php">Logout</a>
  </div>
</div>

<div class="container">

<div class="section-title">🌟 Today’s Picks</div>
<div class="picks">
<?php while($p=mysqli_fetch_assoc($today)): ?>
<div class="pick">
    <img src="assets/img/<?= htmlspecialchars($p['image']) ?>">
    <h4><?= htmlspecialchars($p['name']) ?></h4>
    <div class="pick-price">RM <?= number_format($p['price'],2) ?></div>

    <form method="post" action="cart_action.php?action=add">
      <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
      <input type="hidden" name="qty" value="1">
      <button class="add-btn">Add to Cart</button>
    </form>
</div>
<?php endwhile; ?>
</div>

<div class="section-title">🍴 Categories</div>
<div class="grid">
<?php while($c=mysqli_fetch_assoc($categories)): ?>
  <a href="category.php?category=<?= urlencode($c['category']) ?>" style="color:inherit;text-decoration:none;">
      <div class="cat-card">
        <img src="assets/img/<?= htmlspecialchars($c['image']) ?>">
        <div class="cat-overlay"></div>
        <div class="cat-title"><?= htmlspecialchars($c['category']) ?></div>
      </div>
  </a>
<?php endwhile; ?>
</div>

</div>
</body>
</html>
