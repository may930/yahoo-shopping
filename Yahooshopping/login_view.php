<?php
// 1. セッション開始（共通ヘッダーでログイン状態を判定するために必要！）
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - Yahoo!ショッピング風</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="register-body">

    <!-- ⚡️ ここを共通ヘッダーに置き換え！ -->
    <?php include 'header.php'; ?>

    <main class="register-container">
        <div class="register-box">
            <h1 class="register-title">ログイン</h1>
            <p class="register-subtitle">登録している携帯電話番号またはメールアドレスとパスワードを入力してください。</p>

            <form id="loginForm" action="login.php" class="form-grid" method="post">

                <div class="form-group">
                    <label for="loginIdentifier" class="form-label">携帯電話番号(ハイフンなし) または メールアドレス</label>
                    <input type="text" name="login_info" id="loginIdentifier" class="form-input" placeholder="(例) example@yahoo.co.jp" required>
                </div>

                <!-- 🔑 パスワード入力欄 -->
                <div class="form-group" style="margin-top: 16px;">
                    <label for="loginPassword" class="form-label">パスワード</label>
                    <input type="password" name="password" id="loginPassword" class="form-input" placeholder="パスワードを入力" required>

                    <!-- エラーメッセージ表示エリア -->
                    <div id="error-message" style="color: #ff3333; font-size: 0.85rem; margin-top: 8px; font-weight: bold;"></div>
                </div>

                <button type="submit" name="cmdBtn1" class="submit-btn" style="margin-top: 20px;">ログイン</button>
            </form>

            <div style="text-align: center; margin-top: 24px; font-size: 0.85rem;">
                <p style="color: #666;">アカウントをお持ちでないですか？</p>
                <a href="register.html" style="color: var(--color-link); text-decoration: none; font-weight: 700; display: inline-block; margin-top: 4px;">新規会員登録はこちら</a>
            </div>
        </div>
    </main>

    <footer class="footer" style="margin-top: auto;">
        <div class="footer-bottom">
            <p>© 2026 Yahoo!ショッピング風チーム制作プロジェクト. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // URLパラメータを取得してエラー表示を制御
        const urlParams = new URLSearchParams(window.location.search);
        const errorDiv = document.getElementById('error-message');
        
        if (urlParams.get('error') === 'auth') {
            errorDiv.textContent = 'ログインIDまたはパスワードが正しくありません。';
        } else if (urlParams.get('error') === 'empty') {
            errorDiv.textContent = 'すべての項目を入力してください。';
        }
    </script>
</body>
</html>