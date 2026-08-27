<?php
session_start();
// 必要に応じてDB接続ファイルや共通ファイルを読み込んでね
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ストア（出品者）新規登録 - Yahoo!ショッピング風</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="register-body" style="background: #f4f6f8;">

<?php include 'header.php'; ?>

    <main class="register-container" style="max-width: 700px; margin: 40px auto; padding: 0 20px;">
        <div style="background: #ffffff; border: 1px solid #e4e7ec; border-radius: 8px; padding: 40px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
            
            <div style="margin-bottom: 30px; text-align: center;">
                <span style="background: #ff5a00; color: #fff; font-size: 0.75rem; font-weight: bold; padding: 4px 10px; border-radius: 4px;">出店者向け</span>
                <h1 style="font-size: 1.5rem; font-weight: 700; color: #333; margin-top: 10px;">ストア新規登録（出品者情報入力）</h1>
                <p style="font-size: 0.85rem; color: #666; margin-top: 5px;">HCS!ショッピングでストアを開設するための情報を入力してください。</p>
            </div>

            <!-- 登録処理を行うPHP（例: producer_register_process.php など）に飛ばしてね -->
            <form action="producer_register_process.php" method="post" style="display: flex; flex-direction: column; gap: 20px;">

                <div class="form-group">
                    <label class="form-label" style="font-weight: bold; font-size: 0.9rem; display: block; margin-bottom: 5px;">会社名・屋号 <span style="color: red;">*</span></label>
                    <input type="text" name="company_name" class="form-input" placeholder="例: 株式会社サンプル商事" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                </div>

                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;" class="form-group">
                        <label class="form-label" style="font-weight: bold; font-size: 0.9rem; display: block; margin-bottom: 5px;">ストア名 <span style="color: red;">*</span></label>
                        <input type="text" name="store_name" class="form-input" placeholder="例: サンプルストア" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                    </div>
                    <div style="flex: 1;" class="form-group">
                        <label class="form-label" style="font-weight: bold; font-size: 0.9rem; display: block; margin-bottom: 5px;">ストア名(カナ) <span style="color: red;">*</span></label>
                        <input type="text" name="store_name_kana" class="form-input" placeholder="例: サンプルストア" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                    </div>
                </div>

                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;" class="form-group">
                        <label class="form-label" style="font-weight: bold; font-size: 0.9rem; display: block; margin-bottom: 5px;">代表者名 <span style="color: red;">*</span></label>
                        <input type="text" name="representative_name" class="form-input" placeholder="例: 山田 太郎" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                    </div>
                    <div style="flex: 1;" class="form-group">
                        <label class="form-label" style="font-weight: bold; font-size: 0.9rem; display: block; margin-bottom: 5px;">携帯電話番号(ハイフンなし) <span style="color: red;">*</span></label>
                        <input type="tel" name="phone_number" class="form-input" placeholder="09012345678" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-weight: bold; font-size: 0.9rem; display: block; margin-bottom: 5px;">メールアドレス <span style="color: red;">*</span></label>
                    <input type="email" name="mail_address" class="form-input" placeholder="example@store.com" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-weight: bold; font-size: 0.9rem; display: block; margin-bottom: 5px;">パスワード <span style="color: red;">*</span></label>
                    <input type="password" name="password" class="form-input" placeholder="半角英数字で入力" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                </div>

                <div style="display: flex; gap: 20px;">
                    <div style="width: 150px;" class="form-group">
                        <label class="form-label" style="font-weight: bold; font-size: 0.9rem; display: block; margin-bottom: 5px;">郵便番号 <span style="color: red;">*</span></label>
                        <input type="text" name="zipcode" class="form-input" placeholder="0030806" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                    </div>
                    <div style="flex: 1;" class="form-group">
                        <label class="form-label" style="font-weight: bold; font-size: 0.9rem; display: block; margin-bottom: 5px;">住所 <span style="color: red;">*</span></label>
                        <input type="text" name="address" class="form-input" placeholder="北海道札幌市白石区菊水6条3丁目4-28" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-weight: bold; font-size: 0.9rem; display: block; margin-bottom: 5px;">ストア紹介文</label>
                    <textarea name="introduction" rows="3" placeholder="お店の紹介文を入力してください" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; resize: vertical;"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-weight: bold; font-size: 0.9rem; display: block; margin-bottom: 5px;">備考欄・注意事項</label>
                    <textarea name="notes" rows="3" placeholder="発送に関する注意点などがあれば入力してください" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; resize: vertical;"></textarea>
                </div>

                <div style="text-align: center; margin-top: 10px;">
                    <button type="submit" style="width: 100%; padding: 14px; background: #ff5a00; color: #fff; border: none; border-radius: 4px; font-weight: bold; font-size: 1rem; cursor: pointer;">
                        ストア登録を完了する
                    </button>
                </div>
            </form>

            <div style="text-align: center; margin-top: 20px;">
                <a href="register.php" style="color: #666; text-decoration: none; font-size: 0.85rem;">← 購入者用の新規登録に戻る</a>
            </div>

        </div>
    </main>

    <footer class="footer" style="margin-top: auto;">
        <div class="footer-bottom">
            <p>© 2026 Yahoo!ショッピング風チーム制作プロジェクト. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>