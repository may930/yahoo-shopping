<?php
// クラスファイルの読み込み（実際に存在するファイル名に修正）
require_once 'utilConnDB.php';
require_once 'Beans.php';

session_start();

// 1. POST送信の確認（フォームからの送信時のみ処理を実行）
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{

    // 2. フォームからの受け取り＆Beansへのセット
    $beans = new Beans();
    
    // セッション等からログイン中のuser_idを取得
    // login.php / account_registration.php で $_SESSION['user']['id'] にセットしている
    $userId = $_SESSION['user']['id'] ?? null; 

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
            // ※ user_account テーブルには name_first/name_second 等の列は無く、
            //   name / name_kana の2列にまとめて保存する設計（account_registrationSQL.phpと同じ）
            $sql = "UPDATE user_account SET 
                        name = :name,
                        name_kana = :name_kana,
                        phone_number = :phone_number,
                        mail_address = :mail_address,
                        zipcode = :zipcode,
                        address = :address,
                        sex = :sex
                    WHERE user_id = :user_id";

            $stmt = $pdo->prepare($sql);

            // Beansから値を取得してバインド（SQLインジェクション対策）
            $stmt->bindValue(':name', $beans->getname_first() . $beans->getname_second(), PDO::PARAM_STR);
            $stmt->bindValue(':name_kana', $beans->getname_kana_first() . $beans->getname_kana_second(), PDO::PARAM_STR);
            $stmt->bindValue(':phone_number', $beans->getphone_number(), PDO::PARAM_STR);
            $stmt->bindValue(':mail_address', $beans->getmail_address(), PDO::PARAM_STR);
            $stmt->bindValue(':zipcode', $beans->getzipcode(), PDO::PARAM_STR);
            $stmt->bindValue(':address', $beans->getaddress(), PDO::PARAM_STR);
            $stmt->bindValue(':sex', $beans->getsex(), PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $beans->getuser_id(), PDO::PARAM_INT);

            // クエリ実行
            $stmt->execute();

            // コミット（utilConnDB::connect()内でbeginTransaction()しているため）
            $db->commit($pdo);

            // 🔑 セッション上の表示名も更新後の内容に合わせておく
            $_SESSION['user']['name']         = $beans->getname_first() . $beans->getname_second();
            $_SESSION['user']['phone_number'] = $beans->getphone_number();
            $_SESSION['user']['mail_address'] = $beans->getmail_address();
            $_SESSION['user']['zipcode']      = $beans->getzipcode();
            $_SESSION['user']['address']      = $beans->getaddress();
            $_SESSION['user']['sex']          = $beans->getsex();

            echo "登録情報を更新しました！";

        } 
        catch (PDOException $e) 
        {
            $db->rollback($pdo);
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