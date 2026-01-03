<?php
// home.php
// Dashboard after login. Shows welcome and links to shop/cart.

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db_connect.php';

// require login
if (empty($_SESSION['user']) || !is_array($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$user = $_SESSION['user'];

// determine display name
$displayName = $user['full_name'] ?? $user['username'] ?? $user['email'] ?? 'User';

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
<title>Home — Food Delivery</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
:root{--page-bg:#f0e0df;--card:#f6e34b;--accent:#14c0bf;--muted:#6b7280;--maxw:420px;}
html,body{margin:0;font-family:Arial,Helvetica,sans-serif;background:var(--page-bg);color:#111;height:100%;}
.container{max-width:var(--maxw);margin:20px auto;padding:12px;}
.header{display:flex;align-items:center;justify-content:space-between;background:var(--card);padding:12px;border-radius:10px;}
.brand{font-weight:800;}
.link{font-weight:700;text-decoration:none;color:#111;}
.card{background:var(--card);padding:14px;border-radius:12px;margin-top:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);}
.btn{background:var(--accent);color:#fff;padding:10px 14px;border-radius:12px;border:none;font-weight:800;cursor:pointer;}
.btn-logout{background:#0f172a;color:#fff;padding:10px 14px;border-radius:12px;border:none;font-weight:800;cursor:pointer;}
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
      <h2 style="margin:6px 0;">Welcome, <?php echo htmlspecialchars($displayName); ?></h2>
      <p style="margin:6px 0 12px 0;color:var(--muted);">Browse menu and place your order.</p>

      <div style="display:flex;gap:8px;">
        <a href="menu.php"><button class="btn">Start Ordering</button></a>
        <a href="logout.php"><button class="btn-logout">Logout</button></a>
      </div>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Food Delivery</div>
  </div>
</body>
</html>
