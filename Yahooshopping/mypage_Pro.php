<?php
session_start();
require_once 'utilConnDB.php';

$userId = $_SESSION['user_id'] ?? $_SESSION['user']['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("不正なアクセスです。");
}

if (!$userId) {
    die("エラー: セッションからユーザーIDが取得できませんでした。ログインし直してください。");
}

$name = $_POST['name'] ?? '';
$mail = $_POST['mail_address'] ?? '';
$zip  = $_POST['zipcode'] ?? '';
$addr = $_POST['address'] ?? '';

$db = new UtilConnDB();
$pdo = $db->connect();

if ($pdo) {
    try {
        // 1. まず user_account テーブルを更新
        $sql = "UPDATE user_account SET 
                    name = :name,
                    mail_address = :mail_address,
                    zipcode = :zipcode,
                    address = :address
                WHERE user_id = :user_id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':mail_address', $mail, PDO::PARAM_STR);
        $stmt->bindValue(':zipcode', $zip, PDO::PARAM_STR);
        $stmt->bindValue(':address', $addr, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_STR);

        $stmt->execute();
        $rowCount = $stmt->rowCount();

        // 2. user_account で更新がなかった場合、producer テーブルも更新を試みる
        if ($rowCount === 0) {
            $sqlProducer = "UPDATE producer SET 
                                name = :name,
                                mail_address = :mail_address,
                                zipcode = :zipcode,
                                address = :address
                            WHERE producer_id = :user_id";

            $stmtP = $pdo->prepare($sqlProducer);
            $stmtP->bindValue(':name', $name, PDO::PARAM_STR);
            $stmtP->bindValue(':mail_address', $mail, PDO::PARAM_STR);
            $stmtP->bindValue(':zipcode', $zip, PDO::PARAM_STR);
            $stmtP->bindValue(':address', $addr, PDO::PARAM_STR);
            $stmtP->bindValue(':user_id', $userId, PDO::PARAM_STR);

            $stmtP->execute();
            $rowCount = $stmtP->rowCount();
        }

        $db->disconnect($pdo);

        // 処理が終わったらリダイレクト
        header("Location: mypage.php");
        exit();

    } catch (PDOException $e) {
        die("SQLエラーが発生しました: " . $e->getMessage());
    }
}