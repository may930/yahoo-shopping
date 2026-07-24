<?php
/*
  logout.php - ログアウト処理
*/

// 1. セッションを開始
session_start();

// 2. セッション変数をすべて解除（ログイン情報を消去）
$_SESSION = array();

// 3. クッキーに保存されたセッションIDも削除
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. 最後にセッション自体を破壊
session_destroy();

// 5. トップページ（index.php）に自動転送（リダイレクト）
header('Location: index.php');
exit;