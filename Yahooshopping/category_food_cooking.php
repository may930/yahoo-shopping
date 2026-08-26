<?php 
    //ヘッダーに表示する件数をBeansから取得（カート、お気に入りも）

    /* インポート */
    require_once('Beans.php');
    require_once('utilConnDB.php');

    session_start();

    $Beans = new Beans();
    /* データを受け取る */
    $category_cooking_List = array();
    $favoriteList = array();
    if (isset($_SESSION['favoriteList'])) {
        $favoriteList = $_SESSION['favoriteList'];
    }    
    if (isset($_SESSION['category_cooking_List'])) {
    $category_cooking_List = $_SESSION['category_cooking_List'];
    }

    //お気に入りの件数
    $count = count($favoriteList);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>惣菜、料理カテゴリ - HCS!ショッピング</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>


    <div class="category-banner-wrapper" style="width: 100%; height: 200px; background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat; display: flex; align-items: center; justify-content: flex-start;">
        <div class="container">
            <h1 style="color: category_food_Lstmain.phpfff; font-size: 2rem; font-weight: 700; margin-left: 20px; text-shadow: 1px 1px 5px rgba(0,0,0,0.5);">惣菜、料理</h1>
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
                            <li><a href="category_food_Lstmain.php"><span>すべて</span><span class="arrow">></span></a></li>
                            <li><a href="category_food_grain_Lstmain.php"><span>米、雑穀、粉類</span><span class="arrow">></span></a></li>
                            <li><a href="category_food_sweets_Lstmain.php"><span>スイーツ、洋菓子</span><span class="arrow">></span></a></li>
                            <li><a href="category_food_seafood_Lstmain.php"><span>魚介類、海産物</span><span class="arrow">></span></a></li>
                            <li><a href="category_food_meat_Lstmain.php"><span>肉、ハム、ソーセージ</span><span class="arrow">></span></a></li>
                        </ul>
                    </div>
                </aside>

                <section class="ranking-section">
                <!-- ここから商品の表示をＤＢから行う -->
                <div class="product-grid-3" id="ranking-container">
                    <?php foreach ($category_cooking_List as $Beans): ?>
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
            <div class="footer-top-links" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid category_food_Lstmain.phpddd; padding-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <div class="footer-app-qr" style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 50px; height: 50px; background: category_food_Lstmain.phpccc; display: flex; align-items: center; justify-content: center; font-size: 0.6rem; font-weight: bold; color: category_food_Lstmain.php333; border: 1px solid category_food_Lstmain.phpaaa;">QRコード</div>
                    <div>
                        <p style="font-size: 0.85rem; font-weight: bold; margin: 0;">ショッピングアプリでお得にお買い物</p>
                        <p style="font-size: 0.75rem; color: category_food_Lstmain.php666; margin: 2px 0 0 0;">App Store / Google Play で配信中</p>
                    </div>
                </div>
                <div class="footer-sns" style="display: flex; gap: 15px;">
                    <a href="category_food_Lstmain.php" style="text-decoration: none; font-size: 0.85rem; color: category_food_Lstmain.php06C755; font-weight: bold;">💬 LINE公式アカウント</a>
                    <a href="category_food_Lstmain.php" style="text-decoration: none; font-size: 0.85rem; color: category_food_Lstmain.php65BBE9; font-weight: bold;">Twitter公式アカウント</a>
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
                        <li><a href="category_food_Lstmain.php">ヤフーショッピングを利用するにあたってのガイドライン</a></li>
                        <li><a href="category_food_Lstmain.php">プライバシーセンター</a></li>
                        <li><a href="category_food_Lstmain.php">利用規約</a></li>
                        <li><a href="category_food_Lstmain.php">免責事項</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4 class="footer-heading">サポート・企業情報</h4>
                    <ul class="footer-links">
                        <li><a href="category_food_Lstmain.php">ヘルプ・お問い合わせ</a></li>
                        <li><a href="category_food_Lstmain.php">ご意見・ご要望</a></li>
                        <li><a href="category_food_Lstmain.php">採用情報</a></li>
                        <li><a href="edit-profile.html">会社概要・チーム紹介</a></li>
                        <li><a href="category_food_Lstmain.php">プライバシーポリシー</a></li>
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