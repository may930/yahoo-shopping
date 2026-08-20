<?php
// クラスファイルの読み込み
require_once 'utilCommDB.php';
require_once 'srchBeans.php';

session_start();

// 1. POST送信の確認（フォームからの送信時のみ処理を実行）
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{

    // 2. フォームからの受け取り＆Beansへのセット
    $beans = new Beans();
    
    // セッション等からログイン中のuser_idを取得（例: $_SESSION['user_id']）
    $userId = $_SESSION['user_id'] ?? null; 

    if (!$userId) 
    {
        die("エラー: ログインユーザーの情報が取得できません。");
    }

    $beans->setuser_id($userId);
    $beans->setname_first($_POST['name_first'] ?? '');
    $beans->setname_second($_POST['name_second'] ?? '');
    $beans->setname_kana_first($_POST['name_kana_first'] ?? '');
    $beans->setname_kana_second($_POST['name_kana_second'] ?? '');
    $beans->setphone_number($_POST['phone_number'] ?? '');
    $beans->setmail_address($_POST['mail_address'] ?? '');
    $beans->setzipcode($_POST['zipcode'] ?? '');
    $beans->setaddress($_POST['address'] ?? '');
    $beans->setsex($_POST['sex'] ?? '');

    // 3. データベース更新処理
    $db = new UtilConnDB();
    $pdo = $db->connect();

    if ($pdo) 
        {
        try 
        {
            // SQL文（UPDATE文）の作成
            $sql = "UPDATE users SET 
                        name_first = :name_first,
                        name_second = :name_second,
                        name_kana_first = :name_kana_first,
                        name_kana_second = :name_kana_second,
                        phone_number = :phone_number,
                        mail_address = :mail_address,
                        zipcode = :zipcode,
                        address = :address,
                        sex = :sex
                    WHERE user_id = :user_id";

            $stmt = $pdo->prepare($sql);

            // Beansから値を取得してバインド（SQLインジェクション対策）
            $stmt->bindValue(':name_first', $beans->getname_first(), PDO::PARAM_STR);
            $stmt->bindValue(':name_second', $beans->getname_second(), PDO::PARAM_STR);
            $stmt->bindValue(':name_kana_first', $beans->getname_kana_first(), PDO::PARAM_STR);
            $stmt->bindValue(':name_kana_second', $beans->getname_kana_second(), PDO::PARAM_STR);
            $stmt->bindValue(':phone_number', $beans->getphone_number(), PDO::PARAM_STR);
            $stmt->bindValue(':mail_address', $beans->getmail_address(), PDO::PARAM_STR);
            $stmt->bindValue(':zipcode', $beans->getzipcode(), PDO::PARAM_STR);
            $stmt->bindValue(':address', $beans->getaddress(), PDO::PARAM_STR);
            $stmt->bindValue(':sex', $beans->getsex(), PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $beans->getuser_id(), PDO::PARAM_INT);

            // クエリ実行
            $stmt->execute();

            echo "登録情報を更新しました！";

        } 
        catch (PDOException $e) 
        {
            echo "更新処理に失敗しました: " . $e->getMessage();
        } 
        finally 
        {
            // DB切断
            $db->disconnect($pdo);
        }
    } 
    else 
    {
        echo "データベース接続に失敗しました。";
    }
}
?>