<?php


/* インポート */
require_once('favorite_SQL.php');
require_once('utilConnDB.php');
/* インスタンス生成 */
$favorite_SQL = new FavoriteSQL();
$utilConnDB = new UtilConnDB();
/*　DBに接続 */
$pdo = $utilConnDB->connect();

$favoriteList = array();
$favoriteList = $favorite_SQL->selectprodut($pdo);
/* DB切断 */

$utilConnDB->disconnect($pdo);
/* データを渡す */
session_start();
$_SESSION['favoriteList'] = $favoriteList;
/* 次に実行するモジュール */
header('Location: favorites.php');
?>