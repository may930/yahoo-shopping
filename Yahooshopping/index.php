<?php 
    //ヘッダーに表示する件数をBeansから取得（カート、お気に入りも）

    /* インポート */
    require_once('Beans.php');
    $Beans = new Beans();
    /* データを受け取る */
    session_start();
    $favoriteList = array();
    $toppageList = array();
    $toppage_summer_List = array();
    $toppage_food_List = array();

    if (isset($_SESSION['favoriteList'])) {
    $favoriteList = $_SESSION['favoriteList'];
    }
    if (isset($_SESSION['toppageList'])) {
    $toppageList = $_SESSION['toppageList'];
    }
    if (isset($_SESSION['toppage_summer_List'])) {
        $toppage_summer_List = $_SESSION['toppage_summer_List'];
    }
    if (isset($_SESSION['toppage_food_List'])) {
        $toppage_food_List = $_SESSION['toppage_food_List'];
    }

    //お気に入りの件数
    $count = count($favoriteList);
    //ログイン状態のアカウントのユーザー名を取得
    $userName = $_SESSION['user']['name'] ?? '';
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
                    <!-- ログイン情報を取得して、～さんって表示する -->
                    <span class="welcome-text">ようこそ、<strong><?php echo $userName; ?></strong> さん</span>
                </nav>
            </div>
        </div>

        <div class="header-main">
            <div class="container header-main-inner">
                <a href="index_Lstmain.php" class="logo">
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

                    <!-- 表示する件数をDBから参照する -->
                    <a href="favorites_Lstmain.php" class="action-item-btn">
                        <span class="action-icon">❤</span>
                        <span class="action-label">お気に入り</span>
                        <span class="cart-count"><?php echo $count; ?></span>
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

            <!-- 広告的な部分 -->
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

                    <section class="ranking-section">
                        <div class="section-header-row">
                            <h2 class="main-section-title">売れ筋人気ランキング</h2>
                            <a href="ranking.html" class="view-all-link">すべて見る →</a>
                        </div>
                        <!-- ここから商品の表示をＤＢから行う -->
                        <div class="product-grid-3" id="ranking-container">
                            <?php foreach ($toppageList as $Beans): ?>
                                <?php 
                                    // データベースから取得した安全な値を変数にセット
                                    $productId   = $Beans->getproduct_id();
                                    $productName = htmlspecialchars($Beans->getproduct_name(), ENT_QUOTES, 'UTF-8');
                                    $price       = number_format($Beans->getprice());
                                    $image       = htmlspecialchars($Beans->getimage_url(), ENT_QUOTES, 'UTF-8');
                                ?>

                                <!-- ★ 1個分のカードテンプレート（これがループで自動増殖します） -->
                                <div class="product-card" id="<?php echo $productId; ?>">
                                    

                                    <!-- 商品画像 -->
                                    <a href="product-detail.php"><img src="<?php echo $image; ?>" alt="<?php echo $productName; ?>" class="product-img"></a>
                                    
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
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <section class="ranking-section" style="margin-top: 40px;">
                        <div class="section-header-row" style="border-bottom: 2px solid #00a0e9; padding-bottom: 6px;">
                            <h2 class="main-section-title" id="seasonal-title" style="color: var(--color-black);">☀️ 今年の夏を快適に！役立つ夏グッズ特集</h2>
                            <div style="font-size: 0.8rem; color: #666;">季節で切り替え可能</div>
                        </div>

                        <div class="product-grid-3" id="seasonal-products" style="margin-top: 16px;">
                            <?php foreach ($toppage_summer_List as $Beans): ?>
                                    <?php 
                                        // データベースから取得した安全な値を変数にセット
                                        $productId   = $Beans->getproduct_id();
                                        $productName = htmlspecialchars($Beans->getproduct_name(), ENT_QUOTES, 'UTF-8');
                                        $price       = number_format($Beans->getprice());
                                        $image       = htmlspecialchars($Beans->getimage_url(), ENT_QUOTES, 'UTF-8');
                                    ?>

                                    <!-- ★ 1個分のカードテンプレート（これがループで自動増殖します） -->
                                    <div class="product-card" id="<?php echo $productId; ?>">
                                        
                                        <!-- 商品画像 -->
                                        <a href="product-detail.php"><img src="<?php echo $image; ?>" alt="<?php echo $productName; ?>" class="product-img"></a>
                                        
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
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>


                    <section class="ranking-section" style="margin-top: 40px; margin-bottom: 20px;">
                        <div class="section-header-row" style="border-bottom: 2px solid #1a6e3a; padding-bottom: 6px;">
                            <h2 class="main-section-title" style="color: var(--color-black);">🍖 産地直送！お取り寄せグルメ特集</h2>
                            <a href="#" class="view-all-link" style="color: #1a6e3a;">贅沢グルメをもっと見る →</a>
                        </div>

                        <div class="product-grid-3" id="seasonal-products" style="margin-top: 16px;">
                            <?php foreach ($toppage_food_List as $Beans): ?>
                                    <?php 
                                        // データベースから取得した安全な値を変数にセット
                                        $productId   = $Beans->getproduct_id();
                                        $productName = htmlspecialchars($Beans->getproduct_name(), ENT_QUOTES, 'UTF-8');
                                        $price       = number_format($Beans->getprice());
                                        $image       = htmlspecialchars($Beans->getimage_url(), ENT_QUOTES, 'UTF-8');
                                    ?>

                                    <!-- ★ 1個分のカードテンプレート（これがループで自動増殖します） -->
                                    <div class="product-card" id="<?php echo $productId; ?>">
                                        
                                        <!-- 商品画像 -->
                                        <a href="product-detail.php"><img src="<?php echo $image; ?>" alt="<?php echo $productName; ?>" class="product-img"></a>
                                        
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
                                    </div>
                                 </div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </main>

    <footer class="footer" style="padding-top: 30px;">
        <div class="container footer-inner" style="display: flex; flex-direction: column; gap: 30px;">

            <!-- 【追加】上段：アプリDL（QR）とSNS公式アカウントのエリア -->
            <div class="footer-top-links" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; padding-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <!-- App Store、Google Playへのサイト遷移が可能なQRを表示 -->
                <div class="footer-app-qr" style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 50px; height: 50px; background: #ccc; display: flex; align-items: center; justify-content: center; font-size: 0.6rem; font-weight: bold; color: #333; border: 1px solid #aaa;">QRコード</div>
                    <div>
                        <p style="font-size: 0.85rem; font-weight: bold; margin: 0;">ショッピングアプリでお得にお買い物</p>
                        <p style="font-size: 0.75rem; color: #666; margin: 2px 0 0 0;">App Store / Google Play で配信中</p>
                    </div>
                </div>
                <!-- LINEのトップ画面、X(旧Twitter)アカウントに遷移可能 -->
                <div class="footer-sns" style="display: flex; gap: 15px;">
                    <a href="#" style="text-decoration: none; font-size: 0.85rem; color: #06C755; font-weight: bold;">💬 LINE公式アカウント</a>
                    <a href="#" style="text-decoration: none; font-size: 0.85rem; color: #65BBE9; font-weight: bold;">Twitter公式アカウント</a>
                </div>
            </div>

            <!-- 中段：リンクの複数カラム -->
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
                <div class="footer-col">
                    <p class="footer-logo">Yahoo!ショッピング風サイト</p>
                    <p class="footer-tagline">毎日の生活をもっと豊かに、おトクに。</p>
                </div>

                <!-- 【追加・整理】ガイドライン・規約・ポリシー関係 -->
                <div class="footer-col">
                    <h4 class="footer-heading">各種規約・ガイドライン</h4>
                    <ul class="footer-links">
                        <li><a href="#">ヤフーショッピングを利用するにあたってのガイドライン</a></li>
                        <li><a href="#">プライバシーセンター</a></li>
                        <li><a href="#">利用規約</a></li>
                        <li><a href="#">免責事項</a></li>
                    </ul>
                </div>

                <!-- 【追加・整理】サポート・会社関係 -->
                <div class="footer-col">
                    <h4 class="footer-heading">サポート・企業情報</h4>
                    <ul class="footer-links">
                        <li><a href="#">ヘルプ・お問い合わせ</a></li>
                        <li><a href="#">ご意見・ご要望</a></li>
                        <li><a href="#">採用情報</a></li>
                        <li><a href="edit-profile.html">会社概要・チーム紹介</a></li>
                        <li><a href="#">プライバシーポリシー</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="footer-bottom" style="margin-top: 30px;">
            <p>© 2026 Yahoo!ショッピング風チーム制作プロジェクト. All rights reserved.</p>
        </div>
    </footer>

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

        // 5秒ごとに自動スライド
        setInterval(() => goToSlide(currentIndex + 1), 5000);
    </script>

</body>
</html>
