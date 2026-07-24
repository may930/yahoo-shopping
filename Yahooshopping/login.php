<?php
/*
@author 自分の名前
@version 2.2
@date 作成日
*/

session_start();

require_once('loginSQL.php');
require_once('Beans.php');
require_once('utilConnDB.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cmdBtn1'])) {

    $login_info = isset($_POST['login_info']) ? trim($_POST['login_info']) : '';
    $password   = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!empty($login_info) && !empty($password)) {
        
        $loginSQL = new loginSQL();
        $Beans = new Beans();
        $utilConnDB = new UtilConnDB();

        // 検索キーをセット
        $Beans->setphone_number($login_info);
        if (method_exists($Beans, 'setpassword')) {
            $Beans->setpassword($password);
        }

        $pdo = $utilConnDB->connect();

        /* データベースの照会 */
        $List = $loginSQL->select($pdo, $Beans);

        if (count($List) == 1) {
            $userBeans = $List[0];

            $isPasswordValid = true;
            if (method_exists($userBeans, 'getpassword')) {
                if ($userBeans->getpassword() !== $password) {
                    $isPasswordValid = false;
                }
            }

            if ($isPasswordValid) {
                // コミットして切断
                $utilConnDB->commit($pdo);
                $utilConnDB->disconnect($pdo);

                // セッションにセット
                $_SESSION['Beans'] = $userBeans;

                // 表示用の名前を取得
                $userName = 'ユーザー';
                if (method_exists($userBeans, 'getuser_name') && !empty($userBeans->getuser_name())) {
                    $userName = $userBeans->getuser_name();
                } elseif (method_exists($userBeans, 'getname') && !empty($userBeans->getname())) {
                    $userName = $userBeans->getname();
                }

                // 出品者かどうか判定
                $isProducer = false;
                if (method_exists($userBeans, 'getis_producer')) {
                    $isProducer = $userBeans->getis_producer();
                }

                $_SESSION['user'] = [
                    'id'          => method_exists($userBeans, 'getuser_id') ? $userBeans->getuser_id() : '',
                    'name'        => $userName,
                    'is_producer' => $isProducer
                ];

                // 🚀 出品者の場合は admin_top.php へ直行！
                if ($isProducer) {
                    header('Location: admin_top.php');
                } else {
                    header('Location: index.php');
                }
                exit();
            }
        }

        // 認証失敗
        $utilConnDB->rollback($pdo);
        $utilConnDB->disconnect($pdo);

        header('Location: login_view.php?error=auth');
        exit();

    } else {
        header('Location: login_view.php?error=empty');
        exit();
    }
} else {
    header('Location: login_view.php');
    exit();
}