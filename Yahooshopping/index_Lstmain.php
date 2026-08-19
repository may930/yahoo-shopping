<?php


/* インポート */
require_once('index_sql.php');
require_once('utilConnDB.php');
/* インスタンス生成 */
$toppage_sql = new TopPageSQL();
$utilConnDB = new UtilConnDB();
/*　DBに接続 */
$pdo = $utilConnDB->connect();

$toppage_popular_List = array();
$toppage_summer_List = array();
$toppage_food_List = array();
$toppageList = $toppage_sql->select_popular_produt($pdo);
$toppage_summer_List = $toppage_sql->select_summer_products($pdo);
$toppage_food_List = $toppage_sql->select_food_products($pdo);
/* DB切断 */

$utilConnDB->disconnect($pdo);
/* データを渡す */
session_start();
$_SESSION['toppageList'] = $toppageList;
$_SESSION['toppage_summer_List'] = $toppage_summer_List;
$_SESSION['toppage_food_List'] = $toppage_food_List;

/* 次に実行するモジュール */
header('Location: index.php');
?>