<?php
session_start();

$option_id = isset($_POST['option_id']) ? intval($_POST['option_id']) : 0;
$action    = isset($_POST['action']) ? $_POST['action'] : '';

if ($option_id > 0 && isset($_SESSION['cart'][$option_id])) {
    if ($action === 'plus') {
        if ($_SESSION['cart'][$option_id] < 99) { // 上限99個
            $_SESSION['cart'][$option_id]++;
        }
    } elseif ($action === 'minus') {
        if ($_SESSION['cart'][$option_id] > 1) { // 下限1個
            $_SESSION['cart'][$option_id]--;
        } else {
            // 1個の状態でさらにマイナスされたらカートから削除する場合はここで unset も可能
        }
    }
}

header('Location: cart.php');
exit;