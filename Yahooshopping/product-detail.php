<?php
session_start();

// 1. データベース接続ファイルを読み込む
require_once 'db.php';

// レビュー投稿の結果メッセージ（review_ins.php からのリダイレクト用）
$review_error = $_SESSION['review_error'] ?? null;
unset($_SESSION['review_error']);

try {
    // 2. URLから option_id を取得する（送られてこなかった場合はデフォルトで 1）
    $target_option_id = isset($_GET['option_id']) ? intval($_GET['option_id']) : 1;

    /* 
       3. 必要なカラムを個別に明確に指定して取得する
    */
        $sql = "SELECT 
                p.product_id,
                o.option_id,
                o.variation_id,
                o.option_name,
                o.price,
                o.stock,
                p.product_name, 
                p.information 
            FROM product_attributes_options AS o
            INNER JOIN product_attributes AS pa ON o.variation_id = pa.variation_id
            INNER JOIN product AS p ON pa.product_id = p.product_id 
            WHERE o.option_id = :id";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $target_option_id]);
    $product_data = $stmt->fetch(PDO::FETCH_ASSOC);

    // データが万が一取れなかったときの安全対策
    if (!$product_data) {
        $stmt_fallback = $pdo->prepare("SELECT * FROM product_attributes_options WHERE option_id = :id");
        $stmt_fallback->execute(['id' => $target_option_id]);
        $product_data = $stmt_fallback->fetch(PDO::FETCH_ASSOC);
        
        if ($product_data) {
            $product_data['product_name'] = $product_data['product_name'] ?? '商品名（仮）';
            $product_data['information'] = $product_data['information'] ?? '商品説明が取得できませんでした。';
        } else {
            exit('指定された商品オプションが見つかりませんでした (option_id: ' . $target_option_id . ')');
        }
    }

    // 4. 同じバリエーショングループの選択肢をすべて取得
    $stmt_all = $pdo->prepare("SELECT * FROM product_attributes_options WHERE variation_id = :variation_id");
    $stmt_all->execute(['variation_id' => $product_data['variation_id']]);
    $all_options = $stmt_all->fetchAll(PDO::FETCH_ASSOC);

    // 5. このオプションに紐づく画像をすべて取得
    $stmt_images = $pdo->prepare("SELECT image_url FROM product_images WHERE option_id = :option_id ORDER BY display_order ASC");
    $stmt_images->execute(['option_id' => $target_option_id]);
    $db_images = $stmt_images->fetchAll(PDO::FETCH_COLUMN);

    // 6. ログイン中のユーザーが、このオプションを既にお気に入り登録しているか確認
    $is_favorited = false;
    if (isset($_SESSION['user']['id'])) {
        $stmt_fav = $pdo->prepare("SELECT product_favorites_id FROM product_favorites WHERE user_id = :user_id AND option_id = :option_id");
        $stmt_fav->execute([
            'user_id'   => $_SESSION['user']['id'],
            'option_id' => $target_option_id
        ]);
        $is_favorited = (bool) $stmt_fav->fetch();
    }

    // 6.5. この商品（オプション）に対する公開された問い合わせと返答履歴を取得
    $stmt_inquiries = $pdo->prepare("
        SELECT i.inquiry_id, i.title, i.inquiry_contents, i.user_mailaddress, i.inquiry_at,
               ih.contents AS reply_contents, ih.send_time AS reply_time
        FROM inquiry AS i
        LEFT JOIN inquiry_history AS ih ON i.inquiry_id = ih.inquiry_id AND ih.sender = 0
        WHERE i.option_id = :option_id AND i.privacy = 0
        ORDER BY i.inquiry_at DESC
    ");
    $stmt_inquiries->execute(['option_id' => $target_option_id]);
    $product_inquiries = $stmt_inquiries->fetchAll(PDO::FETCH_ASSOC);

    // 画像パスの整形
    $product_images = [];
    foreach ($db_images as $img) {
        if (!empty($img)) {
            $product_images[] = preg_replace('/^localhost\/(Yahooshopping\/)?/i', '', $img);
        }
    }

    // 7. このオプションに投稿されたレビューを取得（閲覧数・いいね数、自分が既に押したかも含める）
    $current_user_id = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
    $stmt_reviews = $pdo->prepare(
        "SELECT r.review_id, r.rating, r.title, r.comment, r.created_at, r.image_url,
                COALESCE(r.views_count, 0) AS views_count,
                (SELECT COUNT(*) FROM review_likes rl WHERE rl.review_id = r.review_id) AS like_count,
                EXISTS (
                    SELECT 1 FROM review_likes rl2
                    WHERE rl2.review_id = r.review_id AND rl2.user_id = :current_user_id
                ) AS liked_by_me,
                u.user_name
         FROM product_reviews r
         INNER JOIN user_account u ON r.user_id = u.user_id
         WHERE r.option_id = :option_id
         ORDER BY r.created_at DESC"
    );
    $stmt_reviews->execute(['option_id' => $target_option_id, 'current_user_id' => $current_user_id]);
    $reviews = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);

    $review_count = count($reviews);
    $review_avg = $review_count > 0
        ? round(array_sum(array_column($reviews, 'rating')) / $review_count, 1)
        : 0;

    // 8. ページを開いた分だけ、表示した各レビューの閲覧数を+1する
    if (!empty($reviews)) {
        $reviewIds = array_column($reviews, 'review_id');
        $placeholders = implode(',', array_fill(0, count($reviewIds), '?'));
        $stmt_view = $pdo->prepare(
            "UPDATE product_reviews SET views_count = COALESCE(views_count, 0) + 1 WHERE review_id IN ($placeholders)"
        );
        $stmt_view->execute($reviewIds);

        // 画面表示にもすぐ反映させる
        foreach ($reviews as &$rv_ref) {
            $rv_ref['views_count']++;
        }
        unset($rv_ref);
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
    <style>
        .like-btn.liked {
            background: #ffe5d9 !important;
            border-color: var(--color-accent) !important;
            color: var(--color-black) !important;
        }
    </style>
</head>
<body>

     <?php require_once('header.php'); ?>

    <main class="main-bg-gray">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php">トップ</a> ＞ <a href="#">ドリンク、水、お酒</a> ＞ <span><?= htmlspecialchars($product_data['product_name']) ?></span>
            </div>

            <div class="pd-container">
                <div class="pd-image-area">
                    <div style="width: 100%; height: 350px; display: flex; align-items: center; justify-content: center; background: #fff; border: 1px solid #e4e7ec; border-radius: 8px; overflow: hidden; padding: 10px;">
                        <?php if (!empty($product_images)): ?>
                            <img id="pdMainImg" src="<?= htmlspecialchars($product_images[0]) ?>" alt="<?= htmlspecialchars($product_data['product_name']) ?>" class="pd-main-img" style="max-width: 100%; max-height: 100%; object-fit: contain; display: block;">
                        <?php else: ?>
                            <div style="font-size: 3rem; color: #ccc;">🎁</div>
                        <?php endif; ?>
                    </div>

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
                    <h1 class="pd-title">
                        <?= htmlspecialchars($product_data['product_name']) ?> 
                        (<?= htmlspecialchars($product_data['option_name']) ?>)
                    </h1>

                    <div class="pd-rating-row">
                        <span class="stars" style="color: #ffcc00;">★★★★★</span>
                        <span class="rating-avg" id="mainRatingAvg"><?= $review_count > 0 ? $review_avg : '-' ?></span>
                        <a href="#reviews-section" class="rating-link">レビュー<span id="mainReviewCount"><?= $review_count ?></span>件を見る</a>
                    </div>

                    <div class="pd-price-box">
                        <p class="pd-price">¥<?= number_format($product_data['price']) ?> <span class="tax">税込</span></p>
                        <p style="font-size: 0.85rem; color: #d9534f; font-weight: bold; margin-top: 5px;">
                            🔥 残り在庫数: <?= htmlspecialchars($product_data['stock']) ?>個
                        </p>
                        <p class="pd-points">➕ PayPayポイントが毎日5%貯まる！</p>
                    </div>

                    <div class="pd-options">
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
                            <form method="post" action="cart-add.php">
                                <input type="hidden" name="option_id" value="<?= $target_option_id ?>">
                                <input type="hidden" name="quantity" id="qtyHidden" value="1">
                                <button type="submit" class="pd-btn-cart">🛒 カートに入れる</button>
                            </form>
                        <?php else: ?>
                            <button class="pd-btn-cart" disabled style="background-color: #ccc; cursor: not-allowed;">❌ 売り切れです</button>
                        <?php endif; ?>

                        <button type="button" id="favoriteBtn" class="pd-btn-favorite<?= $is_favorited ? ' active' : '' ?>" data-option-id="<?= $target_option_id ?>" data-favorited="<?= $is_favorited ? '1' : '0' ?>" onclick="toggleFavorite(this)">
                            <svg class="heart-icon" viewBox="0 0 24 24" width="20" height="20">
                                <path d="M12 21s-6.7-4.35-9.3-8.1C1.1 10.6 1 8.2 2.6 6.5 4.2 4.8 6.8 4.8 8.4 6.5L12 10.3l3.6-3.8c1.6-1.7 4.2-1.7 5.8 0 1.6 1.7 1.5 4.1-0.1 6.4C18.7 16.65 12 21 12 21z"
                                      fill="none" stroke="currentColor" stroke-width="1.8"/>
                            </svg>
                            <span id="favoriteLabel"><?= $is_favorited ? 'お気に入り済み' : 'お気に入り' ?></span>
                        </button>
                    </div>
                </div>
            </div>

            <section style="background: #fff; border: 1px solid #e4e7ec; border-radius: var(--radius-md); padding: 30px; margin-top: 30px; box-shadow: var(--shadow-card);">
                <h2 style="font-size: 1.3rem; font-weight: 700; border-bottom: 2px solid var(--color-accent); padding-bottom: 8px; margin-bottom: 20px;">商品説明</h2>
                <div style="line-height: 1.8; color: var(--color-black); font-size: 0.95rem;">
                    <p style="margin-bottom: 16px; font-weight: 500; white-space: pre-wrap;"><?= htmlspecialchars($product_data['information']) ?></p>
                </div>
            </section>

            <!-- ================= 問い合わせ・Q&Aセクション ================= -->
            <section id="inquiry-section" style="background: #fff; border: 1px solid #e4e7ec; border-radius: var(--radius-md); padding: 30px; margin-top: 30px; box-shadow: var(--shadow-card);">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; border-bottom: 2px solid var(--color-accent); padding-bottom: 8px; margin-bottom: 20px;">
                    <h2 style="font-size: 1.3rem; font-weight: 700; margin: 0;">カスタマーQ&A（出品者からの回答）</h2>
                    <button type="button" id="openInquiryModalBtn" style="padding: 8px 16px; background-color: #2c3e50; color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 0.9rem;">
                        💬 出品者に問い合わせる
                    </button>
                </div>

                <!-- Q&A一覧表示 -->
                <?php if (!empty($product_inquiries)): ?>
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <?php foreach ($product_inquiries as $inq): ?>
                            <div style="background: #f7f9fa; border: 1px solid #e4e7ec; border-radius: 6px; padding: 20px;">
                                <!-- 質問部分 -->
                                <div style="margin-bottom: 12px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                        <span style="font-size: 0.8rem; background: #e2e8f0; color: #334155; padding: 2px 8px; border-radius: 4px; font-weight: bold;">質問</span>
                                        <span style="font-size: 0.8rem; color: #888;"><?= htmlspecialchars($inq['inquiry_at']) ?></span>
                                    </div>
                                    <h3 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0 0 6px 0;">
                                        <?= htmlspecialchars($inq['title']) ?>
                                    </h3>
                                    <p style="font-size: 0.9rem; color: #475569; margin: 0; white-space: pre-wrap;"><?= htmlspecialchars($inq['inquiry_contents']) ?></p>
                                </div>

                                <!-- 返答部分（出品者からの返信がある場合） -->
                                <?php if (!empty($inq['reply_contents'])): ?>
                                    <div style="background: #fff; border-left: 4px solid #0056b3; padding: 12px 16px; margin-top: 12px; border-radius: 0 4px 4px 0;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                            <span style="font-size: 0.75rem; background: #0056b3; color: #fff; padding: 2px 6px; border-radius: 3px; font-weight: bold;">ストアからの回答</span>
                                            <span style="font-size: 0.75rem; color: #888;"><?= htmlspecialchars($inq['reply_time']) ?></span>
                                        </div>
                                        <p style="font-size: 0.9rem; color: #333; margin: 0; white-space: pre-wrap;"><?= htmlspecialchars($inq['reply_contents']) ?></p>
                                    </div>
                                <?php else: ?>
                                    <div style="margin-top: 10px; font-size: 0.85rem; color: #999; font-style: italic;">
                                        ※出品者からの回答をお待ちください。
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="color: #666; font-size: 0.95rem; text-align: center; padding: 20px 0; margin: 0;">
                        この商品に関する質問はまだありません。気になることはお気軽に質問してみましょう！
                    </p>
                <?php endif; ?>
            </section>

            <section id="reviews-section" style="background: #fff; border: 1px solid #e4e7ec; border-radius: var(--radius-md); padding: 30px; margin-top: 30px; margin-bottom: 30px; box-shadow: var(--shadow-card);">
                <h2 style="font-size: 1.3rem; font-weight: 700; border-bottom: 2px solid var(--color-accent); padding-bottom: 8px; margin-bottom: 20px;">商品レビュー・口コミ</h2>
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px; background: #f7f9fa; padding: 20px; border-radius: var(--radius-sm); margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; gap: 24px;">
                        <div style="text-align: center; border-right: 1px solid #e4e7ec; padding-right: 24px;">
                            <span style="font-size: 2.5rem; font-weight: 700; color: var(--color-black); line-height: 1;" id="summaryRatingAvg"><?= $review_count > 0 ? $review_avg : '-' ?></span>
                            <span style="display: block; font-size: 0.8rem; color: #666; margin-top: 4px;">総合評価（<span id="summaryReviewCount"><?= $review_count ?></span>件）</span>
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

                <div id="reviewFormContainer" style="display: <?= $review_error ? 'block' : 'none' ?>; background: #fff; border: 2px dashed #ccc; border-radius: var(--radius-sm); padding: 20px; margin-bottom: 30px;">
                    <h3 style="font-size: 1.1rem; margin-top: 0; margin-bottom: 16px; font-weight: 700; color: #333;">この商品のレビューを投稿する</h3>
                    <?php if ($review_error): ?>
                        <p style="color: #c00; background: #fdecea; border: 1px solid #f5c2c0; border-radius: 4px; padding: 10px 14px; margin: 0 0 16px 0; font-size: 0.9rem;">
                            <?= htmlspecialchars($review_error, ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    <?php endif; ?>
                    <form id="reviewSubmitForm" action="review_ins.php" method="post" style="display: flex; flex-direction: column; gap: 16px;">
                        <input type="hidden" name="option_id" value="<?= (int)$target_option_id ?>">
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

                        <div>
                            <label for="reviewTitle" style="display: block; font-size: 0.9rem; font-weight: 700; margin-bottom: 6px;">評価タイトル <span style="color: red;">*</span></label>
                            <input type="text" id="reviewTitle" name="title" required placeholder="例：とても気に入りました！" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                        </div>

                        <div>
                            <label for="reviewContent" style="display: block; font-size: 0.9rem; font-weight: 700; margin-bottom: 6px;">レビュー本文 <span style="color: red;">*</span></label>
                            <textarea id="reviewContent" name="contents" rows="4" required placeholder="商品の感想、気に入った点などを自由に書いてください" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; resize: vertical;"></textarea>
                        </div>

                        <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 8px;">
                            <button type="button" id="cancelFormBtn" style="padding: 8px 16px; background-color: #f0f2f5; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;">キャンセル</button>
                            <button type="submit" name="cmdBtn1" value="1" style="padding: 8px 24px; background-color: var(--color-accent); color: var(--color-black); border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">投稿する</button>
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
                    <?php foreach ($reviews as $rv): ?>
                        <?php
                            $rv_rating   = (int)$rv['rating'];
                            $rv_dateIso  = date('Y-m-d', strtotime($rv['created_at']));
                            $rv_dateDisp = date('Y/m/d', strtotime($rv['created_at']));
                            $rv_stars    = str_repeat('★', $rv_rating) . str_repeat('☆', 5 - $rv_rating);
                        ?>
                        <div class="review-item" data-rating="<?= $rv_rating ?>" data-date="<?= $rv_dateIso ?>" style="border-bottom: 1px solid #e4e7ec; padding-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <div>
                                    <span class="stars" style="color: #ffcc00;"><?= $rv_stars ?></span>
                                    <strong style="margin-left: 8px; font-size: 0.95rem;"><?= htmlspecialchars($rv['title'], ENT_QUOTES, 'UTF-8') ?></strong>
                                </div>
                                <span style="font-size: 0.8rem; color: #999;"><?= $rv_dateDisp ?></span>
                            </div>
                            <p style="font-size: 0.9rem; color: #444; line-height: 1.6; margin: 0 0 8px 0;">
                                <?= nl2br(htmlspecialchars($rv['comment'], ENT_QUOTES, 'UTF-8')) ?>
                            </p>
                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                                <span style="font-size: 0.8rem; color: #777;">
                                    購入者：<?= htmlspecialchars($rv['user_name'], ENT_QUOTES, 'UTF-8') ?> さん
                                    <span style="margin-left: 10px; color: #aaa;">👁 閲覧数 <?= (int)$rv['views_count'] ?></span>
                                </span>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-size: 0.8rem; color: #666;">このレビューは参考になりましたか？</span>
                                    <button class="like-btn<?= $rv['liked_by_me'] ? ' liked' : '' ?>" onclick="handleLike(this)" data-review-id="<?= (int)$rv['review_id'] ?>" style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; background: #fff; border: 1px solid #ccc; border-radius: 14px; font-size: 0.8rem; cursor: pointer; color: #333; font-weight: 500; transition: all 0.2s;">
                                        👍 <span class="like-count"><?= (int)$rv['like_count'] ?></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div id="noReviewMessage" style="display: none; text-align: center; padding: 30px; color: #666; font-size: 0.95rem;">
                    選択された評価のレビューはまだありません。
                </div>
            </section>
        </div>
    </main>

    <!-- ================= 問い合わせモーダルフォーム ================= -->
    <div id="inquiryModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #fff; width: 90%; max-width: 600px; border-radius: 8px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); position: relative; max-height: 90vh; overflow-y: auto;">
            <button type="button" id="closeInquiryModalBtn" style="position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #666;">&times;</button>
            
            <h3 style="font-size: 1.2rem; font-weight: 700; margin-top: 0; margin-bottom: 20px; color: #2c3e50;">出品者へのお問い合わせ</h3>
            
            <form action="inquiry_send.php" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
                <input type="hidden" name="option_id" value="<?= $target_option_id ?>">
                
                <div>
                    <label style="display: block; font-size: 0.9rem; font-weight: 700; margin-bottom: 6px;">件名 <span style="color: red;">*</span></label>
                    <input type="text" name="title" required placeholder="例：賞味期限や配送について" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.9rem; font-weight: 700; margin-bottom: 6px;">メールアドレス <span style="color: red;">*</span></label>
                    <input type="email" name="user_mailaddress" required value="<?= isset($_SESSION['user']['email']) ? htmlspecialchars($_SESSION['user']['email']) : '' ?>" placeholder="example@email.com" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.9rem; font-weight: 700; margin-bottom: 6px;">問い合わせ内容 <span style="color: red;">*</span></label>
                    <textarea name="inquiry_contents" rows="5" required placeholder="質問内容を具体的にご記入ください" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; resize: vertical;"></textarea>
                </div>

                <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem;">
                    <input type="checkbox" id="privacy" name="privacy" value="0" checked style="width: 18px; height: 18px; cursor: pointer;">
                    <label for="privacy" style="cursor: pointer; color: #444;">この質問と回答を商品ページに公開する（チェックを外すと非公開になります）</label>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px;">
                    <button type="button" id="cancelInquiryModalBtn" style="padding: 10px 20px; background: #f0f2f5; border: 1px solid #ccc; border-radius: 4px; cursor: pointer; font-weight: bold;">キャンセル</button>
                    <button type="submit" style="padding: 10px 24px; background: var(--color-accent); color: var(--color-black); border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">送信する</button>
                </div>
            </form>
        </div>
    </div>

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

    <script>
        // ==== お気に入りボタン（DB連携） ====
        (function () {
            const btn = document.getElementById('favoriteBtn');
            const label = document.getElementById('favoriteLabel');

            function applyState(isActive) {
                btn.classList.toggle('active', isActive);
                label.textContent = isActive ? 'お気に入り済み' : 'お気に入り';
                btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                btn.dataset.favorited = isActive ? '1' : '0';
            }

            window.toggleFavorite = function (button) {
                const optionId = button.dataset.optionId;
                button.disabled = true;

                fetch('favorites_insert.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'option_id=' + encodeURIComponent(optionId)
                })
                .then(res => {
                    if (res.status === 401) {
                        alert('お気に入り登録にはログインが必要です');
                        window.location.href = 'login_view.php';
                        return null;
                    }
                    return res.json();
                })
                .then(data => {
                    if (!data) return;
                    if (data.status === 'ok') {
                        applyState(data.favorited);
                    } else {
                        alert(data.message || 'お気に入りの更新に失敗しました');
                    }
                })
                .catch(() => {
                    alert('通信エラーが発生しました');
                })
                .finally(() => {
                    button.disabled = false;
                });
            };
        })();

        let qty = 1;
        document.getElementById('qtyUp').addEventListener('click', () => {
            if (qty < 9) { 
                qty++; 
                document.getElementById('qtyVal').textContent = qty; 
                document.getElementById('qtyHidden').value = qty; 
            }
        });
        document.getElementById('qtyDown').addEventListener('click', () => {
            if (qty > 1) { 
                qty--; 
                document.getElementById('qtyVal').textContent = qty; 
                document.getElementById('qtyHidden').value = qty; 
            }
        });

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

        // ==== 問い合わせモーダル用のJS ====
        const inquiryModal = document.getElementById('inquiryModal');
        const openInquiryModalBtn = document.getElementById('openInquiryModalBtn');
        const closeInquiryModalBtn = document.getElementById('closeInquiryModalBtn');
        const cancelInquiryModalBtn = document.getElementById('cancelInquiryModalBtn');

        openInquiryModalBtn.addEventListener('click', () => {
            <?php if (!isset($_SESSION['user']['id'])): ?>
                alert('お問い合わせにはログインが必要です。');
                window.location.href = 'login_view.php';
                return;
            <?php endif; ?>
            inquiryModal.style.display = 'flex';
        });

        closeInquiryModalBtn.addEventListener('click', () => {
            inquiryModal.style.display = 'none';
        });

        cancelInquiryModalBtn.addEventListener('click', () => {
            inquiryModal.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === inquiryModal) {
                inquiryModal.style.display = 'none';
            }
        });

        // ==== レビュー関連のJS ====
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

        let totalReviews = <?= (int)$review_count ?>;
        let totalScore = <?= (float)$review_avg ?> * <?= (int)$review_count ?>;
        let currentFilter = 'all'; 

        toggleFormBtn.addEventListener('click', () => {
            reviewFormContainer.style.display = reviewFormContainer.style.display === 'none' ? 'block' : 'none';
        });
        cancelFormBtn.addEventListener('click', () => {
            reviewFormContainer.style.display = 'none';
            reviewSubmitForm.reset();
        });

        // 投稿フォームは review_ins.php へ通常送信（サーバー側でDB登録）されるため、
        // ここでは特別な submit ハンドラは設定しない。

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
                const dateA = new Date(a.getAttribute('data-date'));
                const dateB = new Date(b.getAttribute('data-date'));
                const ratingA = parseInt(a.getAttribute('data-rating'), 10);
                const ratingB = parseInt(b.getAttribute('data-rating'), 10);

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
            if (button.disabled) return;

            const reviewId = button.dataset.reviewId;
            const isLiked = button.classList.contains('liked');
            const action = isLiked ? 'unlike' : 'like';
            const countSpan = button.querySelector('.like-count');

            button.disabled = true;

            fetch('review_like.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'review_id=' + encodeURIComponent(reviewId) + '&action=' + encodeURIComponent(action)
            })
            .then(res => {
                if (res.status === 401) {
                    alert('いいねにはログインが必要です');
                    window.location.href = 'login_view.php';
                    return null;
                }
                return res.text().then(text => ({ ok: res.ok, status: res.status, text }));
            })
            .then(result => {
                if (!result) return; // 401でログイン画面へ遷移した場合

                const { ok, status, text } = result;
                let data;
                try {
                    data = JSON.parse(text);
                } catch (parseErr) {
                    console.error('review_like.php returned non-JSON response:', status, text);
                    alert('サーバーからの応答が不正です（status ' + status + '）。コンソールに詳細を出力しました。');
                    return;
                }

                if (!ok || data.status !== 'ok') {
                    alert(data.message || 'いいねの更新に失敗しました');
                    return;
                }

                countSpan.textContent = data.like_count;
                button.classList.toggle('liked', data.liked);
            })
            .catch(err => {
                console.error('review_like.php fetch failed:', err);
                alert('通信エラーが発生しました（コンソールに詳細を出力しました）');
            })
            .finally(() => {
                button.disabled = false;
            });
        }

        setActiveFilter('all');
        sortReviews();
    </script>
</body>
</html>