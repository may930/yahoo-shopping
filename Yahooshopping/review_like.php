<?php
/*
review_like.php(レビューへの「いいね」を記録・解除するエンドポイント)
@author 自分の名前
@version 2.0
@date 作成日
POST: review_id (int), action ('like' | 'unlike')
戻り値: JSON { status, like_count, liked }
※ 誰が押したかを review_likes テーブルに記録するため、ログインが必須。
*/

session_start();
header('Content-Type: application/json; charset=utf-8');
require_once 'db.php';

// 未ログインは 401 を返す（フロント側でログイン画面へ誘導する）
if (!isset($_SESSION['user']['id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'いいねにはログインが必要です']);
    exit;
}
$user_id = (int)$_SESSION['user']['id'];

$review_id = isset($_POST['review_id']) ? (int)$_POST['review_id'] : 0;
$action    = isset($_POST['action']) && $_POST['action'] === 'unlike' ? 'unlike' : 'like';

if ($review_id <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => '不正なリクエストです']);
    exit;
}

try {
    if ($action === 'unlike') {
        $sql = 'DELETE FROM review_likes WHERE review_id = :review_id AND user_id = :user_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['review_id' => $review_id, 'user_id' => $user_id]);
        $liked = false;
    } else {
        // 既に押していれば何もしない（主キー重複を無視する）
        $sql = 'INSERT IGNORE INTO review_likes (review_id, user_id) VALUES (:review_id, :user_id)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['review_id' => $review_id, 'user_id' => $user_id]);
        $liked = true;
    }

    $stmt2 = $pdo->prepare('SELECT COUNT(*) AS cnt FROM review_likes WHERE review_id = :review_id');
    $stmt2->execute(['review_id' => $review_id]);
    $row = $stmt2->fetch();

    echo json_encode([
        'status'     => 'ok',
        'like_count' => $row ? (int)$row['cnt'] : 0,
        'liked'      => $liked,
    ]);
} catch (PDOException $e) {
    error_log('review_like.php DB error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'データベースエラーが発生しました']);
}