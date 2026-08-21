<?php
session_start();

// ログインしていなければログイン画面へ（戻り先を保存）
if (!isset($_SESSION['user'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header('Location: login_view.php');
    exit();
}

// カートの中身をDBから取得（表示・金額計算用）
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

// カートが空なら購入手続きに進めないのでカート画面に戻す
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

// 登録済み住所があるかどうか
$hasRegisteredAddress = !empty($_SESSION['user']['address']);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注文内容の確認 - Yahoo!ショッピング風</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="checkout-body">

    <?php require_once('header.php'); ?>

    <main class="checkout-container">
        <form action="confirm.php" method="POST" class="checkout-layout">

            <div class="checkout-main-side">

                <section class="checkout-section-card">
                    <h2 class="checkout-sec-title">1. お届け先住所</h2>

                    <?php if ($hasRegisteredAddress): ?>
                        <div class="checkout-form-group">
                            <label style="display:block; margin-bottom: 8px;">
                                <input type="radio" name="addressType" value="registered" checked onchange="toggleAddressInputs()">
                                登録済みの住所を使う
                            </label>
                            <p style="margin: 0 0 4px 24px; color: #555;">
                                <?= htmlspecialchars($_SESSION['user']['real_name'] ?? $_SESSION['user']['name']) ?> 様<br>
                                〒<?= htmlspecialchars($_SESSION['user']['zipcode']) ?><br>
                                <?= htmlspecialchars($_SESSION['user']['address']) ?>
                            </p>
                            <label style="display:block; margin-top: 12px;">
                                <input type="radio" name="addressType" value="new" onchange="toggleAddressInputs()">
                                新しい住所を入力する
                            </label>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="addressType" value="new">
                    <?php endif; ?>

                    <div id="newAddressInputs" style="<?= $hasRegisteredAddress ? 'display:none;' : '' ?> margin-top: 16px;">
                        <div class="checkout-form-group">
                            <label class="checkout-label">お名前</label>
                            <input type="text" name="shipping_name" class="form-input" placeholder="HCS 太郎" <?= $hasRegisteredAddress ? '' : 'required' ?>>
                        </div>
                        <div class="checkout-form-group" style="margin-top: 16px;">
                            <label class="checkout-label">郵便番号</label>
                            <input type="text" name="zipcode" class="form-input" placeholder="062-0031" <?= $hasRegisteredAddress ? '' : 'required' ?>>
                        </div>
                        <div class="checkout-form-group" style="margin-top: 16px;">
                            <label class="checkout-label">ご住所</label>
                            <input type="text" id="shippingAddress" name="address" class="form-input" placeholder="北海道札幌市中央区北1条西..." <?= $hasRegisteredAddress ? '' : 'required' ?>>
                        </div>
                    </div>
                </section>

                <section class="checkout-section-card">
                    <div class="billing-header-flex">
                        <h2 class="checkout-sec-title">2. 請求先住所</h2>
                        <label class="same-address-checkbox">
                            <input type="checkbox" id="sameAsShipping" checked onchange="toggleBillingAddress()"> お届け先と同じ
                        </label>
                    </div>

                    <div id="billingAddressBox" class="billing-address-input-box" style="display: none; margin-top: 16px;">
                        <div class="checkout-form-group">
                            <label class="checkout-label">請求先お名前</label>
                            <input type="text" class="form-input" placeholder="請求先のお名前">
                        </div>
                        <div class="checkout-form-group" style="margin-top: 16px;">
                            <label class="checkout-label">請求先ご住所</label>
                            <input type="text" class="form-input" placeholder="請求先のご住所">
                        </div>
                    </div>
                </section>

                <section class="checkout-section-card">
                    <h2 class="checkout-sec-title">3. お支払方法</h2>
                    <div class="payment-selector-group">
                        <label class="payment-radio-label">
                            <input type="radio" name="paymentMethod" value="credit" checked>
                            <span class="payment-method-name">クレジットカード決済</span>
                        </label>
                        <label class="payment-radio-label">
                            <input type="radio" name="paymentMethod" value="paypay">
                            <span class="payment-method-name">PayPay（残高払い）</span>
                        </label>
                        <label class="payment-radio-label">
                            <input type="radio" name="paymentMethod" value="cod">
                            <span class="payment-method-name">代金引換（代引き）</span>
                        </label>
                    </div>
                </section>

                <section class="checkout-section-card">
                    <h2 class="checkout-sec-title">4. ギフト設定（任意）</h2>
                    <div class="gift-selector-group">
                        <label class="gift-checkbox-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 500;">
                            <input type="checkbox" id="isGift" name="isGift" onchange="toggleGiftOptions()"> ギフトラッピングを希望する
                        </label>

                        <div id="giftOptionsBox" style="display: none; margin-top: 16px; border-top: 1px dashed #ddd; padding-top: 16px;">
                            <div class="checkout-form-group">
                                <label class="checkout-label">ラッピングの種類</label>
                                <select class="form-input" name="giftWrapping" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-family: inherit;">
                                    <option value="standard">通常ラッピング（無料）</option>
                                    <option value="premium">プレミアムギフトバッグ（+￥300）</option>
                                    <option value="birthday">お誕生日専用ラッピング（無料）</option>
                                </select>
                            </div>
                            <div class="checkout-form-group" style="margin-top: 16px;">
                                <label class="checkout-label">メッセージカード（任意のメッセージを入力してください）</label>
                                <textarea class="form-input" name="giftMessage" rows="3" placeholder="のし・メッセージカードの内容など（例：お誕生日おめでとう！）" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-family: inherit; resize: vertical;"></textarea>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="checkout-section-card">
                    <h2 class="checkout-sec-title">5. 注文商品</h2>
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
                    <h3 class="summary-box-title">注文内容の確認（<?= $total_count ?>点）</h3>
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

                    <button type="submit" class="checkout-submit-btn">注文内容の確認へ</button>
                </div>
            </div>

        </form>
    </main>

    <footer class="footer">
        <div class="footer-bottom">
            <p>© 2026 Yahoo!ショッピング風チーム制作プロジェクト. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function toggleBillingAddress() {
            const checkbox = document.getElementById('sameAsShipping');
            const billingBox = document.getElementById('billingAddressBox');
            billingBox.style.display = checkbox.checked ? 'none' : 'block';
        }

        function toggleGiftOptions() {
            const checkbox = document.getElementById('isGift');
            const giftBox = document.getElementById('giftOptionsBox');
            giftBox.style.display = checkbox.checked ? 'block' : 'none';
        }

        function toggleAddressInputs() {
            const checkedEl = document.querySelector('input[name="addressType"]:checked');
            const isNew = checkedEl ? checkedEl.value === 'new' : true;
            document.getElementById('newAddressInputs').style.display = isNew ? 'block' : 'none';
        }
    </script>
</body>
</html>