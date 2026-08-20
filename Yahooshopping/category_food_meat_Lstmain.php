<?php
/* インポート */
require_once('category_sql.php');
require_once('utilConnDB.php');
/* インスタンス生成 */
$category_sql = new CategorySQL();
$utilConnDB = new UtilConnDB();
/*　DBに接続 */
$pdo = $utilConnDB->connect();

$category_meat_List = array();
$category_meat_List = $category_sql->child_meat_puroduct($pdo);
/* DB切断 */

$utilConnDB->disconnect($pdo);
/* データを渡す */
session_start();
$_SESSION['category_meat_List'] = $category_meat_List;

/* 次に実行するモジュール */
header('Location: category_food_meat.php');
?>