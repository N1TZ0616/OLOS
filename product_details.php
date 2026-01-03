<?php
// product_details.php
// Show full product details and add to cart.

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "Product not found."; exit;
}

$product = null;
$sql = "SELECT `id`,`name`,`description`,`price`,`image`,`category`,`available` FROM `menu` WHERE `id` = ? LIMIT 1";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $res = $stmt->get_result();
        $product = $res->fetch_assoc();
        $res->free();
    }
    $stmt->close();
}

if (!$product) {
    echo "Product not found."; exit;
}

// cart count
$cartCount = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $it) $cartCount += (int)($it['qty'] ?? 0);
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php echo htmlspecialchars($product['name']); ?> — Food Delivery</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
:root{--page-bg:#f0e0df;--card:#f6e34b;--accent:#14c0bf;--muted:#6b7280;--maxw:420px;}
html,body{margin:0;font-family:Arial,Helvetica,sans-serif;background:var(--page-bg);color:#111;}
.container{max-width:var(--maxw);margin:18px auto;padding:12px;}
.header{display:flex;justify-content:space-between;align-items:center;background:var(--card);padding:12px;border-radius:10px;}
.card{background:var(--card);padding:14px;border-radius:12px;margin-top:12px;}
.img{width:100%;height:240px;border-radius:8px;object-fit:cover;background:#fff;}
.title{font-weight:800;margin-top:10px;}
.muted{color:var(--muted);font-size:13px;}
.btn{background:var(--accent);color:#fff;padding:10px;border-radius:10px;border:none;cursor:pointer;font-weight:700;}
.footer{margin-top:18px;text-align:center;color:var(--muted);font-size:13px;}
</style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="brand">Food Delivery</div>
      <div><a class="link" href="cart.php">Cart (<?php echo (int)$cartCount; ?>)</a></div>
    </div>

    <div class="card">
      <?php if (!empty($product['image']) && file_exists(__DIR__.'/products/'.$product['image'])): ?>
        <img class="img" src="<?php echo 'products/' . htmlspecialchars($product['image']); ?>" alt="">
      <?php else: ?>
        <div class="img" style="display:flex;align-items:center;justify-content:center;color:#666;">No Image</div>
      <?php endif; ?>

      <div class="title"><?php echo htmlspecialchars($product['name']); ?></div>
      <div class="muted"><?php echo htmlspecialchars($product['category']); ?></div>
      <p style="margin-top:10px;"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

      <div style="display:flex;justify-content:space-between;align-items:center;margin-top:12px;">
        <div style="font-weight:800;font-size:18px;">$<?php echo number_format($product['price'], 2); ?></div>

        <form method="post" action="cart_action.php" style="display:flex;gap:8px;align-items:center;">
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="product_id" value="<?php echo (int)$product['id']; ?>">
          <input type="number" name="qty" value="1" min="1" style="width:70px;padding:8px;border-radius:8px;border:1px solid #ddd;">
          <button class="btn" type="submit">Add to Cart</button>
        </form>
      </div>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Food Delivery</div>
  </div>
</body>
</html>
