<?php
// セッション開始と依存ファイルの読み込み
require_once 'Beans.php';
require_once 'utilConnDB.php';
require_once 'MypageSQL.php';

session_start();

// XSS対策関数（未定義の場合のみ定義）
if (!function_exists('h')) {
    function h($str) {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
}

// セッションからログインユーザーIDを取得
$userId = $_SESSION['user_id'] ?? $_SESSION['user']['id'] ?? null;

// 未ログインの場合はログイン画面へ遷移
if (!$userId) {
    header('Location: login.php');
    exit();
}

// データベースからユーザー情報を取得
$db = new UtilConnDB();
$pdo = $db->connect();

$mypageSql = new MypageSQL();
$beans = $mypageSql->selectById($pdo, $userId);

// 取得失敗やnullだった場合に備えて安全に初期化しておく（これが重要！）
if (!$beans) 
{
    $beans = new Beans();
}

// 接続解除
$db->disconnect($pdo);

// セッションから画面用データの受け取り
$favoriteList = $_SESSION['favoriteList'] ?? array();
$toppageList = $_SESSION['toppageList'] ?? array();
$count = count($favoriteList);

// 表示用HTMLの呼び出し
include 'mypage.php';