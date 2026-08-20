<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login_view.php');
    exit();
}

// checkout.phpからのPOSTが無ければ、直接アクセスされたとみなしcheckoutへ戻す
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit();
}

$addressType   = $_POST['addressType'] ?? 'new';
$paymentMethod = $_POST['paymentMethod'] ?? '';

if ($addressType === 'registered') {
    // 登録済み住所を使う場合は、セッションの値を信頼して使う（改ざん防止のため）
    $shipping_name = $_SESSION['user']['real_name'] ?? $_SESSION['user']['name'] ?? '';
    $zipcode       = $_SESSION['user']['zipcode'] ?? '';
    $address       = $_SESSION['user']['address'] ?? '';
} else {
    $shipping_name = trim($_POST['shipping_name'] ?? '');
    $zipcode       = trim($_POST['zipcode'] ?? '');
    $address       = trim($_POST['address'] ?? '');
}


if (empty($shipping_name) || empty($zipcode) || empty($address) || empty($paymentMethod)) {
    header('Location: checkout.php');
    exit();
}

// 支払方法の表示名変換
$payment_labels = [
    'credit' => 'クレジットカード',
    'paypay' => 'PayPay',
    'cod'    => '代金引換',
];
$payment_label = $payment_labels[$paymentMethod] ?? $paymentMethod;

// カートの中身をDBから再取得（価格はここで再計算、フォームの値は信用しない）
require_once('cartSQL.php');
require_once('utilConnDB.php');

$cartSQL    = new CartSQL();
$utilConnDB = new UtilConnDB();
$pdo        = $utilConnDB->connect();

$cart_session = $_SESSION['cart'] ?? [];
$option_ids   = array_keys($cart_session);

$cart_items = [];
if (!empty($option_ids)) {
    $cart_items = $cartSQL->selectCartItems($pdo, $option_ids);
}
$utilConnDB->disconnect($pdo);

if (empty($cart_items)) {
    header('Location: cart.php');
    exit();
}

$total_price = 0;
$total_count = 0;
foreach ($cart_items as $item) {
    $quantity = $cart_session[$item['option_id']] ?? 1;
    $total_price += $item['price'] * $quantity;
    $total_count += $quantity;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注文内容の最終確認 - Yahoo!ショッピング風</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="checkout-body">

    <?php require_once('header.php'); ?>

    <main class="checkout-container">
        <form action="order-process.php" method="POST" class="checkout-layout">

            <!-- 確認画面なので、直前の入力値をhiddenで引き継ぐ -->
            <input type="hidden" name="shipping_name" value="<?= htmlspecialchars($shipping_name) ?>">
            <input type="hidden" name="zipcode" value="<?= htmlspecialchars($zipcode) ?>">
            <input type="hidden" name="address" value="<?= htmlspecialchars($address) ?>">
            <input type="hidden" name="paymentMethod" value="<?= htmlspecialchars($paymentMethod) ?>">

            <div class="checkout-main-side">

                <section class="checkout-section-card">
                    <h2 class="checkout-sec-title">お届け先住所</h2>
                    <p>お名前：<?= htmlspecialchars($shipping_name) ?></p>
                    <p>郵便番号：<?= htmlspecialchars($zipcode) ?></p>
                    <p>ご住所：<?= htmlspecialchars($address) ?></p>
                </section>

                <section class="checkout-section-card">
                    <h2 class="checkout-sec-title">お支払方法</h2>
                    <p><?= htmlspecialchars($payment_label) ?></p>
                </section>

                <section class="checkout-section-card">
                    <h2 class="checkout-sec-title">注文商品</h2>
                    <div class="checkout-item-list">
                        <?php foreach ($cart_items as $item): ?>
                            <?php
                                $quantity = $cart_session[$item['option_id']] ?? 1;
                                $img_url = !empty($item['image_url']) ? preg_replace('/^localhost\/(Yahooshopping\/)?/i', '', $item['image_url']) : 'https://placehold.co/200x200/f8f9fa/ff5a00?text=NoImage';
                            ?>
                            <div class="checkout-product-item">
                                <div class="checkout-product-img-box">
                                    <img src="<?= htmlspecialchars($img_url) ?>" alt="商品画像">
                                </div>
                                <div class="checkout-product-info">
                                    <h4 class="checkout-product-name"><?= htmlspecialchars($item['product_name']) ?>（<?= htmlspecialchars($item['option_name']) ?>）</h4>
                                    <p class="checkout-product-meta">数量: <?= $quantity ?> | ￥<?= number_format($item['price']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

            </div>

            <div class="checkout-side-bar">
                <div class="summary-sticky-card">
                    <h3 class="summary-box-title">ご注文内容（<?= $total_count ?>点）</h3>
                    <div class="summary-price-row">
                        <span>商品合計</span>
                        <span>￥<?= number_format($total_price) ?></span>
                    </div>
                    <div class="summary-price-row">
                        <span>送料</span>
                        <span style="color: #1a6e3a; font-weight: 700;">無料</span>
                    </div>
                    <div class="summary-divider"></div>
                    <div class="summary-total-row">
                        <span>ご請求金額</span>
                        <span class="final-total-price">￥<?= number_format($total_price) ?></span>
                    </div>

                    <button type="submit" class="checkout-submit-btn">購入</button>
                    <a href="checkout.php" style="display:block; text-align:center; margin-top:10px; color:#666;">入力内容を修正する</a>
                </div>
            </div>

        </form>
    </main>

    <footer class="footer">
        <div class="footer-bottom">
            <p>© 2026 Yahoo!ショッピング風チーム制作プロジェクト. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>