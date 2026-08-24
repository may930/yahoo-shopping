<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登録完了 - Yahoo!ショッピング風（チーム制作サンプル）</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
</head>
<body class="register-body">

<?php include 'header.php'; ?>

    <main class="register-container">
        <div class="complete-box">

            <div class="success-icon-box">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>

            <h1 class="complete-title">会員登録が完了しました！</h1>
            <p class="complete-text">
                ご登録ありがとうございます。<br>
                これよりYahoo!ショッピング風サイトでのお買い物をお楽しみいただけます！
            </p>

            <div class="redirect-notice">
                <span id="countdown" class="countdown-num">3</span> 秒後に自動的にトップページへ移動します。
            </div>

            <br>
            <a href="index.php" class="back-home-btn">トップページへ戻る</a>
        </div>
    </main>

    <footer class="footer" style="margin-top: auto;">
        <div class="footer-bottom">
            <p>© 2026 Yahoo!ショッピング風チーム制作プロジェクト. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // 💡 3秒後に自動遷移するカウントダウンタイマーばい！
        let count = 3;
        const countdownElement = document.getElementById('countdown');

        const timer = setInterval(() => {
            count--;
            countdownElement.textContent = count;

            if (count <= 0) {
                clearInterval(timer);
                window.location.href = "index.php"; // ここでトップに飛ばすばい！
            }
        }, 1000);
    </script>
</body>
</html>