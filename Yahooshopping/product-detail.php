<?php
// 1. データベース接続ファイルを読み込む
require_once 'db.php';

try {
    // 2. URLから option_id を取得する（送られてこなかった場合はデフォルトで 1）
    $target_option_id = isset($_GET['option_id']) ? intval($_GET['option_id']) : 1;

    /* 
       3. INNER JOIN を使って、
          product_attributes_options と product テーブルを結合して一気に取得するばい！
    */
    $sql = "SELECT 
                o.*, 
                p.product_name, 
                p.information 
            FROM product_attributes_options AS o
            INNER JOIN product AS p ON o.variation_id = p.product_id 
            WHERE o.option_id = :id";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $target_option_id]);
    $product_data = $stmt->fetch();

    // データが万が一取れなかったときの安全対策
    if (!$product_data) {
        $stmt_fallback = $pdo->prepare("SELECT * FROM product_attributes_options WHERE option_id = :id");
        $stmt_fallback->execute(['id' => $target_option_id]);
        $product_data = $stmt_fallback->fetch();
        
        $product_data['product_name'] = $product_data['product_name'] ?? 'ストロング酒（仮）';
        $product_data['information'] = $product_data['information'] ?? 'データベースから商品説明が取得できませんでした。';
    }

    // 4. 同じバリエーショングループの選択肢（500ml, 750ml, 1000ml など）をすべて取得
    $stmt_all = $pdo->prepare("SELECT * FROM product_attributes_options WHERE variation_id = :variation_id");
    $stmt_all->execute(['variation_id' => $product_data['variation_id']]);
    $all_options = $stmt_all->fetchAll();

    // 5. 🛠️ 【追加】このオプションに紐づく画像をすべて取得
    $stmt_images = $pdo->prepare("SELECT image_url FROM product_images WHERE option_id = :option_id ORDER BY display_order ASC");
    $stmt_images->execute(['option_id' => $target_option_id]);
    $db_images = $stmt_images->fetchAll(PDO::FETCH_COLUMN);

    // 画像パスの整形（localhost/ などを除去）
    $product_images = [];
    foreach ($db_images as $img) {
        if (!empty($img)) {
            $product_images[] = preg_replace('/^localhost\/(Yahooshopping\/)?/i', '', $img);
        }
    }

} catch (PDOException $e) {
    exit('データ取得に失敗しました: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product_data['product_name']) ?> (<?= htmlspecialchars($product_data['option_name']) ?>) - Yahoo!ショッピング風</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

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

    <main class="main-bg-gray">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php">トップ</a> ＞ <a href="#">ドリンク、水、お酒</a> ＞ <span><?= htmlspecialchars($product_data['product_name']) ?></span>
            </div>

            <div class="pd-container">
                <!-- 🛠️ 画像表示エリアの動的対応 -->
                <div class="pd-image-area">
                    <div style="width: 100%; height: 350px; display: flex; align-items: center; justify-content: center; background: #fff; border: 1px solid #e4e7ec; border-radius: 8px; overflow: hidden; padding: 10px;">
                        <?php if (!empty($product_images)): ?>
                            <img id="pdMainImg" src="<?= htmlspecialchars($product_images[0]) ?>" alt="<?= htmlspecialchars($product_data['product_name']) ?>" class="pd-main-img" style="max-width: 100%; max-height: 100%; object-fit: contain; display: block;">
                        <?php else: ?>
                            <div style="font-size: 3rem; color: #ccc;">🎁</div>
                        <?php endif; ?>
                    </div>

                    <!-- サムネイル一覧エリア -->
                    <?php if (count($product_images) > 0): ?>
                        <div class="pd-thumb-grid" style="display: flex; gap: 10px; margin-top: 10px; flex-wrap: wrap;">
                            <?php foreach ($product_images as $index => $img): ?>
                                <div class="pd-thumb <?= $index === 0 ? 'active' : '' ?>" data-img="<?= htmlspecialchars($img) ?>" style="width: 60px; height: 60px; border: 1px solid #ccc; border-radius: 4px; overflow: hidden; cursor: pointer; display: flex; align-items: center; justify-content: center; background: #fff; padding: 2px;">
                                    <img src="<?= htmlspecialchars($img) ?>" style="max-width: 100%; max-height: 100%; object-fit: contain; display: block;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="pd-info-panel">
                    <p class="pd-brand">SUNTORY</p>
                    <!-- 大元の「商品名」 ＋ 選ばれている「味やサイズ」をドッキング！ -->
                    <h1 class="pd-title">
                        <?= htmlspecialchars($product_data['product_name']) ?> 
                        (<?= htmlspecialchars($product_data['option_name']) ?>)
                    </h1>

                    <div class="pd-rating-row">
                        <span class="stars" style="color: #ffcc00;">★★★★★</span>
                        <span class="rating-avg" id="mainRatingAvg">4.7</span>
                        <a href="#reviews-section" class="rating-link">レビュー<span id="mainReviewCount">128</span>件を見る</a>
                    </div>

                    <div class="pd-price-box">
                        <!-- データベースから引っ張ってきた価格をフォーマットして表示！ -->
                        <p class="pd-price">¥<?= number_format($product_data['price']) ?> <span class="tax">税込</span></p>
                        <!-- 残り在庫数を表示するエリア -->
                        <p style="font-size: 0.85rem; color: #d9534f; font-weight: bold; margin-top: 5px;">
                            🔥 残り在庫数: <?= htmlspecialchars($product_data['stock']) ?>個
                        </p>
                        <p class="pd-points">➕ PayPayポイントが毎日5%貯まる！</p>
                    </div>

                    <div class="pd-options">
                        <!-- データベースの「オプション（容量/種類など）」を動的にボタン出力！ -->
                        <div class="option-group">
                            <label class="option-label">サイズ・仕様: <span id="selectedSize"><?= htmlspecialchars($product_data['option_name']) ?></span></label>
                            <div class="option-buttons">
                                <?php foreach ($all_options as $opt): ?>
                                    <?php 
                                    $is_out_of_stock = ($opt['stock'] <= 0); 
                                    $is_active = ($opt['option_id'] == $target_option_id) ? 'active' : '';
                                    ?>
                                    <button class="pd-size-btn <?= $is_active ?>" 
                                            <?= $is_out_of_stock ? 'disabled' : '' ?>
                                            onclick="location.href='product-detail.php?option_id=<?= $opt['option_id'] ?>'">
                                        <?= htmlspecialchars($opt['option_name']) ?>
                                        <?= $is_out_of_stock ? ' (売り切れ)' : '' ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="option-group">
                            <label class="option-label">数量</label>
                            <div class="pd-qty-selector">
                                <button id="qtyDown" class="qty-btn">-</button>
                                <span id="qtyVal" class="qty-val">1</span>
                                <button id="qtyUp" class="qty-btn">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="pd-actions">
                        <?php if ($product_data['stock'] > 0): ?>
                            <button class="pd-btn-cart" onclick="location.href='cart.html'">🛒 カートに入れる</button>
                        <?php else: ?>
                            <button class="pd-btn-cart" disabled style="background-color: #ccc; cursor: not-allowed;">❌ 売り切れです</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 商品詳細説明エリア -->
            <section style="background: #fff; border: 1px solid #e4e7ec; border-radius: var(--radius-md); padding: 30px; margin-top: 30px; box-shadow: var(--shadow-card);">
                <h2 style="font-size: 1.3rem; font-weight: 700; border-bottom: 2px solid var(--color-accent); padding-bottom: 8px; margin-bottom: 20px;">商品説明</h2>

                <div style="line-height: 1.8; color: var(--color-black); font-size: 0.95rem;">
                    <p style="margin-bottom: 16px; font-weight: 500; white-space: pre-wrap;"><?= htmlspecialchars($product_data['information']) ?></p>
                </div>
            </section>

            <!-- 口コミ（レビュー）エリア -->
            <section id="reviews-section" style="background: #fff; border: 1px solid #e4e7ec; border-radius: var(--radius-md); padding: 30px; margin-top: 30px; margin-bottom: 30px; box-shadow: var(--shadow-card);">
                <h2 style="font-size: 1.3rem; font-weight: 700; border-bottom: 2px solid var(--color-accent); padding-bottom: 8px; margin-bottom: 20px;">商品レビュー・口コミ</h2>

                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px; background: #f7f9fa; padding: 20px; border-radius: var(--radius-sm); margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; gap: 24px;">
                        <div style="text-align: center; border-right: 1px solid #e4e7ec; padding-right: 24px;">
                            <span style="font-size: 2.5rem; font-weight: 700; color: var(--color-black); line-height: 1;" id="summaryRatingAvg">4.7</span>
                            <span style="display: block; font-size: 0.8rem; color: #666; margin-top: 4px;">総合評価（<span id="summaryReviewCount">128</span>件）</span>
                        </div>
                        <div>
                            <div class="stars" style="font-size: 1.2rem; margin-bottom: 4px; color: #ffcc00;">★★★★★</div>
                            <p style="font-size: 0.85rem; color: #555; margin: 0;">購入したユーザーの多くが高い評価を寄せています。</p>
                        </div>
                    </div>

                    <button id="toggleFormBtn" style="padding: 10px 20px; background-color: var(--color-primary); color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                        ✍️ レビューを書く
                    </button>
                </div>

                <div id="reviewFormContainer" style="display: none; background: #fff; border: 2px dashed #ccc; border-radius: var(--radius-sm); padding: 20px; margin-bottom: 30px;">
                    <h3 style="font-size: 1.1rem; margin-top: 0; margin-bottom: 16px; font-weight: 700; color: #333;">この商品のレビューを投稿する</h3>
                    <form id="reviewSubmitForm" style="display: flex; flex-direction: column; gap: 16px;">
                        <div>
                            <label style="display: block; font-size: 0.9rem; font-weight: 700; margin-bottom: 6px;">評価（星の数） <span style="color: red;">*</span></label>
                            <div style="display: flex; gap: 15px; font-size: 1.1rem;">
                                <label style="cursor: pointer;"><input type="radio" name="rating" value="5" checked style="margin-right: 4px;"><span style="color: #ffcc00;">★★★★★</span> 5</label>
                                <label style="cursor: pointer;"><input type="radio" name="rating" value="4" style="margin-right: 4px;"><span style="color: #ffcc00;">★★★★☆</span> 4</label>
                                <label style="cursor: pointer;"><input type="radio" name="rating" value="3" style="margin-right: 4px;"><span style="color: #ffcc00;">★★★☆☆</span> 3</label>
                                <label style="cursor: pointer;"><input type="radio" name="rating" value="2" style="margin-right: 4px;"><span style="color: #ffcc00;">★★☆☆☆</span> 2</label>
                                <label style="cursor: pointer;"><input type="radio" name="rating" value="1" style="margin-right: 4px;"><span style="color: #ffcc00;">★☆☆☆☆</span> 1</label>
                            </div>
                        </div>

                        <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                            <div style="flex: 1; min-width: 200px;">
                                <label for="reviewerName" style="display: block; font-size: 0.9rem; font-weight: 700; margin-bottom: 6px;">ニックネーム <span style="color: red;">*</span></label>
                                <input type="text" id="reviewerName" required placeholder="例：りりあちゃん さん" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                            </div>
                            <div style="flex: 2; min-width: 280px;">
                                <label for="reviewTitle" style="display: block; font-size: 0.9rem; font-weight: 700; margin-bottom: 6px;">評価タイトル <span style="color: red;">*</span></label>
                                <input type="text" id="reviewTitle" required placeholder="例：とても気に入りました！" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                            </div>
                        </div>

                        <div>
                            <label for="reviewContent" style="display: block; font-size: 0.9rem; font-weight: 700; margin-bottom: 6px;">レビュー本文 <span style="color: red;">*</span></label>
                            <textarea id="reviewContent" rows="4" required placeholder="商品の感想、気に入った点などを自由に書いてください" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; resize: vertical;"></textarea>
                        </div>

                        <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 8px;">
                            <button type="button" id="cancelFormBtn" style="padding: 8px 16px; background-color: #f0f2f5; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;">キャンセル</button>
                            <button type="submit" style="padding: 8px 24px; background-color: var(--color-accent); color: var(--color-black); border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">投稿する</button>
                        </div>
                    </form>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 20px; border-bottom: 1px solid #e4e7ec; padding-bottom: 12px;">
                    <div style="display: flex; gap: 8px; overflow-x: auto;">
                        <button class="review-filter-btn active" data-stars="all" style="padding: 6px 12px; border: 1px solid #ccc; background-color: #fff; border-radius: 20px; font-size: 0.85rem; cursor: pointer; white-space: nowrap;">すべて</button>
                        <button class="review-filter-btn" data-stars="5" style="padding: 6px 12px; border: 1px solid #ccc; background-color: #fff; border-radius: 20px; font-size: 0.85rem; cursor: pointer; white-space: nowrap;">★5のみ</button>
                        <button class="review-filter-btn" data-stars="4" style="padding: 6px 12px; border: 1px solid #ccc; background-color: #fff; border-radius: 20px; font-size: 0.85rem; cursor: pointer; white-space: nowrap;">★4のみ</button>
                        <button class="review-filter-btn" data-stars="3" style="padding: 6px 12px; border: 1px solid #ccc; background-color: #fff; border-radius: 20px; font-size: 0.85rem; cursor: pointer; white-space: nowrap;">★3以下</button>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px;">
                        <label for="reviewSort" style="font-size: 0.85rem; color: #555; font-weight: 500; white-space: nowrap;">並び替え：</label>
                        <select id="reviewSort" style="padding: 6px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem; cursor: pointer; background: #fff;">
                            <option value="date-desc">投稿日の新しい順 (降順)</option>
                            <option value="date-asc">投稿日の古い順 (昇順)</option>
                            <option value="rating-desc">評価の高い順 (降順)</option>
                            <option value="rating-asc">評価の低い順 (昇順)</option>
                        </select>
                    </div>
                </div>

                <div id="reviewsList" style="display: flex; flex-direction: column; gap: 20px;">
                    <div class="review-item" data-rating="5" data-date="2026-06-18" style="border-bottom: 1px solid #e4e7ec; padding-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <div>
                                <span class="stars" style="color: #ffcc00;">★★★★★</span>
                                <strong style="margin-left: 8px; font-size: 0.95rem;">後味がすっきりで飲みやすい！</strong>
                            </div>
                            <span style="font-size: 0.8rem; color: #999;">2026/06/18</span>
                        </div>
                        <p style="font-size: 0.9rem; color: #444; line-height: 1.6; margin: 0 0 8px 0;">
                            しっかり果汁感があってすごく美味しいです！配送もスピーディーで助かりました。リピ確定です！
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                            <span style="font-size: 0.8rem; color: #777;">購入者：りりあちゃん さん</span>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 0.8rem; color: #666;">このレビューは参考になりましたか？</span>
                                <button class="like-btn" onclick="handleLike(this)" style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; background: #fff; border: 1px solid #ccc; border-radius: 14px; font-size: 0.8rem; cursor: pointer; color: #333; font-weight: 500; transition: all 0.2s;">
                                    👍 <span class="like-count">12</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="noReviewMessage" style="display: none; text-align: center; padding: 30px; color: #666; font-size: 0.95rem;">
                    選択された評価のレビューはまだありません。
                </div>
            </section>

        </div>
    </main>

    <footer class="footer">
        <div class="container footer-inner">
            <div class="footer-col">
                <p class="footer-logo">Yahoo!ショッピング風サイト</p>
                <p class="footer-tagline">毎日の生活をもっと豊かに、おトクに。</p>
            </div>
            <div class="footer-col">
                <h4 class="footer-heading">運営チーム</h4>
                <ul class="footer-links">
                    <li><a href="#">会社概要・チーム紹介</a></li>
                    <li><a href="#">プライバシーポリシー</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 Yahoo!ショッピング風チーム制作プロジェクト. All rights reserved.</p>
        </div>
    </footer>

    <!-- JavaScript部 -->
    <script>
        // 数量変更
        let qty = 1;
        document.getElementById('qtyUp').addEventListener('click', () => {
            if (qty < 9) { qty++; document.getElementById('qtyVal').textContent = qty; }
        });
        document.getElementById('qtyDown').addEventListener('click', () => {
            if (qty > 1) { qty--; document.getElementById('qtyVal').textContent = qty; }
        });

        // サムネイル切り替え（メイン画像の差し替え）
        document.querySelectorAll('.pd-thumb').forEach(thumb => {
            thumb.addEventListener('click', () => {
                document.querySelectorAll('.pd-thumb').forEach(t => t.classList.remove('active'));
                thumb.classList.add('active');
                const mainImg = document.getElementById('pdMainImg');
                if (mainImg) {
                    mainImg.src = thumb.dataset.img;
                }
            });
        });

        // ==========================================
        // レビュー機能
        // ==========================================
        const toggleFormBtn = document.getElementById('toggleFormBtn');
        const reviewFormContainer = document.getElementById('reviewFormContainer');
        const cancelFormBtn = document.getElementById('cancelFormBtn');
        const reviewSubmitForm = document.getElementById('reviewSubmitForm');
        const reviewsList = document.getElementById('reviewsList');
        const noReviewMessage = document.getElementById('noReviewMessage');
        const filterBtns = document.querySelectorAll('.review-filter-btn');
        const reviewSortSelect = document.getElementById('reviewSort');

        const mainReviewCount = document.getElementById('mainReviewCount');
        const mainRatingAvg = document.getElementById('mainRatingAvg');
        const summaryReviewCount = document.getElementById('summaryReviewCount');
        const summaryRatingAvg = document.getElementById('summaryRatingAvg');

        let totalReviews = 128;
        let totalScore = 4.7 * 128;
        let currentFilter = 'all'; 

        toggleFormBtn.addEventListener('click', () => {
            reviewFormContainer.style.display = reviewFormContainer.style.display === 'none' ? 'block' : 'none';
        });
        cancelFormBtn.addEventListener('click', () => {
            reviewFormContainer.style.display = 'none';
            reviewSubmitForm.reset();
        });

        reviewSubmitForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const ratingVal = parseInt(document.querySelector('input[name="rating"]:checked').value, 10);
            const nameVal = document.getElementById('reviewerName').value;
            const titleVal = document.getElementById('reviewTitle').value;
            const contentVal = document.getElementById('reviewContent').value;

            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const date = String(today.getDate()).padStart(2, '0');
            const dateIso = `${year}-${month}-${date}`;
            const dateStr = `${year}/${month}/${date}`;

            const starString = '★'.repeat(ratingVal) + '☆'.repeat(5 - ratingVal);

            const newReview = document.createElement('div');
            newReview.className = 'review-item';
            newReview.setAttribute('data-rating', ratingVal);
            newReview.setAttribute('data-date', dateIso); 
            newReview.style.borderBottom = '1px solid #e4e7ec';
            newReview.style.paddingBottom = '20px';

            newReview.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <div>
                        <span class="stars" style="color: #ffcc00;">${starString}</span>
                        <strong style="margin-left: 8px; font-size: 0.95rem;">${escapeHTML(titleVal)}</strong>
                    </div>
                    <span style="font-size: 0.8rem; color: #999;">${dateStr}</span>
                </div>
                <p style="font-size: 0.9rem; color: #444; line-height: 1.6; margin: 0 0 8px 0;">
                    ${escapeHTML(contentVal).replace(/\n/g, '<br>')}
                </p>
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <span style="font-size: 0.8rem; color: #777;">購入者：${escapeHTML(nameVal)}</span>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 0.8rem; color: #666;">このレビューは参考になりましたか？</span>
                        <button class="like-btn" onclick="handleLike(this)" style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; background: #fff; border: 1px solid #ccc; border-radius: 14px; font-size: 0.8rem; cursor: pointer; color: #333; font-weight: 500; transition: all 0.2s;">
                            👍 <span class="like-count">0</span>
                        </button>
                    </div>
                </div>
            `;

            reviewsList.appendChild(newReview);

            totalReviews += 1;
            totalScore += ratingVal;
            const newAvg = (totalScore / totalReviews).toFixed(1);

            mainReviewCount.textContent = totalReviews;
            summaryReviewCount.textContent = totalReviews;
            mainRatingAvg.textContent = newAvg;
            summaryRatingAvg.textContent = newAvg;

            reviewFormContainer.style.display = 'none';
            reviewSubmitForm.reset();

            reviewSortSelect.value = 'date-desc';
            sortReviews();
            setActiveFilter('all');
        });

        function escapeHTML(str) {
            return str.replace(/[&<>'"]/g,
                tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
            );
        }

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                setActiveFilter(btn.dataset.stars);
            });
        });

        function setActiveFilter(filterVal) {
            currentFilter = filterVal;

            filterBtns.forEach(btn => {
                if (btn.dataset.stars === filterVal) {
                    btn.classList.add('active');
                    btn.style.backgroundColor = 'var(--color-accent)';
                    btn.style.color = 'var(--color-black)';
                    btn.style.borderColor = 'var(--color-accent)';
                } else {
                    btn.classList.remove('active');
                    btn.style.backgroundColor = '#fff';
                    btn.style.color = '#333';
                    btn.style.borderColor = '#ccc';
                }
            });

            const reviewItems = document.querySelectorAll('.review-item');
            let visibleCount = 0;

            reviewItems.forEach(item => {
                const itemRating = parseInt(item.getAttribute('data-rating'), 10);

                if (filterVal === 'all' || 
                   (filterVal === '5' && itemRating === 5) || 
                   (filterVal === '4' && itemRating === 4) || 
                   (filterVal === '3' && itemRating <= 3)) {
                    item.style.display = 'block';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            noReviewMessage.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        function sortReviews() {
            const sortVal = reviewSortSelect.value;
            const items = Array.from(reviewsList.querySelectorAll('.review-item'));

            items.sort((a, b) => {
                const ratingA = parseInt(a.getAttribute('data-rating'), 10);
                const ratingB = parseInt(b.getAttribute('data-rating'), 10);
                const dateA = new Date(a.getAttribute('data-date'));
                const dateB = new Date(b.getAttribute('data-date'));

                if (sortVal === 'date-desc') return dateB - dateA;
                if (sortVal === 'date-asc') return dateA - dateB;
                if (sortVal === 'rating-desc') return ratingB - ratingA;
                if (sortVal === 'rating-asc') return ratingA - ratingB;
                return 0;
            });

            items.forEach(item => reviewsList.appendChild(item));
            setActiveFilter(currentFilter);
        }

        reviewSortSelect.addEventListener('change', sortReviews);

        function handleLike(button) {
            const isLiked = button.classList.contains('liked');
            const countSpan = button.querySelector('.like-count');
            let currentCount = parseInt(countSpan.textContent, 10);

            if (!isLiked) {
                currentCount++;
                button.classList.add('liked');
                button.style.background = '#ffe5d9'; 
                button.style.borderColor = 'var(--color-accent)';
                button.style.color = 'var(--color-black)';
            } else {
                currentCount--;
                button.classList.remove('liked');
                button.style.background = '#fff';
                button.style.borderColor = '#ccc';
                button.style.color = '#333';
            }

            countSpan.textContent = currentCount;
        }

        setActiveFilter('all');
        sortReviews();
    </script>
</body>
</html>