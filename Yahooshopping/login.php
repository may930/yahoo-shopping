<?php
/*
@author 自分の名前
@version 2.0
@date 作成日
*/

session_start();

/* インポート */
require_once('loginSQL.php');
require_once('Beans.php');
require_once('utilConnDB.php');

/* インスタンス生成 */
$loginSQL = new loginSQL();
$Beans = new Beans();
$utilConnDB = new UtilConnDB();

/* HTMLからデータを受け取る */
$login_info = isset($_POST['login_info']) ? htmlspecialchars($_POST['login_info'], ENT_QUOTES, 'utf-8') : '';

/* ボタン識別 */
$cmdBtnNo = 0;
for ($cmdBtnNo = 1; $cmdBtnNo <= 3; $cmdBtnNo++) 
{
    if (isset($_POST['cmdBtn' . $cmdBtnNo])) 
    {
        $cmdBtnNo = $cmdBtnNo;
        break;
    }
}

/* main */
switch ($cmdBtnNo) 
{
    case 1: // 「ログイン」ボタン

        $Beans->setphone_number($login_info);
        
        /* DB接続 */
        $pdo = $utilConnDB->connect();
        
        /* SQL文実行 */
        $List = array();
        $List = $loginSQL->select($pdo, $Beans);
        
        if (count($List) == 1) 
        {
            $Beans = $List[0];
            
            // コミットして切断
            $utilConnDB->commit($pdo);
            $utilConnDB->disconnect($pdo);
            
            // セッションにセットして移動
            $_SESSION['Beans'] = $Beans;
            
            header('Location: index.html');
            exit(); // 遷移した後は処理を強制終了する
        } 
        else /* 未登録 */
        { 
            $Beans->setphone_number("登録情報が見つかりませんでした");
            
            // ロールバック（またはそのまま切断）
            $utilConnDB->rollback($pdo);
            $utilConnDB->disconnect($pdo);
            
            $_SESSION['Beans'] = $Beans;
            
            // 未登録ならエラー表示などにするか、一旦ログインに戻す
            header('Location: login.html?error=notfound');
            exit();
        }
        break;
}
?>