<?php
/* インポート */
require_once('category_sql.php');
require_once('utilConnDB.php');
/* インスタンス生成 */
$category_sql = new CategorySQL();
$utilConnDB = new UtilConnDB();
/*　DBに接続 */
$pdo = $utilConnDB->connect();

$category_kids_List = array();
$category_kids_List = $category_sql->parent_kids_puroduct($pdo);
/* DB切断 */

$utilConnDB->disconnect($pdo);
/* データを渡す */
session_start();
$_SESSION['category_kids_List'] = $category_kids_List;

/* 次に実行するモジュール */
header('Location: category_kids.php');
?>