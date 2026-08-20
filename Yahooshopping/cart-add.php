<?php
session_start();

$option_id = isset($_POST['option_id']) ? intval($_POST['option_id']) : 0;
$quantity  = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

if ($option_id > 0) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    // 既にカートにある場合は数量を加算、なければ新規追加
    if (isset($_SESSION['cart'][$option_id])) {
        $_SESSION['cart'][$option_id] += $quantity;
    } else {
        $_SESSION['cart'][$option_id] = $quantity;
    }
}

header('Location: cart.php');
exit;