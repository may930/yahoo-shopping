<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>個人情報登録 - Yahoo!ショッピング風（チーム制作サンプル）</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
   
</head>
<body class="register-body">

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

    <main class="register-container">
        <div class="register-box">
            <h1 class="register-title">お客様情報の登録</h1>
            <p class="register-subtitle">お買い物や配送に必要な情報を入力してください。</p>

            <form action="account_registration.php" method="post" class="form-grid">

                <div class="form-group">
                    <label class="form-label">お名前（漢字）<span class="badge-required">必須</span></label>
                    <div class="form-row-2col">
                        <input type="text" name="name_first" class="form-input" placeholder="姓" required>
                        <input type="text" name="name_second" class="form-input" placeholder="名" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">お名前（カナ）<span class="badge-required">必須</span></label>
                    <div class="form-row-2col">
                        <input type="text" name="name_kana_first" class="form-input" placeholder="セイ" required pattern="[\u30A1-\u30FC]+">
                        <input type="text" name="name_kana_second" class="form-input" placeholder="メイ" required pattern="[\u30A1-\u30FC]+">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">性別<span class="badge-optional">任意</span></label>
                    <div class="gender-group">
                        <label class="gender-label"><input type="radio" name="sex" value="male">男性</label>
                        <label class="gender-label"><input type="radio" name="sex" value="female">女性</label>
                        <label class="gender-label"><input type="radio" name="sex" value="other" checked>選択しない</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="userZip" class="form-label">郵便番号(ハイフン含む)<span class="badge-required">必須</span></label>
                    <div class="zip-wrapper">
                        <input type="text" name="zipcode" id="userZip" class="form-input" style="max-width: 200px;" placeholder="060-0001" required>
                        <button type="button" class="btn-sub-action" onclick="autoFillAddress()">住所を自動入力</button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="userAddress" class="form-label">ご住所<span class="badge-required">必須</span></label>
                    <input type="text" name="address" id="userAddress" class="form-input" placeholder="北海道札幌市中央区北1条西..." required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">パスワード<span class="badge-required">必須</span></label>
                    <input type="text" name="password" id="password" class="form-input"  required>
                </div>


                <div class="form-group">
                    <input type="hidden" name="phone_Number" value="<?php echo htmlspecialchars($_POST['phone_Number']); ?>">
                    <input type="hidden" name="mail_address" value="<?php echo htmlspecialchars($_POST['mail_address']); ?>">
                </div>

                <button type="submit" name="cmdBtn1" class="submit-btn">入力内容を登録する</button>
            </form>
        </div>
    </main>

    <footer class="footer" style="margin-top: auto;">
        <div class="footer-bottom">
            <p>© 2026 Yahoo!ショッピング風チーム制作プロジェクト. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // ボタンを押したらパッとサンプル住所が入る魔法のJS！
        function autoFillAddress() {
            const zipValue = document.getElementById('userZip').value.trim();
            if(zipValue !== "") {
                document.getElementById('userAddress').value = "北海道札幌市中央区北1条西（デモ自動入力住所）";
            } else {
                alert("まずは郵便番号を入力してみてね！");
            }
        }
    </script>
</body>
</html>