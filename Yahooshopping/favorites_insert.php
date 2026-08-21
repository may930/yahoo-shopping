<?php
/*
favorite-toggle.php
商品詳細ページのハートボタンから呼ばれるAPI。
ログイン中のユーザーについて、指定された option_id を
お気に入りに「追加」または「解除」してJSONで結果を返す。
*/

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once('favorite_SQL.php');
require_once('Beans.php');
require_once('utilConnDB.php');

// 🔑 ログインチェック（login.phpが $_SESSION['user']['id'] にuser_idをセットしている）
if (!isset($_SESSION['user']['id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'ログインが必要です']);
    exit;
}

$user_id   = (int) $_SESSION['user']['id'];
$option_id = isset($_POST['option_id']) ? (int) $_POST['option_id'] : 0;

if ($option_id <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'option_id が不正です']);
    exit;
}

$favorite_SQL = new FavoriteSQL();
$beans = new Beans();
$beans->setuser_id($user_id);
$beans->setoption_id($option_id);

$utilConnDB = new UtilConnDB();
$pdo = $utilConnDB->connect();

if (!$pdo) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'データベースに接続できませんでした']);
    exit;
}

try {
    $already = $favorite_SQL->isFavorited($pdo, $beans);

    if ($already) {
        // 既にお気に入り登録済み → 解除
        $favorite_SQL->deleteByUserOption($pdo, $beans);
        $pdo->commit();
        echo json_encode(['status' => 'ok', 'favorited' => false]);
    } else {
        // 未登録 → 追加
        $favorite_SQL->addFavorite($pdo, $beans);
        $pdo->commit();
        echo json_encode(['status' => 'ok', 'favorited' => true]);
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'お気に入りの更新に失敗しました']);
} finally {
    $utilConnDB->disconnect($pdo);
}