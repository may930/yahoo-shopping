<?php
// セッション開始
session_start();

// 必要なファイルをインポート
require_once('cartSQL.php');
require_once('utilConnDB.php'); 
require_once('Beans.php');

// インスタンス生成
$cartSQL    = new CartSQL();
$utilConnDB = new UtilConnDB();
$pdo        = $utilConnDB->connect();

// セッションからカートのデータ（option_id => 数量）を取得
// 例: $_SESSION['cart'] = [ 1 => 2,  3 => 1 ] （option_id => 数量）
$cart_session = $_SESSION['cart'] ?? [];
$option_ids = array_keys($cart_session);

// データベースからカートに入っている商品の詳細情報を取得
$cart_items = [];
if (!empty($option_ids)) {
    $cart_items = $cartSQL->selectCartItems($pdo, $option_ids);
}

// DB切断
$utilConnDB->disconnect($pdo);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ショッピングカート - Yahoo!ショッピング風</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php require_once('header.php'); ?>

    <main class="cart-container">
        <div class="cart-layout">
            <div class="cart-main-side">
                <div class="cart-store-group">
                    <div class="store-header">
                        <span class="store-icon">🏪</span>
                        <h3 class="store-title-text">あすつく対応公式ストア</h3>
                    </div>

                    <?php if (empty($cart_items)): ?>
                        <div style="padding: 40px; text-align: center; color: #666;">
                            <p style="font-size: 1.1rem; margin-bottom: 20px;">カートに商品は入っていません。</p>
                            <a href="index.php" style="padding: 10px 20px; background: var(--color-primary); color: #fff; text-decoration: none; border-radius: 4px;">お買い物を続ける</a>
                        </div>
                    <?php else: ?>
                        <?php 
                        $total_price = 0;
                        $total_count = 0;
                        foreach ($cart_items as $item):
                            $option_id = $item['option_id'];
                            $quantity = $cart_session[$option_id] ?? 1;
                            $subtotal = $item['price'] * $quantity;
                            $total_price += $subtotal;
                            $total_count += $quantity;
                            
                            // 画像パスの調整
                            $img_url = !empty($item['image_url']) ? preg_replace('/^localhost\/(Yahooshopping\/)?/i', '', $item['image_url']) : 'https://placehold.co/200x200/f8f9fa/ff5a00?text=NoImage';
                        ?>
                            <div class="cart-item" data-option-id="<?= $option_id ?>">
                                <div class="cart-item-checkbox">
                                    <input type="checkbox" checked>
                                </div>
                                <div class="cart-item-img-box">
                                    <img src="<?= htmlspecialchars($img_url) ?>" alt="商品画像" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                </div>
                                <div class="cart-item-info">
                                    <h4 class="cart-item-name"><?= htmlspecialchars($item['product_name']) ?></h4>
                                    <p class="cart-item-meta">仕様：<?= htmlspecialchars($item['option_name']) ?></p>
                                    <div class="cart-item-actions">
                                        <a href="cart-remove.php?option_id=<?= $option_id ?>" class="cart-item-delete" style="text-decoration: none; color: #d9534f; display: inline-flex; align-items: center; gap: 4px;">
                                            <span class="delete-icon">🗑️</span>削除
                                        </a>
                                    </div>
                                </div>
                                <div class="cart-item-qty">
                                    <form method="post" action="cart-update.php" class="qty-controller" style="display: flex; align-items: center; gap: 8px;">
                                        <input type="hidden" name="option_id" value="<?= $option_id ?>">
                                        <button type="submit" name="action" value="minus" class="qty-btn">−</button>
                                        <span class="cart-qty-val" data-price="<?= $item['price'] ?>"><?= $quantity ?></span>
                                        <button type="submit" name="action" value="plus" class="qty-btn">＋</button>
                                    </form>
                                </div>
                                <div class="cart-item-price">
                                    <span class="price-currency">￥</span><span class="price-val"><?= number_format($item['price']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>
            </div>

            <div class="cart-side-bar">
                <div class="summary-card">
                    <h3 class="summary-title">ご請求金額 (<?= $total_count ?? 0 ?>点)</h3>
                    <div class="summary-row">
                        <span class="summary-label">商品合計</span>
                        <span class="summary-value" id="subtotal-price">￥<?= number_format($total_price ?? 0) ?></span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">送料</span>
                        <span class="summary-value shipping-free">無料</span>
                    </div>
                    <div class="summary-divider"></div>
                    <div class="summary-row total-row">
                        <span class="summary-label">合計金額</span>
                        <span class="summary-value final-price" id="total-price">￥<?= number_format($total_price ?? 0) ?></span>
                    </div>

                    <?php if (!empty($cart_items)): ?>
                        <a href="checkout.php" class="cart-submit-btn" style="display: block; text-align: center; text-decoration: none;">購入手続きへ進む</a>
                    <?php else: ?>
                        <button class="cart-submit-btn" disabled style="background: #ccc; cursor: not-allowed;">購入手続きへ進む</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-bottom">
            <p>© 2026 Yahoo!ショッピング風チーム制作プロジェクト. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>