<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require "config/db.php";

/* 初始化购物车 */
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_GET['action'] ?? '';

/* 获取商品 */
function getProduct($conn, $id) {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, name, price, image FROM menu WHERE id=? AND available=1 LIMIT 1"
    );
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res);
}

/* ======================
   ADD TO CART
====================== */
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $pid = (int)($_POST['product_id'] ?? 0);
    $qty = max(1, (int)($_POST['qty'] ?? 1));

    if ($pid > 0) {
        $product = getProduct($conn, $pid);

        if ($product) {
            if (isset($_SESSION['cart'][$pid])) {
                $_SESSION['cart'][$pid]['qty'] += $qty;
            } else {
                $_SESSION['cart'][$pid] = [
                    'id'    => $product['id'],
                    'name'  => $product['name'],
                    'price' => (float)$product['price'],
                    'image' => $product['image'],
                    'qty'   => $qty
                ];
            }
        }
    }

    header("Location: cart.php");
    exit;
}

/* ======================
   UPDATE CART
====================== */
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['qty'] as $id => $qty) {
        $id  = (int)$id;
        $qty = (int)$qty;

        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } elseif (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] = $qty;
        }
    }
    header("Location: cart.php");
    exit;
}

/* ======================
   REMOVE ITEM
====================== */
if ($action === 'remove') {
    $id = (int)($_GET['id'] ?? 0);
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit;
}
