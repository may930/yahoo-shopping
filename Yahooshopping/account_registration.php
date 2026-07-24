<?php
/*
@author 自分の名前
@version 2.0
@date 作成日
*/

session_start();

/* インポート */
require_once('account_registrationSQL.php');
require_once('Beans.php');
require_once('utilConnDB.php');

/* インスタンス生成 */
$account_registrationSQL = new account_registrationSQL();
$Beans = new Beans();
$utilConnDB = new UtilConnDB();

/* HTMLからデータを受け取る */
$phone_number = isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number'], ENT_QUOTES, 'utf-8') : '';
$mail_address = isset($_POST['mail_address']) ? htmlspecialchars($_POST['mail_address'], ENT_QUOTES, 'utf-8') : '';
$name_first = isset($_POST['name_first']) ? htmlspecialchars($_POST['name_first'], ENT_QUOTES, 'utf-8') : '';
$name_second = isset($_POST['name_second']) ? htmlspecialchars($_POST['name_second'], ENT_QUOTES, 'utf-8') : '';
$name_kana_first = isset($_POST['name_kana_first']) ? htmlspecialchars($_POST['name_kana_first'], ENT_QUOTES, 'utf-8') : '';
$name_kana_second = isset($_POST['name_kana_second']) ? htmlspecialchars($_POST['name_kana_second'], ENT_QUOTES, 'utf-8') : '';
$password = isset($_POST['password']) ? htmlspecialchars($_POST['password'], ENT_QUOTES, 'utf-8') : '';
$sex = isset($_POST['sex']) ? htmlspecialchars($_POST['sex'], ENT_QUOTES, 'utf-8') : '';
$zipcode = isset($_POST['zipcode']) ? htmlspecialchars($_POST['zipcode'], ENT_QUOTES, 'utf-8') : '';
$address = isset($_POST['address']) ? htmlspecialchars($_POST['address'], ENT_QUOTES, 'utf-8') : '';

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
    case 1: // 「登録」ボタン

        $Beans->setphone_number($phone_number);
        $Beans->setmail_address($mail_address);
        $Beans->setname_first($name_first);
        $Beans->setname_second($name_second);
        $Beans->setname_kana_first($name_kana_first);
        $Beans->setname_kana_second($name_kana_second);
        $Beans->setpassword($password);
        $Beans->setsex($sex);
        $Beans->setzipcode($zipcode);
        $Beans->setaddress($address);
    
        /* DB接続 */
        $pdo = $utilConnDB->connect();

        /* SQL文実行 */
        $recCount = $account_registrationSQL->insert($pdo, $Beans);
        if ($recCount == 1) 
        {
            $utilConnDB->commit($pdo); // コミット
            $Beans->BeansClear();
        } 
        else 
        {
            $utilConnDB->rollback($pdo); // ロールバック
        }
        
        /* DB切断 */
        $utilConnDB->disconnect($pdo);
        
        /* データを渡す */
        session_start();
        $_SESSION['Beans'] = $Beans;
        break;
}
/* 次に実行するモジュール */
header('Location: register-complete.html');
?>
?>