<?php
session_start();

// 1. データベース接続
require_once 'db.php';

// 2. ログインチェック（念のためここでも弾く）
if (!isset($_SESSION['user']['id'])) {
    header('Location: login_view.php');
    exit;
}

// 3. POSTデータの受け取り
$option_id         = isset($_POST['option_id']) ? intval($_POST['option_id']) : 0;
$title             = isset($_POST['title']) ? trim($_POST['title']) : '';
$user_mailaddress  = isset($_POST['user_mailaddress']) ? trim($_POST['user_mailaddress']) : '';
$inquiry_contents  = isset($_POST['inquiry_contents']) ? trim($_POST['inquiry_contents']) : '';
// チェックボックスが外れている場合は非公開（1）、入っている場合は公開（0）
$privacy           = isset($_POST['privacy']) ? 0 : 1; 

$user_id = $_SESSION['user']['id'];

// バリデーション（空っぽの項目がないかチェック）
if ($option_id <= 0 || $title === '' || $user_mailaddress === '' || $inquiry_contents === '') {
    exit('不正なアクセスです。必要な項目が入力されていません。');
}

try {
    // 4. このオプションから「出品者（producer_id）」を特定する
    // product_attributes_options -> product_attributes -> product を辿る
    $stmt_producer = $pdo->prepare("
        SELECT p.producer_id 
        FROM product_attributes_options AS o
        INNER JOIN product_attributes AS pa ON o.variation_id = pa.variation_id
        INNER JOIN product AS p ON pa.product_id = p.product_id
        WHERE o.option_id = :option_id
    ");
    $stmt_producer->execute(['option_id' => $option_id]);
    $producer = $stmt_producer->fetch(PDO::FETCH_ASSOC);

    // 万が一プロデューサーが見つからない場合のフォールバック（必要に応じて調整）
    $producer_id = $producer ? $producer['producer_id'] : 1; 

    // トランザクション開始（複数のテーブルに安全に書き込むため）
    $pdo->beginTransaction();

    // 5. `inquiry` テーブルに親データを挿入
    $stmt_inquiry = $pdo->prepare("
        INSERT INTO inquiry (user_id, producer_id, option_id, title, inquiry_contents, user_mailaddress, privacy, inquiry_at)
        VALUES (:user_id, :producer_id, :option_id, :title, :inquiry_contents, :user_mailaddress, :privacy, NOW())
    ");
    $stmt_inquiry->execute([
        'user_id'          => $user_id,
        'producer_id'      => $producer_id,
        'option_id'        => $option_id,
        'title'            => $title,
        'inquiry_contents' => $inquiry_contents,
        'user_mailaddress' => $user_mailaddress,
        'privacy'          => $privacy
    ]);

    // 今挿入した inquiry_id を取得
    $inquiry_id = $pdo->lastInsertId();

    // 6. `inquiry_history` テーブルに最初のメッセージ（質問）を挿入
    // ※ sender = 1 は購入者からのメッセージを表す前提
    $stmt_history = $pdo->prepare("
        INSERT INTO inquiry_history (inquiry_id, sender, contents, send_time)
        VALUES (:inquiry_id, 1, :contents, NOW())
    ");
    $stmt_history->execute([
        'inquiry_id' => $inquiry_id,
        'contents'   => $inquiry_contents
    ]);

    // トランザクション確定
    $pdo->commit();

    // 7. 送信完了後、元の商談商品ページへリダイレクト
    header('Location: product-detail.php?option_id=' . $option_id . '&inquiry=success');
    exit;

} catch (PDOException $e) {
    // エラー時はロールバック
    $pdo->rollBack();
    exit('データの保存に失敗しました: ' . $e->getMessage());
}
?>