<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login_view.php');
    exit();
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ご注文完了 - Yahoo!ショッピング風</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="checkout-body">

    <?php require_once('header.php'); ?>

    <main class="checkout-container" style="text-align:center; padding: 60px 20px;">
        <h2>ご注文ありがとうございました！</h2>
        <?php if ($order_id > 0): ?>
            <p>ご注文番号：<strong>#<?= $order_id ?></strong></p>
        <?php endif; ?>
        <p style="margin-top: 20px;">
            <a href="order-history.php" style="margin-right: 20px;">注文履歴を確認する</a>
            <a href="index.php">お買い物を続ける</a>
        </p>
    </main>

</body>
</html>