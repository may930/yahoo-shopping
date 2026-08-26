<?php
session_start();

/* インポート */
require_once('browsing-history-sql.php');
require_once('utilConnDB.php');
/* インスタンス生成 */
$history_sql = new Browsing_history_SQL();
$utilConnDB = new UtilConnDB();
//ユーザーID取得
$user_id = $_SESSION['user']['id'] ?? null;
/*　DBに接続 */
$pdo = $utilConnDB->connect();

$history_List = array();
$history_List = $history_sql->select_history_product($pdo,$user_id);
/* DB切断 */

$utilConnDB->disconnect($pdo);
/* データを渡す */
$_SESSION['history_List'] = $history_List;

/* 次に実行するモジュール */
header('Location: browsing-history.php');
?>