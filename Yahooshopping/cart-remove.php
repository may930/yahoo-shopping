<?php
session_start();

$option_id = isset($_GET['option_id']) ? intval($_GET['option_id']) : 0;

if ($option_id > 0 && isset($_SESSION['cart'][$option_id])) {
    unset($_SESSION['cart'][$option_id]);
}

header('Location: cart.php');
exit;