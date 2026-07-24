<?php
// db.php
$host = 'localhost';
$dbname = 'yahoo_shopping'; // ★ここをスクショに合わせて変更したよ！
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    exit('データベース接続に失敗しました: ' . $e->getMessage());
}