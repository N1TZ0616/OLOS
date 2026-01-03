<?php
session_start();
$cart = $_SESSION['cart'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Cart</title>

<style>
body{
  margin:0;
  font-family:Segoe UI, system-ui;
  background:radial-gradient(circle at top,#0f172a,#020617);
  color:#e5e7eb;
  min-height:100vh;
}
.container{
  max-width:900px;
  margin:80px auto;
  background:#020617;
  border-radius:28px;
  padding:40px;
  box-shadow:0 40px 90px rgba(0,0,0,.7);
}
h2{margin-top:0;}
table{
  width:100%;
  border-collapse:collapse;
}
th,td{
  padding:14px;
  border-bottom:1px solid #1e293b;
}
th{text-align:left;color:#94a3b8;}
input[type=number]{
  width:70px;
  padding:6px;
  border-radius:8px;
  border:none;
}
.actions{
  margin-top:30px;
  display:flex;
  justify-content:space-between;
  align-items:center;
}
.btn{
  padding:12px 22px;
  border-radius:14px;
  border:none;
  background:#14b8a6;
  color:white;
  font-weight:600;
  cursor:pointer;
  text-decoration:none;
}
.btn.dark{
  background:#1e293b;
}
.link{
  color:#94a3b8;
  text-decoration:none;
}
.total{
  font-size:22px;
  font-weight:800;
}
</style>
</head>

<body>

<div class="container">
<h2>Your Cart</h2>

<?php if (empty($cart)): ?>

  <p>Your cart is empty.</p>
  <a class="link" href="menu.php">← Back to Menu</a>

<?php else: ?>

<form method="post" action="cart_action.php?action=update">

<table>
<tr>
  <th>Item</th>
  <th>Price</th>
  <th>Qty</th>
  <th>Subtotal</th>
  <th></th>
</tr>

<?php
$total = 0;
foreach ($cart as $item):
  $sub = $item['price'] * $item['qty'];
  $total += $sub;
?>
<tr>
  <td><?= htmlspecialchars($item['name']) ?></td>
  <td>RM <?= number_format($item['price'],2) ?></td>
  <td>
    <input type="number"
           name="qty[<?= $item['id'] ?>]"
           value="<?= $item['qty'] ?>"
           min="1">
  </td>
  <td>RM <?= number_format($sub,2) ?></td>
  <td>
    <a class="link"
       href="cart_action.php?action=remove&id=<?= $item['id'] ?>">
       Remove
    </a>
  </td>
</tr>
<?php endforeach; ?>
</table>

<div class="actions">
  <a class="link" href="menu.php">← Continue Shopping</a>
  <div>
    <div class="total">Total RM <?= number_format($total,2) ?></div><br>
    <button class="btn dark" type="submit">Update Cart</button>
    <a class="btn" href="checkout.php">Checkout</a>
  </div>
</div>

</form>

<?php endif; ?>
</div>

</body>
</html>
