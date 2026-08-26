<?php 
    //ヘッダーに表示する件数をBeansから取得（カート、お気に入りも）

    /* インポート */
    require_once('Beans.php');
    require_once('utilConnDB.php');
    session_start();
    $Beans = new Beans();
    /* データを受け取る */
    $history_List = array();
    if (isset($_SESSION['history_List'])) {
    $history_List = $_SESSION['history_List'];
    }
    if (!isset($_SESSION['user'])) {
        header('Location: login_view.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>閲覧履歴 - Yahoo!ショッピング風</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="container" style="margin-top: 30px; margin-bottom: 60px; max-width: 900px;">

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0;">最近チェックした商品（閲覧履歴）</h1>
        </div>

        <div style="display: flex; flex-direction: column; gap: 16px;">

        <?php foreach ($history_List as $Beans): ?>
                <?php 
                    // データベースから取得した安全な値を変数にセット
                    $option_id   = $Beans->getoption_id();
                    $productName = htmlspecialchars($Beans->getproduct_name(), ENT_QUOTES, 'UTF-8');
                    $price       = number_format($Beans->getprice());
                    $image       = htmlspecialchars($Beans->getimage_url(), ENT_QUOTES, 'UTF-8');
                    $view_time       = htmlspecialchars($Beans->getview_time(), ENT_QUOTES, 'UTF-8');
                ?>
                <!-- ★ 1個分のカードテンプレート（これがループで自動増殖します） -->
                <div class="product-card" id="<?php echo $option_id; ?>" style="background: #fff; border: 1px solid #e4e7ec; border-radius: var(--radius-md); padding: 20px; display: flex; gap: 16px; align-items: flex-start; position: relative; box-shadow: var(--shadow-card);">
                    <!-- 商品画像 -->
                    <a href="product-detail.php?option_id=<?php echo $optionId; ?>"><img src="<?php echo $image; ?>" alt="<?php echo $productName; ?>" class="product-img"></a>
                    
                    <!-- 商品情報 -->
                    <div class="product-info">
                        <span style="display: block; font-size: 0.8rem; color: #999; margin-bottom: 4px;">閲覧日時: <?php echo $view_time; ?></span>

                        <h3 class="product-name"><?php echo $productName; ?></h3>
                        <div class="product-rating">
                            <span class="stars" style="color: #ffcc00;">★★★★★</span>
                            <span class="rating-count">1,590</span>
                        </div>
                        <div class="product-price">
                            ¥<?php echo $price; ?>
                        </div>
                    </div>
                </div>
             <?php endforeach; ?>
        </div>

    </main>

    <footer class="footer">
        <div class="footer-bottom">
            <p>© 2026 Yahoo!ショッピング風チーム制作プロジェクト. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>