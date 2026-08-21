<?php 
    //ヘッダーに表示する件数をBeansから取得（カート、お気に入りも）

    /* インポート */
    require_once('Beans.php');
    require_once('utilConnDB.php');

    session_start();

    $Beans = new Beans();
    /* データを受け取る */
    $category_accessories_List = array();
    $favoriteList = array();
    if (isset($_SESSION['favoriteList'])) {
        $favoriteList = $_SESSION['favoriteList'];
    }    
    if (isset($_SESSION['category_accessories_List'])) {
    $category_accessories_List = $_SESSION['category_accessories_List'];
    }

    //お気に入りの件数
    $count = count($favoriteList);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アクセサリーカテゴリ - HCS!ショッピング</title>
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



    <div class="category-banner-wrapper" style="width: 100%; height: 200px; background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat; display: flex; align-items: center; justify-content: flex-start;">
        <div class="container">
            <h1 style="color: #fff; font-size: 2rem; font-weight: 700; margin-left: 20px; text-shadow: 1px 1px 5px rgba(0,0,0,0.5);">アクセサリー</h1>
        </div>
    </div>

    <main class="main-bg-gray" style="padding-top: 20px;">
        <div class="container">
            <div class="ys-main-layout">

                <aside class="ys-sidebar" style="display: flex; flex-direction: column; gap: 20px;">

                    <!-- indexからカテゴリの親IDを受け取って、そこから深掘りするカテゴリを分けていく -->
                    <div class="sidebar-box">
                        <h3 class="sidebar-title">カテゴリ</h3>
                        <ul class="sidebar-menu-list">
                        <li><a href="category_accessories_necklace_Lstmain.php"><span>ネックレス</span><span class="arrow">></span></a></li>
                        <li><a href="category_accessories_ring_Lstmain.php"><span>指輪</span><span class="arrow">></span></a></li>
                        <li><a href="category_accessories_earrings_Lstmain.php"><span>ピアス</span><span class="arrow">></span></a></li>
                        </ul>
                    </div>
                </aside>

                <section class="ranking-section">
                <!-- ここから商品の表示をＤＢから行う -->
                <div class="product-grid-3" id="ranking-container">
                    <?php foreach ($category_accessories_List as $Beans): ?>
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
    </main>

    <footer class="footer" style="padding-top: 30px;">
        <div class="container footer-inner" style="display: flex; flex-direction: column; gap: 30px;">
            <div class="footer-top-links" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; padding-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <div class="footer-app-qr" style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 50px; height: 50px; background: #ccc; display: flex; align-items: center; justify-content: center; font-size: 0.6rem; font-weight: bold; color: #333; border: 1px solid #aaa;">QRコード</div>
                    <div>
                        <p style="font-size: 0.85rem; font-weight: bold; margin: 0;">ショッピングアプリでお得にお買い物</p>
                        <p style="font-size: 0.75rem; color: #666; margin: 2px 0 0 0;">App Store / Google Play で配信中</p>
                    </div>
                </div>
                <div class="footer-sns" style="display: flex; gap: 15px;">
                    <a href="#" style="text-decoration: none; font-size: 0.85rem; color: #06C755; font-weight: bold;">💬 LINE公式アカウント</a>
                    <a href="#" style="text-decoration: none; font-size: 0.85rem; color: #65BBE9; font-weight: bold;">Twitter公式アカウント</a>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
                <div class="footer-col">
                    <p class="footer-logo">Yahoo!ショッピング風サイト</p>
                    <p class="footer-tagline">毎日の生活をもっと豊かに、おトクに。</p>
                </div>
                <div class="footer-col">
                    <h4 class="footer-heading">各種規約・ガイドライン</h4>
                    <ul class="footer-links">
                        <li><a href="#">ヤフーショッピングを利用するにあたってのガイドライン</a></li>
                        <li><a href="#">プライバシーセンター</a></li>
                        <li><a href="#">利用規約</a></li>
                        <li><a href="#">免責事項</a></li>
                    </ul>
                </div>
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

</body>
</html>