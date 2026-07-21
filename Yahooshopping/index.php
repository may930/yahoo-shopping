<?php
// 1. データベース接続ファイルを読み込む
require_once 'db.php';

try {
    /* 
       2. 売れ筋人気ランキング用データ取得
       product, product_attributes_options, product_images の3つを結合
    */
    $sql = "SELECT 
                p.product_id,
                p.product_name,
                p.information,
                p.category_id,
                IFNULL(MIN(o.option_id), 1) AS option_id,
                IFNULL(MIN(o.price), 0) AS price,
                img.image_url
            FROM product AS p
            LEFT JOIN product_attributes_options AS o ON p.product_id = o.variation_id
            LEFT JOIN product_images AS img ON o.option_id = img.option_id AND img.display_order = 1
            GROUP BY p.product_id
            LIMIT 3";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $ranking_products = $stmt->fetchAll();

    /*
       3. ☀️ 夏対策特集（category_id = 69）から3つ取得
    */
    $sql_summer = "SELECT 
                        p.product_id, 
                        p.product_name, 
                        p.information,
                        o.option_id,
                        o.price,
                        i.image_url
                   FROM product AS p
                   INNER JOIN product_attributes_options AS o ON p.product_id = o.variation_id
                   LEFT JOIN product_images AS i ON o.option_id = i.option_id
                   WHERE p.category_id = 69
                   GROUP BY p.product_id
                   ORDER BY RAND()
                   LIMIT 3";
                   
    $stmt_summer = $pdo->query($sql_summer);
    $summer_products = $stmt_summer->fetchAll();

} catch (PDOException $e) {
    exit('データ取得に失敗しました: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yahoo!ショッピング風 - オンラインショップ</title>
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

            <div class="slider-wrapper">
                <div class="slider-container" id="slider">
                    <div class="slide-item slide-blue">
                        <div class="slide-content">
                            <span class="slide-badge">5のつく日・25の日</span>
                            <h2>5のつく日はポイント爆祭り！</h2>
                            <p>エントリーするだけでPayPayポイントがどんどん貯まる大チャンス！</p>
                            <a href="#" class="slide-btn">今すぐエントリー</a>
                        </div>
                    </div>
                    <div class="slide-item slide-gold">
                        <div class="slide-content">
                            <span class="slide-badge badge-orange">先着順！本日使える</span>
                            <h2>人気の美容家電・イヤホンもおトク！</h2>
                            <p>対象ストアで使える最大1,000円OFFクーポン配布中！</p>
                            <a href="#" class="slide-btn btn-dark">クーポンを獲得</a>
                        </div>
                    </div>
                    <div class="slide-item slide-green">
                        <div class="slide-content">
                            <span class="slide-badge badge-green">新規入会特典</span>
                            <h2>PayPayカード入会でポイント進呈！</h2>
                            <p>いつものお買い物がずーっとおトクになる魔法のカードを入手しよ！</p>
                            <a href="#" class="slide-btn">詳細を見てみる</a>
                        </div>
                    </div>
                </div>
                <button class="slide-arrow arrow-left" id="prevBtn" aria-label="前へ">&lt;</button>
                <button class="slide-arrow arrow-right" id="nextBtn" aria-label="次へ">&gt;</button>
            </div>

            <div class="ys-main-layout">

                <aside class="ys-sidebar">
                    <div class="sidebar-box">
                        <h3 class="sidebar-title">カテゴリから探す</h3>
                        <ul class="sidebar-menu-list">
                            <li><a href="product-detail.php"><span>レディースファッション</span><span class="arrow">＞</span></a></li>
                            <li><a href="#"><span>メンズファッション</span><span class="arrow">＞</span></a></li>
                            <li><a href="#"><span>腕時計、アクセサリー</span><span class="arrow">＞</span></a></li>
                            <li><a href="#"><span>ベビー、キッズ、マタニティ</span><span class="arrow">＞</span></a></li>
                            <li><a href="category.html"><span>食品</span><span class="arrow">＞</span></a></li>
                            <li><a href="#"><span>ドリンク、水、お酒</span><span class="arrow">＞</span></a></li>
                            <li><a href="#" class="more-link">さらに表示する</a></li>
                        </ul>
                    </div>
                </aside>

                <div class="ys-content-area">

                    <!-- 売れ筋人気ランキング -->
                    <section class="ranking-section">
                        <div class="section-header-row">
                            <h2 class="main-section-title">売れ筋人気ランキング</h2>
                            <a href="ranking.html" class="view-all-link">すべて見る →</a>
                        </div>

                        <div class="product-grid-3" id="ranking-container">
                            <?php 
                            $rank = 1; 
                            foreach ($ranking_products as $product): 
                                $imgPath = $product['image_url'] ?? '';
                                if (!empty($imgPath)) {
                                    $imgPath = preg_replace('/^localhost\/(Yahooshopping\/)?/i', '', $imgPath);
                                }
                            ?>
                                <div class="product-card rank-<?= $rank ?>">
                                    <div class="rank-badge"><?= $rank ?></div>
                                    <a href="product-detail.php?option_id=<?= $product['option_id'] ?>" class="product-img-wrap">
                                        <div class="product-img" style="height: 180px; display: flex; align-items: center; justify-content: center; padding: 10px;">
                                            <?php if (!empty($imgPath)): ?>
                                                <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                            <?php else: ?>
                                                🎁
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                    <div class="product-info">
                                        <h3 class="product-name">
                                            <a href="product-detail.php?option_id=<?= $product['option_id'] ?>" style="text-decoration: none; color: inherit;">
                                                <?= htmlspecialchars($product['product_name']) ?>
                                            </a>
                                        </h3>
                                        <p style="font-size: 0.8rem; color: #666; margin: 4px 0; line-height: 1.4;">
                                            <?= htmlspecialchars($product['information']) ?>
                                        </p>
                                        <div class="product-rating">
                                            <span class="stars">★★★★★</span>
                                        </div>
                                        <div class="product-price-row">
                                            <span class="product-price">¥<?= number_format($product['price']) ?> <span class="tax">税込</span></span>
                                            <button class="btn-cart-add" onclick="location.href='cart.html'" aria-label="カートに追加">🛒</button>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                            $rank++; 
                            endforeach; 
                            ?>
                        </div>
                    </section>

                    <!-- 🌴 夏対策特集セクション (category_id = 69) 🌊 -->
                    <section style="margin-top: 35px; background: linear-gradient(135deg, #e0f7fa 0%, #fff9c4 100%); padding: 25px; border-radius: 12px; border: 2px solid #4dd0e1; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                        
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 2px dashed #00acc1; padding-bottom: 10px;">
                            <h2 style="font-size: 1.3rem; font-weight: bold; color: #00838f; margin: 0; display: flex; align-items: center; gap: 8px;">
                                <span>☀️ 暑い夏を乗り切る！夏対策特集 🧊</span>
                            </h2>
                            <span style="font-size: 0.8rem; background: #ffb74d; color: #fff; padding: 4px 12px; border-radius: 20px; font-weight: bold;">おすすめ3選</span>
                        </div>

                        <?php if (!empty($summer_products)): ?>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                                <?php foreach ($summer_products as $item): ?>
                                    <?php 
                                        $summerImg = !empty($item['image_url']) ? preg_replace('/^localhost\/(Yahooshopping\/)?/i', '', $item['image_url']) : '';
                                    ?>
                                    <a href="product-detail.php?option_id=<?= $item['option_id'] ?>" style="text-decoration: none; color: inherit; display: block;">
                                        <div style="background: #fff; border-radius: 10px; padding: 15px; height: 100%; border: 1px solid #b2ebf2; transition: transform 0.2s, box-shadow 0.2s; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 15px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                            
                                            <div>
                                                <div style="width: 100%; height: 160px; display: flex; align-items: center; justify-content: center; background: #fafafa; border-radius: 6px; overflow: hidden; margin-bottom: 12px;">
                                                    <?php if (!empty($summerImg)): ?>
                                                        <img src="<?= htmlspecialchars($summerImg) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                    <?php else: ?>
                                                        <div style="font-size: 2.5rem;">🏖️</div>
                                                    <?php endif; ?>
                                                </div>

                                                <h3 style="font-size: 0.95rem; font-weight: bold; margin: 0 0 6px 0; color: #333; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                    <?= htmlspecialchars($item['product_name']) ?>
                                                </h3>
                                            </div>

                                            <div style="margin-top: 10px; border-top: 1px solid #f0f0f0; padding-top: 8px;">
                                                <span style="font-size: 1.1rem; font-weight: bold; color: #e53935;">
                                                    ¥<?= number_format($item['price']) ?>
                                                </span>
                                                <span style="font-size: 0.75rem; color: #777;"> (税込)</span>
                                            </div>

                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p style="text-align: center; color: #666; font-size: 0.9rem; margin: 20px 0;">現在、夏対策のおすすめ商品は準備中です！</p>
                        <?php endif; ?>

                    </section>

                </div>
            </div>
        </div>
    </main>

    <script>
        // スライダー制御
        const slider = document.getElementById('slider');
        const slides = document.querySelectorAll('.slide-item');
        let currentIndex = 0;

        function goToSlide(index) {
            currentIndex = (index + slides.length) % slides.length;
            slider.style.transform = `translateX(-${currentIndex * 100}%)`;
        }

        document.getElementById('nextBtn').addEventListener('click', () => goToSlide(currentIndex + 1));
        document.getElementById('prevBtn').addEventListener('click', () => goToSlide(currentIndex - 1));

        setInterval(() => goToSlide(currentIndex + 1), 5000);
    </script>
</body>
</html>