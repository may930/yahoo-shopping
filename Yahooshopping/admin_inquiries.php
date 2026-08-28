<?php
session_start();
require_once 'db.php';

// 出品者（またはログインユーザー）のIDを取得
// ※もし購入者セッションと共通なら $_SESSION['user']['id'] に変更してね！
$producer_id = isset($_SESSION['producer']['id']) ? $_SESSION['producer']['id'] : (isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 1);

// 返信が送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_submit'])) {
    $inquiry_id = intval($_POST['inquiry_id']);
    $reply_contents = trim($_POST['reply_contents']);

    if ($inquiry_id > 0 && $reply_contents !== '') {
        try {
            // inquiry_history に出品者の返信（sender = 0）を挿入
            $stmt_reply = $pdo->prepare("
                INSERT INTO inquiry_history (inquiry_id, sender, contents, send_time)
                VALUES (:inquiry_id, 0, :contents, NOW())
            ");
            $stmt_reply->execute([
                'inquiry_id' => $inquiry_id,
                'contents'   => $reply_contents
            ]);

            header('Location: admin_inquiries.php?success=1');
            exit;
        } catch (PDOException $e) {
            $error_message = '返信の送信に失敗しました: ' . $e->getMessage();
        }
    } else {
        $error_message = '返信内容を入力してください。';
    }
}

try {
    // この出品者宛ての問い合わせをすべて取得
    $stmt = $pdo->prepare("
        SELECT i.*, p.product_name, o.option_name 
        FROM inquiry AS i
        INNER JOIN product_attributes_options AS o ON i.option_id = o.option_id
        INNER JOIN product_attributes AS pa ON o.variation_id = pa.variation_id
        INNER JOIN product AS p ON pa.product_id = p.product_id
        WHERE i.producer_id = :producer_id
        ORDER BY i.inquiry_at DESC
    ");
    $stmt->execute(['producer_id' => $producer_id]);
    $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    exit('データ取得に失敗しました: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カスタマーQ&A 管理画面 - ストアダッシュボード</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background: #f4f6f8; font-family: sans-serif; margin: 0; padding: 20px;">

    <div style="max-width: 900px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #0056b3; padding-bottom: 12px; margin-bottom: 20px;">
            <h1 style="font-size: 1.4rem; color: #333; margin: 0;">💬 顧客からの問い合わせ管理</h1>
            <a href="index.php" style="color: #0056b3; text-decoration: none; font-size: 0.9rem;">← トップページに戻る</a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div style="background: #e6f4ea; color: #137333; padding: 12px; border-radius: 4px; margin-bottom: 20px; font-weight: bold;">
                ✅ 返信を送信しました！
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div style="background: #fce8e6; color: #c5221f; padding: 12px; border-radius: 4px; margin-bottom: 20px; font-weight: bold;">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($inquiries)): ?>
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <?php foreach ($inquiries as $inq): ?>
                    <?php
                        // 各問い合わせに対する過去のやり取り（履歴）をすべて取得
                        $stmt_hist = $pdo->prepare("SELECT * FROM inquiry_history WHERE inquiry_id = :inquiry_id ORDER BY send_time ASC");
                        $stmt_hist->execute(['inquiry_id' => $inq['inquiry_id']]);
                        $histories = $stmt_hist->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <div style="border: 1px solid #dfe3e8; border-radius: 6px; padding: 20px; background: #fafbfc;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 0.85rem; color: #666;">
                            <span>対象商品: <strong><?= htmlspecialchars($inq['product_name']) ?> (<?= htmlspecialchars($inq['option_name']) ?>)</strong></span>
                            <span>投稿日時: <?= htmlspecialchars($inq['inquiry_at']) ?></span>
                        </div>

                        <div style="margin-bottom: 12px;">
                            <span style="font-size: 0.75rem; background: <?= $inq['privacy'] == 0 ? '#e1f5fe; color: #0288d1;' : '#fff3e0; color: #f57c00;' ?> padding: 2px 6px; border-radius: 4px; font-weight: bold;">
                                <?= $inq['privacy'] == 0 ? '公開質問' : '非公開質問' ?>
                            </span>
                            <h3 style="font-size: 1rem; margin: 6px 0; color: #222;"><?= htmlspecialchars($inq['title']) ?></h3>
                            <p style="font-size: 0.85rem; color: #555; margin: 0;">ユーザーメール: <?= htmlspecialchars($inq['user_mailaddress']) ?></p>
                        </div>

                        <!-- やり取りのタイムライン表示 -->
                        <div style="background: #fff; border: 1px solid #e4e7ec; border-radius: 4px; padding: 15px; margin-bottom: 15px; display: flex; flex-direction: column; gap: 12px;">
                            <?php foreach ($histories as $h): ?>
                                <div style="padding: 10px; border-radius: 4px; background: <?= $h['sender'] == 1 ? '#f7f9fa' : '#f0f4f8' ?>; border-left: 4px solid <?= $h['sender'] == 1 ? '#94a3b8' : '#0056b3' ?>;">
                                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: #666; margin-bottom: 4px;">
                                        <strong><?= $h['sender'] == 1 ? '購入者からの質問' : 'ストアからの返信' ?></strong>
                                        <span><?= htmlspecialchars($h['send_time']) ?></span>
                                    </div>
                                    <p style="font-size: 0.9rem; color: #333; margin: 0; white-space: pre-wrap;"><?= htmlspecialchars($h['contents']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- 返信フォーム -->
                        <form method="POST" style="display: flex; flex-direction: column; gap: 10px;">
                            <input type="hidden" name="inquiry_id" value="<?= $inq['inquiry_id'] ?>">
                            <textarea name="reply_contents" rows="3" required placeholder="この質問に対する返信を入力してください..." style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; resize: vertical;"></textarea>
                            <div style="text-align: right;">
                                <button type="submit" name="reply_submit" style="padding: 8px 20px; background: #0056b3; color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
                                    返信を送信する
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #666; padding: 40px 0;">現在、届いているお問い合わせはありません。</p>
        <?php endif; ?>
    </div>

</body>
</html>