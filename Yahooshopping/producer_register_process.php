<?php
session_start();

$sql = "INSERT INTO producer (
            company_name, zipcode, address, representative_name, 
            store_name, store_name_kana, introduction, related_store, 
            notes, created_at, phone_number, mail_address, password
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, '', ?, NOW(), ?, ?, ?
        )";

require_once('utilConnDB.php');

// POSTでデータが送られてきているかチェック
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // フォームから送られてきたデータを取得
    $company_name        = $_POST['company_name'] ?? '';
    $store_name          = $_POST['store_name'] ?? '';
    $store_name_kana     = $_POST['store_name_kana'] ?? '';
    $representative_name = $_POST['representative_name'] ?? '';
    $phone_number        = $_POST['phone_number'] ?? '';
    $mail_address        = $_POST['mail_address'] ?? '';
    $password            = $_POST['password'] ?? '';
    $zipcode             = $_POST['zipcode'] ?? '';
    $address             = $_POST['address'] ?? '';
    $introduction        = $_POST['introduction'] ?? '';
    $notes               = $_POST['notes'] ?? '';

    // 必須項目が空でないかチェック
    if (!empty($company_name) && !empty($store_name) && !empty($phone_number) && !empty($mail_address) && !empty($password)) {

        $utilConnDB = new UtilConnDB();
        $pdo = $utilConnDB->connect();

        try {
            // データベースに登録するSQL文（producerテーブル）
            $sql = "INSERT INTO producer (
                        company_name, zipcode, address, representative_name, 
                        store_name, store_name_kana, introduction, related_store, 
                        notes, created_at, phone_number, mail_address, password
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, ?, '', ?, NOW(), ?, ?, ?
                    )";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $company_name,
                $zipcode,
                $address,
                $representative_name,
                $store_name,
                $store_name_kana,
                $introduction,
                $notes,
                $phone_number,
                $mail_address,
                $password // ※もしハッシュ化する仕様なら password_hash() を使ってね！
            ]);

            $utilConnDB->commit($pdo);
            $utilConnDB->disconnect($pdo);

            // 登録成功したら完了画面（またはログイン画面）へリダイレクト
            header('Location: register-complete.php');
            exit();

        } catch (Exception $e) {
            $utilConnDB->rollback($pdo);
            $utilConnDB->disconnect($pdo);
            echo "登録エラーが発生しました: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
            exit();
        }

    } else {
        // 必須項目が抜けている場合
        header('Location: producer_register.php?error=empty');
        exit();
    }

} else {
    // 直接アクセスされた場合
    header('Location: producer_register.php');
    exit();
}