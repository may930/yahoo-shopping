<?php
/*
utilCommDB.php(データベース関連クラス)
@author 自分の名前
@version 2.0
@date 作成日
*/
class UtilConnDB {
/* データベース設定 */
private $dbHostName = 'localhost';
private $dbUserName = 's20247077'; // ユーザ名(各自の学籍番号)
private $dbPassword = '20050724'; // パスワード(各自の生年月日)
private $dbDBName = 'yahooshopping_db'; // データベース名
/*
* DBを生成する
*/
public function createDB() {
$dbSW = true;
try {
$pdo = new PDO(
'mysql:host=' . $this->dbHostName
. ';dbname='
. ';charset=utf8mb4',
$this->dbUserName,
$this->dbPassword,
[

PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]
);
$sql = 'CREATE DATABASE ' . $this->dbDBName
. ' default character set utf8';
$count = $pdo->exec($sql);
if ($count == 0) { // SQL文実行失敗
throw new PDOException(); // catch へ移動
}
} catch (PDOException $e) {
$dbSW = false;
}
return $dbSW;
}
/*
* DBを接続する
*/
public function connect() {
$pdo = null;
try {
$pdo = new PDO(
'mysql:host=' . $this->dbHostName
. ';dbname=' . $this->dbDBName
. ';charset=utf8mb4',
$this->dbUserName,
$this->dbPassword,
[
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]
);


- 4 -

$pdo->beginTransaction(); // トランザクション開始
} catch (PDOException $e) {
}
return $pdo;
}
/*
* DBをコミットする
*/
public function commit($pdo) {
$pdo->commit();
}
/*
* DBをロールバックする
*/
public function rollback($pdo) {
$pdo->rollback();
}
/*
* DBを切断する
*/
public function disconnect($pdo) {
$pdo = null;
}
}
?>