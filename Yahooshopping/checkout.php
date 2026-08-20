<?php
session_start();

// ログインしていなければログイン画面へ
if (!isset($_SESSION['user'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header('Location: login_view.php');
    exit();
}

// ★ここから追加：カートの中身をDBから取得
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

// 合計金額・合計点数を計算
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
    <title>注文内容の確認 - Yahoo!ショッピング風</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="checkout-body">

    <header class="header">
        <div class="header-top">
            <div class="container header-top-inner">
                <span class="header-notice">送料無料をお届け！お得なキャンペーン実施中</span>
                <nav class="header-top-nav">
                    <span class="welcome-text">ようこそ、<strong>サンプル</strong> さん</span>
                </nav>
            </div>
        </div>

        <div class="header-main">
            <div class="container header-main-inner">
                <a href="index.html" class="logo">
                    <span class="logo-y">HCS!</span><span class="logo-s">ショッピング</span>
                </a>
                <div class="search-bar">
                    <input type="text" placeholder="何をお探しですか？ 商品名、カテゴリ、ブランドから探す" aria-label="商品検索">
                    <button type="submit" class="search-btn" aria-label="検索">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.35-4.35" />
                        </svg>
                        <span>検索する</span>
                    </button>
                </div>
                <div class="header-actions">
                    <a href="cart.html" class="action-item-btn">
                        <span class="action-icon">🛒</span>
                        <span class="action-label">カート</span>
                        <span class="cart-count">3</span>
                    </a>
                    <a href="favorites.html" class="action-item-btn">
                        <span class="action-icon">❤</span>
                        <span class="action-label">お気に入り</span>
                        <span class="cart-count">3</span>
                    </a>
                    <a href="browsing-history.html" class="action-item-btn">
                        <span class="action-icon">🕒</span>
                        <span class="action-label">閲覧履歴</span>
                    </a>
                    <a href="order-history.html" class="action-item-btn">
                        <span class="action-icon">⏱️</span>
                        <span class="action-label">注文履歴</span>
                    </a>
                    <a href="mypage.html" class="action-item-btn">
                        <span class="action-icon">👤</span>
                        <span class="action-label">マイページ</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="checkout-container">
        <form action="order-complete.html" method="GET" class="checkout-layout">

            <div class="checkout-main-side">

                <section class="checkout-section-card">
                    <h2 class="checkout-sec-title">1. お届け先住所</h2>
                    <div class="checkout-form-group">
                        <label class="checkout-label">お名前</label>
                        <input type="text" class="form-input" placeholder="HCS 太郎" required>
                    </div>
                    <div class="checkout-form-group" style="margin-top: 16px;">
                        <label class="checkout-label">ご住所</label>
                        <input type="text" id="shippingAddress" class="form-input" placeholder="北海道札幌市中央区北1条西..." required>
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
                    <h4 class="checkout-product-name"><?= htmlspecialchars($item['product_name']) ?></h4>
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

            if (checkbox.checked) {
                billingBox.style.display = 'none';
            } else {
                billingBox.style.display = 'block';
            }
        }

        // ギフト設定の表示・非表示を切り替える関数ばい！
        function toggleGiftOptions() {
            const checkbox = document.getElementById('isGift');
            const giftBox = document.getElementById('giftOptionsBox');

            if (checkbox.checked) {
                giftBox.style.display = 'block';
            } else {
                giftBox.style.display = 'none';
            }
        }
    </script>
</body>
</html>