<?php
session_start();
require_once 'utilConnDB.php';
require_once 'Beans.php';
require_once 'mypage_SQL.php';
// エスケープ関数
if (!function_exists('h')) {
    function h($str) {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
}

// ログインIDの取得
$userId = $_SESSION['user_id'] ?? $_SESSION['user']['id'] ?? null;

// $beans の初期化とDBからのデータ取得
$beans = null;
if ($userId) {
    $db = new UtilConnDB();
    $pdo = $db->connect();
    
    $mypageSql = new MypageSQL();
    $beans = $mypageSql->selectById($pdo, $userId);
    
    $db->disconnect($pdo);
}

if (!$beans) {
    $beans = new Beans();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マイページ - Yahoo!ショッピング風</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <!-- プロフィールバナー（動的に名前とIDを表示） -->
    <div class="profile-banner-wrapper">
        <div class="container profile-banner-inner">
            <div class="profile-banner-icon">👤</div>
            <div>
                <h1 class="profile-banner-name"><?= h($beans->getname()) ?> さんのマイページ</h1>
                <p class="profile-banner-id">ユーザーID: <?= h($beans->getuser_id()) ?></p>
            </div>
        </div>
    </div>

    <main class="main-bg-gray">
        <div class="container">
            <div class="ys-main-layout">

                <aside class="ys-sidebar">
                    <div class="sidebar-box">
                        <h3 class="sidebar-title" style="color: var(--color-primary);">マイページメニュー</h3>
                        <ul class="sidebar-menu-list">
                            <li><a href="mypage.php" class="is-active"><span>マイページトップ</span></a></li>
                            <li><a href="edit-profile.php"><span>定額確認・会員情報変更</span><span class="arrow">＞</span></a></li>
                            <li><a href="favorites.php"><span>お気に入り商品</span><span class="arrow">＞</span></a></li>
                            <li><a href="support-top.php"><span>お問合せ</span><span class="arrow">＞</span></a></li>
                            <li><a href="#"><span>クーポン一覧</span><span class="arrow">＞</span></a></li>
                            <li style="margin-top: 15px; border-top: 1px dashed #ddd; padding-top: 10px;">
                                <a href="logout.php" class="sidebar-logout-btn" style="color: #ff3b30; font-weight: bold;">
                                    <span>🚪 ログアウト</span>
                                    <span class="arrow">＞</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </aside>

                <div class="ys-content-area" style="display: flex; flex-direction: column; gap: 20px;">

                    <!-- 会員登録情報の参照表示エリア -->
                    <div class="profile-card">
                        <div class="card-header-row">
                            <h3>👤 お客様の登録情報</h3>
                            <a href="edit-profile.php" class="card-header-link">詳細・変更 ＞</a>
                        </div>
                        <div class="info-grid">
                            <span class="info-label">ユーザー名</span>
                            <span class="info-value"><?= h($beans->getname()) ?> さん</span>

                            <span class="info-label">メールアドレス</span>
                            <span class="info-value normal"><?= h($beans->getmail_address()) ?></span>

                            <span class="info-label">お届け先住所</span>
                            <span class="info-value normal">
                                <?php if ($beans->getzipcode()): ?>
                                    〒<?= h($beans->getzipcode()) ?> <?= h($beans->getaddress()) ?>
                                <?php else: ?>
                                    未登録
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>

                    <div class="profile-grid">
                        <div class="profile-card">
                            <div class="card-header-row">
                                <h3>💳 お支払い方法・連携</h3>
                            </div>
                            <p style="font-size: 0.85rem; color: #555; margin-top: 8px;">
                                PayPay残高：<strong style="color: var(--color-primary);">未連携</strong>
                            </p>
                            <p style="font-size: 0.85rem; color: #555; margin-top: 4px;">登録済カード：なし</p>
                            <a href="#" class="card-header-link" style="display: inline-block; margin-top: 12px;">ウォレット管理を開く ＞</a>
                        </div>

                        <div class="profile-card card-highlight">
                            <div class="card-header-row">
                                <h3>🔥 お得なステータス</h3>
                            </div>
                            <p style="font-size: 0.85rem; color: #333; margin-top: 8px;">
                                現在のPayPayポイント付与率：
                                <strong style="font-size: 1.2rem; color: #ff3b30;">毎日5%</strong>
                            </p>
                            <a href="#" style="font-size: 0.8rem; color: var(--color-primary); text-decoration: none; display: inline-block; margin-top: 12px; font-weight: 700;">
                                特典内訳をチェックする ＞
                            </a>
                        </div>
                    </div>

                </div>
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
                        <li><a href="edit-profile.php">会社概要・チーム紹介</a></li>
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