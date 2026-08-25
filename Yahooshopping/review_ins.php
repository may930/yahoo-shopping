<?php
/*
@author 自分の名前
@version 2.0
@date 作成日
*/

session_start();

/* インポート */
require_once('review_insSQL.php');
require_once('Beans.php');
require_once('utilConnDB.php');

/* インスタンス生成 */
$review_insSQL = new review_insSQL();
$Beans = new Beans();
$utilConnDB = new utilConnDB();

/* HTMLからデータを受け取る */
$rating = isset($_POST['rating']) ? htmlspecialchars($_POST['rating'], ENT_QUOTES, 'utf-8') : '';
$title = isset($_POST['title']) ? htmlspecialchars($_POST['title'], ENT_QUOTES, 'utf-8') : '';
$contents = isset($_POST['contents']) ? htmlspecialchars($_POST['contents'], ENT_QUOTES, 'utf-8') : '';

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

        $Beans->setrating($rating);
        $Beans->settitle($title);
        $Beans->setcontents($contents);
        
        /* DB接続 */
        $pdo = $utilConnDB->connect();

        /* SQL文実行 */
        $recCount = $review_insSQL->insert($pdo, $Beans);
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
        $_SESSION['Beans'] = $Beans;
        break;
}
/* 次に実行するモジュール */
header('Location: product-detail.php');
?>