<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>出品者管理ツール - TOP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* 管理画面専用のスタイル */
        .admin-body {
            background-color: #f4f6f8;
            font-family: 'Noto Sans JP', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .admin-header {
            background-color: #2c3e50;
            color: #fff;
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .admin-header-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-logo {
            font-size: 1.2rem;
            font-weight: bold;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-logo span {
            background-color: #ff0033;
            color: #fff;
            font-size: 0.75rem;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .admin-user-info {
            font-size: 0.9rem;
            color: #ecf0f1;
        }

        .admin-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
            flex: 1;
        }

        .welcome-box {
            background: #fff;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            border-left: 5px solid #0056b3;
        }

        .welcome-box h1 {
            font-size: 1.4rem;
            margin: 0 0 8px 0;
            color: #333;
        }

        .welcome-box p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }

        /* 4つのメニューカードグリッド */
        .admin-menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .admin-menu-card {
            background: #fff;
            border: 1px solid #e0e6ed;
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .admin-menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            border-color: #0056b3;
        }

        .card-icon {
            font-size: 2.8rem;
            margin-bottom: 15px;
            line-height: 1;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 8px;
            color: #2c3e50;
        }

        .card-desc {
            font-size: 0.8rem;
            color: #7f8c8d;
            line-height: 1.4;
        }

        /* 戻るボタンエリア */
        .site-back-link {
            text-align: center;
            margin-top: 40px;
        }

        .site-back-link a {
            color: #666;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .site-back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="admin-body">

    <!-- ヘッダー -->
    <header class="admin-header">
        <div class="container admin-header-inner">
            <a href="admin_top.php" class="admin-logo">
                HCS! ストアマネージャー <span>出品者専用</span>
            </a>
            <div class="admin-user-info">
                ログイン中: <strong>サントリー公式ストア</strong> 様
            </div>
        </div>
    </header>

    <!-- メインコンテンツ -->
    <div class="admin-container">
        
        <div class="welcome-box">
            <h1>ストア管理ダッシュボード</h1>
            <p>ご希望の操作メニューを選択してください。</p>
        </div>

        <!-- 4つの主要メニュー -->
        <div class="admin-menu-grid">
            
            <!-- 1. 注文管理 -->
            <a href="admin_orders.php" class="admin-menu-card">
                <div class="card-icon">📦</div>
                <div class="card-title">注文管理</div>
                <div class="card-desc">注文の確認・発送ステータスの更新を行います</div>
            </a>

            <!-- 2. 問い合わせ管理 -->
            <a href="admin_inquiries.php" class="admin-menu-card">
                <div class="card-icon">💬</div>
                <div class="card-title">問い合わせ管理</div>
                <div class="card-desc">お客様からの質問への回答・履歴管理を行います</div>
            </a>

            <!-- 3. 商品登録 -->
            <a href="admin_product_register.php" class="admin-menu-card">
                <div class="card-icon">📝</div>
                <div class="card-title">商品登録</div>
                <div class="card-desc">新しい商品の出品・情報や価格の編集を行います</div>
            </a>

            <!-- 4. ストア構築 -->
            <a href="admin_store_setting.php" class="admin-menu-card">
                <div class="card-icon">🎨</div>
                <div class="card-title">ストア構築</div>
                <div class="card-desc">店舗看板・バナー設置やショップ設定を行います</div>
            </a>

        </div>

        <div class="site-back-link">
            <a href="index.php">← ショッピングサイト（購入者画面）に戻る</a>
        </div>

    </div>

</body>
</html>