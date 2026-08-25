<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員情報変更 - Yahoo!ショッピング風（チーム制作サンプル）</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="main-bg-gray">
        <div class="container">
            <div class="breadcrumb" style="margin-bottom: 20px;">
                <a href="index.php">トップ</a> ＞ <a href="mypage.php">マイページ</a> ＞ <span>会員情報変更</span>
            </div>

            <div class="ys-main-layout">

                <aside class="ys-sidebar">
                    <div class="sidebar-box">
                        <h3 class="sidebar-title" style="color: #ff5a00; font-weight: 700;">マイページメニュー</h3>
                        <ul class="sidebar-menu-list">
                            <li><a href="mypage.php"><span>マイページトップ</span></a></li>
                            <li><a href="edit-profile.php" style="font-weight: 700; color: #ff5a00; background: #fff5f0;"><span>定額確認・会員情報変更</span></a></li>
                            <li><a href="favorites.php"><span>お気に入り商品</span><span class="arrow">＞</span></a></li>
                            <li><a href="support-top.php"><span>お問合せ</span><span class="arrow">＞</span></a></li>
                            <li><a href="coupons.php"><span>クーポン一覧</span><span class="arrow">＞</span></a></li>
                        </ul>
                    </div>
                </aside>

                <div class="ys-content-area">
                    <form id="profileForm" onsubmit="return handleSave(event)">
                        <div class="profile-grid" style="grid-template-columns: 1fr; gap: 24px;">

                            <div class="profile-card" style="box-shadow: var(--shadow-card); padding: 24px;">
                                <div class="card-header-row" style="border-bottom: 2px solid #ff5a00; padding-bottom: 12px; margin-bottom: 20px;">
                                    <h2 style="font-size: 1.2rem; font-weight: 700; color: #111;">📝 会員情報の変更・修正</h2>
                                </div>

                                <div style="display: flex; flex-direction: column; gap: 20px;">
                                    <div class="card-body-field">
                                        <label style="font-size: 0.9rem; font-weight: 700; color: #333;">ユーザー名 <span style="color:#ff3b30; font-size:0.75rem;">(必須)</span></label>
                                        <input type="text" id="usernameInput" value="サンプル" style="max-width: 400px; padding: 12px;" placeholder="ユーザー名を入力してください">
                                        <span class="error-message" id="usernameError">ユーザー名は必須です</span>
                                    </div>

                                    <div class="card-body-field">
                                        <label style="font-size: 0.9rem; font-weight: 700; color: #333;">メールアドレス <span style="color:#ff3b30; font-size:0.75rem;">(必須)</span></label>
                                        <input type="email" id="emailInput" value="sample@example.com" style="max-width: 400px; padding: 12px;" placeholder="example@example.com">
                                        <span class="error-message" id="emailError">正しいメールアドレスを入力してください</span>
                                    </div>

                                    <div class="card-body-field">
                                        <label style="font-size: 0.9rem; font-weight: 700; color: #333;">郵便番号</label>
                                        <input type="text" id="zipInput" value="060-0001" style="max-width: 200px; padding: 12px;" placeholder="123-4567">
                                        <span class="error-message" id="zipError">郵便番号の形式が正しくありません (例: 123-4567)</span>
                                    </div>

                                    <div class="card-body-field">
                                        <label style="font-size: 0.9rem; font-weight: 700; color: #333;">お届け先住所</label>
                                        <input type="text" id="addressInput" value="北海道札幌市中央区北1条西..." style="padding: 12px;" placeholder="市区町村、番地、建物名など">
                                        <span class="error-message" id="addressError">住所を入力してください</span>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div style="display: flex; gap: 16px; justify-content: center; margin-top: 30px;">
                            <button type="button" class="profile-save-btn" style="background: #e4e7ec; color: #333;" onclick="location.href='mypage.html'">キャンセル</button>
                            <button type="submit" class="profile-save-btn" style="background: #ff5a00;">変更内容を保存する</button>
                        </div>
                        <div style="text-align: center;">
                            <p id="successMessage" style="color: #1a6e3a; font-weight: 700; margin-top: 16px; display: none;">✓ 会員情報を正常に保存しました！マイページに戻ります...</p>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container footer-inner">
            <div class="footer-col">
                <p class="footer-logo">Yahoo!ショッピング風サイト</p>
                <p class="footer-tagline">毎日の生活をもっと豊かに、おトクに。</p>
            </div>
            <div class="footer-col">
                <h4 class="footer-heading">運営チーム</h4>
                <ul class="footer-links">
                    <li><a href="#">会社概要・チーム紹介</a></li>
                    <li><a href="#">プライバシーポリシー</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 Yahoo!ショッピング風チーム制作プロジェクト. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function handleSave(event) {
            event.preventDefault();
            let hasError = false;

            const username = document.getElementById('usernameInput');
            const email = document.getElementById('emailInput');
            const zip = document.getElementById('zipInput');
            const address = document.getElementById('addressInput');

            const usernameError = document.getElementById('usernameError');
            const emailError = document.getElementById('emailError');
            const zipError = document.getElementById('zipError');
            const addressError = document.getElementById('addressError');
            const successMessage = document.getElementById('successMessage');

            [username, email, zip, address].forEach(input => input.classList.remove('input-error-border'));
            [usernameError, emailError, zipError, addressError].forEach(err => err.style.display = 'none');
            successMessage.style.display = 'none';

            if (username.value.trim() === '') {
                username.classList.add('input-error-border');
                usernameError.style.display = 'block';
                hasError = true;
            }
            if (!email.value.includes('@')) {
                email.classList.add('input-error-border');
                emailError.style.display = 'block';
                hasError = true;
            }
            if (zip.value.trim() !== '' && !zip.value.match(/^\d{3}-\d{4}$/)) {
                zip.classList.add('input-error-border');
                zipError.style.display = 'block';
                hasError = true;
            }
            if (address.value.trim() === '') {
                address.classList.add('input-error-border');
                addressError.style.display = 'block';
                hasError = true;
            }

            if (!hasError) {
                successMessage.style.display = 'block';
                setTimeout(() => {
                    location.href = 'mypage.php'; // 保存に成功したら自動でマイページに戻るばい！
                }, 2000);
            }
            return false;
        }
    </script>
</body>
</html>