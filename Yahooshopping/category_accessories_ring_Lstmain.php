<?php
/* インポート */
require_once('category_sql.php');
require_once('utilConnDB.php');
/* インスタンス生成 */
$category_sql = new CategorySQL();
$utilConnDB = new UtilConnDB();
/*　DBに接続 */
$pdo = $utilConnDB->connect();

$category_accessories_ring_List = array();
$category_accessories_ring_List = $category_sql->child_accessories_ring_puroduct($pdo);
/* DB切断 */

$utilConnDB->disconnect($pdo);
/* データを渡す */
session_start();
$_SESSION['category_accessories_ring_List'] = $category_accessories_ring_List;

/* 次に実行するモジュール */
header('Location: category_accessories_ring.php');
?>