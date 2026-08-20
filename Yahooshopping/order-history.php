<?php
session_start();

// ログインしている購入者かどうかをチェック（ログイン画面の仕様に合わせて適宜変更してね）
if (!isset($_SESSION['user'])) {
    header('Location: login_view.php');
    exit();
}

require_once('utilConnDB.php');

$user_id = $_SESSION['user']['user_id'] ?? 1; // セッションからユーザーIDを取得

$utilConnDB = new UtilConnDB();
$pdo = $utilConnDB->connect();

// ログイン中のユーザーの注文履歴と詳細をデータベースから取得するSQL
// order_history と order_details を結合して取得します
$sql = 'SELECT h.*, d.product_name, d.quantity, d.price, d.option_name 
        FROM order_history h
        JOIN order_details d ON h.order_id = d.order_id
        WHERE h.user_id = ?
        ORDER BY h.order_date DESC;';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(1, $user_id, PDO::PARAM_INT);
$stmt->execute();
$orderList = $stmt->fetchAll(PDO::FETCH_ASSOC);

$utilConnDB->disconnect($pdo);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注文履歴 - Yahoo!ショッピング風</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="header">
        <div class="header-top">
            <div class="container header-top-inner">
                <span class="header-notice">送料無料をお届け！お得なキャンペーン実施中</span>
                <nav class="header-top-nav">
                    <span class="welcome-text">ようこそ、<strong><?php echo htmlspecialchars($_SESSION['user']['name'] ?? 'サンプル', ENT_QUOTES, 'UTF-8'); ?></strong> さん</span>
                </nav>
            </div>
        </div>

        <div class="header-main">
            <div class="container header-main-inner">
                <a href="index.php" class="logo">
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
                    <a href="cart.php" class="action-item-btn">
                        <span class="action-icon">🛒</span>
                        <span class="action-label">カート</span>
                    </a>
                    <a href="favorites.php" class="action-item-btn">
                        <span class="action-icon">❤</span>
                        <span class="action-label">お気に入り</span>
                    </a>
                    <a href="my_history.php" class="action-item-btn">
                        <span class="action-icon">⏱️</span>
                        <span class="action-label">注文履歴</span>
                    </a>
                    <a href="mypage.php" class="action-item-btn">
                        <span class="action-icon">👤</span>
                        <span class="action-label">マイページ</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="container" style="margin-top: 30px; margin-bottom: 60px; max-width: 900px;">

        <h1 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 20px;">注文履歴</h1>

        <div class="filter-panel" style="background: #fff; padding: 20px; border: 1px solid #e4e7ec; border-radius: 8px; margin-bottom: 24px; display: flex; flex-wrap: wrap; gap: 20px; align-items: center; justify-content: space-between; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 280px;">
                <label for="keywordInput" style="font-size: 0.9rem; font-weight: 700; color: #333; white-space: nowrap;">🔍 履歴から探す：</label>
                <input type="text" id="keywordInput" placeholder="商品名や注文番号などを入力" style="width: 100%; padding: 8px 12px; font-size: 0.9rem; border: 1px solid #ccc; border-radius: 4px; outline: none;">
            </div>
        </div>

        <?php if (empty($orderList)): ?>
            <div style="text-align: center; padding: 40px; background: #fff; border: 1px solid #e4e7ec; border-radius: 8px; color: #666;">
                <p style="font-size: 1.2rem; margin: 0 0 8px 0; font-weight: bold;">注文履歴がありません</p>
                <p style="font-size: 0.9rem; margin: 0;">まだお買い物の履歴がありません。</p>
            </div>
        <?php else: ?>
            <?php foreach ($orderList as $order): ?>
                <?php 
                    // 注文日から年を取得（フィルタリング用）
                    $orderYear = date('Y', strtotime($order['order_date']));
                ?>
                <div class="order-card" data-year="<?php echo $orderYear; ?>" style="background: #fff; border: 1px solid #e4e7ec; border-radius: 8px; margin-bottom: 24px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <div style="background: #f7f9fa; padding: 16px; border-bottom: 1px solid #e4e7ec; display: flex; flex-wrap: wrap; justify-content: space-between; gap: 16px; font-size: 0.85rem; color: #666;">
                        <div style="display: flex; gap: 24px; flex-wrap: wrap;">
                            <div>
                                <span style="display: block; margin-bottom: 4px;">注文日</span>
                                <strong style="color: #000;"><?php echo htmlspecialchars($order['order_date'], ENT_QUOTES, 'UTF-8'); ?></strong>
                            </div>
                            <div>
                                <span style="display: block; margin-bottom: 4px;">合計</span>
                                <strong style="color: #000; font-size: 1rem;">￥<?php echo number_format($order['total_price']); ?></strong>
                            </div>
                            <div>
                                <span style="display: block; margin-bottom: 4px;">お届け先</span>
                                <span style="color: #000;"><?php echo htmlspecialchars($order['shipping_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <span style="display: block; margin-bottom: 4px;">注文ID: <span class="order-number"><?php echo htmlspecialchars($order['order_id'], ENT_QUOTES, 'UTF-8'); ?></span></span>
                        </div>
                    </div>

                    <div style="padding: 20px; display: flex; flex-direction: column; gap: 20px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <!-- 配送ステータスの表示切り替え -->
                                <?php if ($order['shipping_status'] === 'shipped'): ?>
                                    <span style="color: #007bff; font-weight: 700; font-size: 1.1rem;">🚚 発送済み</span>
                                <?php elseif ($order['shipping_status'] === 'delicvered'): ?>
                                    <span style="color: #28a745; font-weight: 700; font-size: 1.1rem;">✅ お届け済み</span>
                                <?php else: ?>
                                    <span style="color: #ffc107; font-weight: 700; font-size: 1.1rem;">🟡 注文済み・出荷準備中</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div style="display: flex; gap: 16px; align-items: flex-start;">
                            <div style="width: 90px; height: 90px; background: #f0f2f5; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: #aaa; flex-shrink: 0;">
                                商品画像
                            </div>
                            <div style="flex-grow: 1;">
                                <a href="#" class="js-product-title" style="color: #000; font-weight: 500; text-decoration: none; font-size: 0.95rem; display: block; margin-bottom: 4px; line-height: 1.4;">
                                    <?php echo htmlspecialchars($order['product_name'], ENT_QUOTES, 'UTF-8'); ?>
                                    <?php if (!empty($order['option_name'])): ?>
                                        <br><small style="color: #666;">(<?php echo htmlspecialchars($order['option_name'], ENT_QUOTES, 'UTF-8'); ?>)</small>
                                    <?php endif; ?>
                                </a>
                                <p style="font-size: 0.85rem; color: #666;">数量: <?php echo htmlspecialchars($order['quantity'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <p style="font-size: 0.9rem; font-weight: 700; color: #000; margin-top: 4px;">￥<?php echo number_format($order['price']); ?></p>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px; width: 160px; flex-shrink: 0;">
                                <button class="btn-sub-action" style="padding: 8px; font-size: 0.85rem; width: 100%; cursor: pointer;">商品のレビューを書く</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div id="noOrderMessage" style="display: none; text-align: center; padding: 40px; background: #fff; border: 1px solid #e4e7ec; border-radius: 8px; color: #666;">
            <p style="font-size: 1.2rem; margin: 0 0 8px 0; font-weight: bold;">該当する注文履歴がありません</p>
            <p style="font-size: 0.9rem; margin: 0;">条件に合うお買い物履歴はありませんでした。</p>
        </div>

    </main>

    <footer class="footer">
        <div class="footer-bottom">
            <p>© 2026 Yahoo!ショッピング風チーム制作プロジェクト. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const keywordInput = document.getElementById('keywordInput');
            const orderCards = document.querySelectorAll('.order-card');
            const noOrderMessage = document.getElementById('noOrderMessage');

            function filterOrders() {
                const keyword = keywordInput.value.toLowerCase().trim();
                let visibleCount = 0;

                orderCards.forEach(card => {
                    const productTitle = card.querySelector('.js-product-title').textContent.toLowerCase();
                    const orderNumber = card.querySelector('.order-number').textContent;

                    const keywordMatches = (keyword === '' || productTitle.includes(keyword) || orderNumber.includes(keyword));

                    if (keywordMatches) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (visibleCount === 0 && orderCards.length > 0) {
                    noOrderMessage.style.display = 'block';
                } else {
                    noOrderMessage.style.display = 'none';
                }
            }

            if (keywordInput) {
                keywordInput.addEventListener('input', filterOrders);
            }
        });
    </script>

</body>
</html>