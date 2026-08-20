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

                // 出品者かどうか判定
                $isProducer = false;
                if (method_exists($userBeans, 'getis_producer')) {
                    $isProducer = $userBeans->getis_producer();
                }

                // 表示用の名前を取得（Beansに存在するメソッドだけを安全に呼び出す）
                $userName = 'ユーザー';
                if ($isProducer) {
                    if (method_exists($userBeans, 'getstore_name') && !empty($userBeans->getstore_name())) {
                        $userName = $userBeans->getstore_name();
                    }
                } else {
                    if (method_exists($userBeans, 'getuser_name') && !empty($userBeans->getuser_name())) {
                        $userName = $userBeans->getuser_name();
                    } elseif (method_exists($userBeans, 'getname_first') && !empty($userBeans->getname_first())) {
                        $userName = $userBeans->getname_first();
                    }
                }

                // IDを取得
                $userId = method_exists($userBeans, 'getuser_id') ? $userBeans->getuser_id() : '';

                // 基本的なユーザー情報をセッションにセット
                $_SESSION['user'] = [
                    'name'        => $userName,
                    'is_producer' => $isProducer
                ];

                // 出品者の場合は producer_id と store_name をセット
                if ($isProducer) {
                    $_SESSION['user']['producer_id'] = $userId;
                    $_SESSION['user']['store_name']  = $userName;
                } else {
                    $_SESSION['user']['id'] = $userId;
                }

                // 🔑 新規登録側と同じように、DBに保持している詳細情報も
                // 存在するメソッドだけ安全に呼び出してセッションへ格納する
                if (method_exists($userBeans, 'getphone_number')) {
                    $_SESSION['user']['phone_number'] = $userBeans->getphone_number();
                }
                if (method_exists($userBeans, 'getmail_address')) {
                    $_SESSION['user']['mail_address'] = $userBeans->getmail_address();
                }
                if (!$isProducer) {
                    if (method_exists($userBeans, 'getzipcode')) {
                        $_SESSION['user']['zipcode'] = $userBeans->getzipcode();
                    }
                    if (method_exists($userBeans, 'getaddress')) {
                        $_SESSION['user']['address'] = $userBeans->getaddress();
                    }
                    if (method_exists($userBeans, 'getsex')) {
                        $_SESSION['user']['sex'] = $userBeans->getsex();
                    }
                }

                // 🚀 出品者の場合は admin_top.php へ直行！
if ($isProducer) {
    header('Location: admin_top.php');
} else {
    // ★変更：リダイレクト先が保存されていればそちらへ、無ければ従来通りindex.phpへ
    if (isset($_SESSION['redirect_after_login'])) {
        $redirect = $_SESSION['redirect_after_login'];
        unset($_SESSION['redirect_after_login']); // 使い終わったら削除（重要）
        header('Location: ' . $redirect);
    } else {
        header('Location: index.php');
    }
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