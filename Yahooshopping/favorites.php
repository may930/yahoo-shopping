<?php 
/* インポート */
require_once('Beans.php');

/* データを受け取る */
session_start();
$favoriteList = array();
if (isset($_SESSION['favoriteList'])) {
 $favoriteList = $_SESSION['favoriteList'];
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お気に入り商品 - Yahoo!ショッピング風</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="main-bg-gray">

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
                    <a href="favorites_Lstmain.php" class="action-item-btn">
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

    
    <!-- メインコンテンツ -->
    <main class="container" style="margin-top: 30px; margin-bottom: 60px;">
        <!-- ページタイトルとお気に入り件数＆並び替え -->
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px; border-bottom: 2px solid var(--color-primary); padding-bottom: 8px; flex-wrap: wrap; gap: 10px;">
            <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--color-black); margin: 0;">
                ❤️ お気に入り商品
            </h1>

            <!-- 右側のコントロールエリア（並び替え ＆ 件数） -->
            <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                <!-- 並び替えプルダウンを追加したばい！ -->
                <div style="display: flex; align-items: center; gap: 6px;">
                    <label for="sort-favorites" style="font-size: 0.85rem; color: #555; font-weight: bold;">並び替え：</label>
                    <select id="sort-favorites" onchange="sortFavorites()" style="padding: 6px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem; background-color: #fff; cursor: pointer; outline: none; font-family: inherit;">
                        <option value="newest">追加した日時の新しい順</option>
                        <option value="oldest">追加した日時の古い順</option>
                    </select>
                </div>

                <!-- お気に入り件数の表示（JSで動的変更できるように id="fav-count" を付与） -->
                <span style="font-size: 0.9rem; color: #666; font-weight: 500;">
                    現在のお気に入り：<strong id="fav-count" style="color: var(--color-primary); font-size: 1.1rem;">3</strong>
                </span>
            </div>
        </div>


        <!-- 商品カードを並べるコンテナ（CSSグリッドやFlexboxで横並びにする） -->
        <div class="product-grid">

            <?php foreach ($favoriteList as $Beans): ?>
                <?php 
                    // データベースから取得した安全な値を変数にセット
                    $productId   = $Beans->getproduct_favorites_id();
                    $productName = htmlspecialchars($Beans->getproduct_name(), ENT_QUOTES, 'UTF-8');
                    $price       = number_format($Beans->getprice());
                    $image       = htmlspecialchars($Beans->getimage_url(), ENT_QUOTES, 'UTF-8');
                    $add_at       = htmlspecialchars($Beans->getadd_at(), ENT_QUOTES, 'UTF-8');
                ?>

                <!-- ★ 1個分のカードテンプレート（これがループで自動増殖します） -->
                <div class="product-card" id="<?php echo $productId; ?>" data-added-at="<?php echo $add_at; ?>">
                    
                    <!-- 商品画像 -->
                    <img src="<?php echo $image; ?>" alt="<?php echo $productName; ?>" class="product-img">
                    
                    <!-- 商品情報 -->
                    <div class="product-info">
                        <h3 class="product-name"><?php echo $productName; ?></h3>
                        
                        <!-- 評価（スター）-->
                         <!-- 後から修正（レビューする機能が出来たら） -->
                        <div class="product-rating">
                            <span class="stars">★★★★☆</span>
                            <span class="rating-count">1,590</span>
                        </div>

                        <!-- 価格 -->
                        <div class="product-price">
                            ¥<?php echo $price; ?>
                        </div>

                        <!-- カートに入れるボタン -->
                        <button class="btn-cart" onclick="addToCart('<?php echo $productId; ?>')">
                            カートに追加する
                        </button>

                        <!--商品を削除するボタン-->
                        <button type="button" class="btn-delete" onclick="removeFavorite(<?php echo $productId; ?>)">🗑️</button>                    </div>

                </div>
            <?php endforeach; ?>

        </div>


        <!-- お買い物を続けるボタン -->
        <div style="text-align: center; margin-top: 40px;">
            <a href="index.html" class="btn-sub-action" style="text-decoration: none; padding: 12px 30px; font-weight: 500; display: inline-block;">
                トップページに戻ってお買い物を続ける
            </a>
        </div>

    </main>

    <!-- フッター -->
    <footer class="footer">
        <div class="footer-bottom">
            <p>© 2026 Yahoo!ショッピング風チーム制作プロジェクト. All rights reserved.</p>
        </div>
    </footer>

    <!-- スクリプト処理 -->
    <script>
        // ① お気に入り商品の並び替えロジック
        function sortFavorites() {
            const sortVal = document.getElementById('sort-favorites').value;
            const grid = document.querySelector('.product-grid');
            // お気に入り商品を配列として取得
            const items = Array.from(grid.querySelectorAll('.product-card'));

            // 各商品の `data-added-at`（追加日時）を元に比較してソート
            items.sort((a, b) => {
                const dateA = new Date(a.getAttribute('data-added-at'));
                const dateB = new Date(b.getAttribute('data-added-at'));

                if (sortVal === 'newest') {
                    return dateB - dateA; // 新しい順（降順）
                } else {
                    return dateA - dateB; // 古い順（昇順）
                }
            });

            // ソートされた順にDOMを再配置
            items.forEach(item => grid.appendChild(item));
        }

        // ページ読み込み時に初期ソートを実行（初期値：新しい順）
        window.addEventListener('DOMContentLoaded', () => {
            sortFavorites();
        });

        // ② お気に入り削除（件数更新機能を追加！）
        function removeFavorite(itemId) {
            if(confirm('この商品をお気に入りから削除してもよろしいですか？')) {
                const item = document.getElementById(itemId);
                
                const sendDeleteForm = () => {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'favorites_deletemain.php';

                    const inputPro = document.createElement('input');
                    inputPro.type = 'hidden';
                    inputPro.name = 'delete_item';
                    inputPro.value = itemId;

                    form.appendChild(inputPro);
                    document.body.appendChild(form);
                    form.submit();
                };

                if(item) {
                    item.style.transition = 'all 0.4s ease';
                    item.style.opacity = '0';
                    item.style.transform = 'scale(0.9)';
                    
                    setTimeout(() => {
                        item.remove();
                        updateFavoriteCount();
                        sendDeleteForm();
                    }, 400);
                } else {
                    sendDeleteForm();
                }                
            }
        }
        // 件数表示をリアルタイムで同期して更新する関数
        function updateFavoriteCount() {
            const countEl = document.getElementById('fav-count');
            const remainingItems = document.querySelectorAll('.product-grid .product-card');
            if (countEl) {
                countEl.textContent = remainingItems.length;
            }
        }
    </script>

</body>
</html>