<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require "config/db.php";

/* LOGIN CHECK */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$category = $_GET['category'] ?? '';
if ($category === '') {
    header("Location: menu.php");
    exit;
}

/* 菜品介绍（不改数据库） */
$descriptions = [
  'American Burger'        => 'Classic beef burger served with fresh lettuce, tomato and house sauce.',
  'Beef Prosperity Burger' => 'Juicy beef patty with signature black pepper sauce.',
  'Chicken Burger'         => 'Crispy chicken fillet with soft bun and fresh vegetables.',
  'Japanese Burger'        => 'Fusion-style burger inspired by Japanese flavors.',
  'Black Pepper Chicken Burger' => 'Freshly grilled chicken burger with black pepper sauce.',
  'Crispy Chicken Burger'  => 'Golden crispy chicken with fresh vegetables.',
];

$stmt = mysqli_prepare($conn, "
  SELECT * FROM menu
  WHERE category = ? AND available = 1
  ORDER BY name
");
mysqli_stmt_bind_param($stmt,"s",$category);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= ucfirst($category) ?> | Menu</title>

<style>
/* ===== 你的原 CSS 完全不动 ===== */
:root{--accent:#17C3B2;}
body{
  margin:0;
  font-family:Segoe UI, system-ui;
  background:
    linear-gradient(rgba(0,0,0,.55), rgba(0,0,0,.55)),
    url("assets/img/bg-orderingol.jpg") center / cover fixed;
  color:white;
}
.header{
  padding:22px 40px;
  display:flex;
  justify-content:space-between;
  align-items:center;
  backdrop-filter: blur(8px);
}
.header h2{margin:0;font-size:28px;}
.back{color:var(--accent);text-decoration:none;font-weight:600;}
.container{max-width:1300px;margin:40px auto 80px;padding:0 40px;}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:32px;}
.card{
  background:white;color:#0f172a;border-radius:28px;padding:16px;
  box-shadow:0 30px 60px rgba(0,0,0,.35);
  display:flex;flex-direction:column;transition:.25s;
}
.card:hover{transform:translateY(-6px);}
.card img{width:100%;height:180px;object-fit:cover;border-radius:18px;}
.card h4{margin:14px 0 6px;font-size:17px;}
.desc{font-size:14px;color:#475569;line-height:1.4;margin-bottom:10px;}
.price{font-weight:700;margin-bottom:14px;}
.card button{
  margin-top:auto;width:100%;padding:12px;border:none;border-radius:14px;
  background:var(--accent);color:white;font-size:14px;font-weight:600;cursor:pointer;
}
</style>
</head>

<body>

<div class="header">
  <h2><?= ucfirst($category) ?></h2>
  <a class="back" href="menu.php">← Back to Menu</a>
</div>

<div class="container">
<div class="grid">

<?php while($item = mysqli_fetch_assoc($result)):
  $desc = $descriptions[$item['name']]
          ?? 'Delicious freshly prepared dish made with quality ingredients.';
?>
  <div class="card">
    <img src="assets/img/<?= htmlspecialchars($item['image']) ?>" alt="">
    <h4><?= htmlspecialchars($item['name']) ?></h4>
    <div class="desc"><?= htmlspecialchars($desc) ?></div>
    <div class="price">RM <?= number_format($item['price'],2) ?></div>

    <!-- ✅ 唯一修改点：Add to Cart 正确提交到 cart_action.php -->
    <form method="post" action="cart_action.php?action=add">
      <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
      <input type="hidden" name="qty" value="1">
      <button type="submit">Add to Cart</button>
    </form>

  </div>
<?php endwhile; ?>

</div>
</div>

</body>
</html>
