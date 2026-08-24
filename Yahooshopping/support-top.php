<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お問い合わせTOP - Yahoo!ショッピング風</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>

    <main class="container" style="margin-top: 30px; margin-bottom: 60px; max-width: 800px;">

        <div style="text-align: center; margin-bottom: 32px;">
            <h1 style="font-size: 1.6rem; font-weight: 700; color: var(--color-black); margin-bottom: 8px;">
                💬 お問い合わせ・ヘルプセンター
            </h1>
            <p style="font-size: 0.9rem; color: #666;">
                ご不明な点がある場合は、まず以下のカテゴリから該当する項目をお選びください。
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 40px;">

            <div style="background: #fff; border: 1px solid #e4e7ec; border-radius: var(--radius-md); padding: 24px; box-shadow: var(--shadow-card); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="font-size: 1.8rem; margin-bottom: 8px;">📦</div>
                    <h2 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 8px;">注文・配送について</h2>
                    <p style="font-size: 0.85rem; color: #666; line-height: 1.5; margin-bottom: 16px;">
                        注文のキャンセル、お届け先の変更、商品が届かない、配送状況の確認などはこちら。
                    </p>
                </div>
                <a href="support-order.html" class="btn-sub-action" style="text-align: center; text-decoration: none; display: block; width: 100%; padding: 10px; font-weight: 500;">この内容で問い合わせる ＞</a>
            </div>

            <div style="background: #fff; border: 1px solid #e4e7ec; border-radius: var(--radius-md); padding: 24px; box-shadow: var(--shadow-card); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="font-size: 1.8rem; margin-bottom: 8px;">👤</div>
                    <h2 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 8px;">会員登録・ログインについて</h2>
                    <p style="font-size: 0.85rem; color: #666; line-height: 1.5; margin-bottom: 16px;">
                        パスワードを忘れた、ログインできない、会員情報の変更、退会手続きなどはこちら。
                    </p>
                </div>
                <a href="registration-order.html" class="btn-sub-action" style="text-align: center; text-decoration: none; display: block; width: 100%; padding: 10px; font-weight: 500;">この内容で問い合わせる ＞</a>
            </div>

            <div style="background: #fff; border: 1px solid #e4e7ec; border-radius: var(--radius-md); padding: 24px; box-shadow: var(--shadow-card); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="font-size: 1.8rem; margin-bottom: 8px;">💳</div>
                    <h2 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 8px;">お支払いについて</h2>
                    <p style="font-size: 0.85rem; color: #666; line-height: 1.5; margin-bottom: 16px;">
                        クレジットカードのエラー、PayPay連携、領収書の発行、請求金額の確認などはこちら。
                    </p>
                </div>
                <a href="payment-order.html" class="btn-sub-action" style="text-align: center; text-decoration: none; display: block; width: 100%; padding: 10px; font-weight: 500;">この内容で問い合わせる ＞</a>
            </div>

            <div style="background: #fff; border: 1px solid #e4e7ec; border-radius: var(--radius-md); padding: 24px; box-shadow: var(--shadow-card); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="font-size: 1.8rem; margin-bottom: 8px;">❓</div>
                    <h2 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 8px;">その他・ご意見</h2>
                    <p style="font-size: 0.85rem; color: #666; line-height: 1.5; margin-bottom: 16px;">
                        サイトの使い方が分からない、不具合の報告、サービスへのご意見・ご要望などはこちら。
                    </p>
                </div>
                <a href="others-order.html" class="btn-sub-action" style="text-align: center; text-decoration: none; display: block; width: 100%; padding: 10px; font-weight: 500;">この内容で問い合わせる ＞</a>
            </div>

        </div>

        <div style="background: var(--color-gray-50); border: 1px solid #e4e7ec; border-radius: var(--radius-sm); padding: 20px; text-align: center; font-size: 0.85rem; color: #555;">
            <p style="margin-bottom: 4px;">お急ぎの場合は、よくある質問（FAQ）ページもご確認ください。</p>
            <p>カスタマーサポート受付時間：24時間いつでも受付中（ご返信には1〜2営業日ほどいただきます）</p>
        </div>

    </main>

    <footer class="footer">
        <div class="footer-bottom">
            <p>© 2026 Yahoo!ショッピング風チーム制作プロジェクト. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>