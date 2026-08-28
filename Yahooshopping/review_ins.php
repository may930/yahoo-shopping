<?php
/*
review_ins.php(レビュー登録 コントローラ)
@author 自分の名前
@version 3.1
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

/* 未ログインならログイン画面へ */
if (!isset($_SESSION['user']['id']))
{
    header('Location: login_view.php');
    exit;
}
$user_id = (int)$_SESSION['user']['id'];

/* HTMLからデータを受け取る */
$rating    = isset($_POST['rating'])    ? (int)$_POST['rating'] : 0;
$title     = isset($_POST['title'])     ? htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8') : '';
$contents  = isset($_POST['contents'])  ? htmlspecialchars($_POST['contents'], ENT_QUOTES, 'UTF-8') : '';
$option_id = isset($_POST['option_id']) ? (int)$_POST['option_id'] : 0;

/* ボタン識別 */
$cmdBtnNo = 0;
for ($i = 1; $i <= 3; $i++)
{
    if (isset($_POST['cmdBtn' . $i]))
    {
        $cmdBtnNo = $i;
        break;
    }
}

/* main */
switch ($cmdBtnNo)
{
    case 1: // 「登録」ボタン

        /* 入力チェック */
        if ($rating < 1 || $rating > 5 || $title === '' || $contents === '' || $option_id <= 0)
        {
            $_SESSION['review_error'] = '評価・タイトル・本文を正しく入力してください。';
            break;
        }

        /* DB接続（接続失敗時は utilConnDB 内で die） */
        $pdo = $utilConnDB->connect();

        /* 購入済みか確認し、紐づける order_detail_id を取得 */
        $order_detail_id = $review_insSQL->findOrderDetailId($pdo, $user_id, $option_id);
        if ($order_detail_id === null)
        {
            $utilConnDB->disconnect($pdo);
            $_SESSION['review_error'] = 'ご購入いただいた商品のみレビューを投稿できます。';
            break;
        }

        /* レビュー画像（商品の代表画像を流用） */
        $image_url = $review_insSQL->findImageUrl($pdo, $option_id);

        $Beans->setrating($rating);
        $Beans->settitle($title);
        $Beans->setcontents($contents);
        $Beans->setoption_id($option_id);
        $Beans->setuser_id($user_id);
        $Beans->setorder_detail_id($order_detail_id);
        $Beans->setimage_url($image_url);

        /* SQL文実行 */
        $recCount = $review_insSQL->insert($pdo, $Beans);
        if ($recCount == 1)
        {
            $utilConnDB->commit($pdo); // コミット
            unset($_SESSION['review_error']);
            $Beans->BeansClear();
        }
        else
        {
            $utilConnDB->rollback($pdo); // ロールバック
            $_SESSION['review_error'] = 'レビューの登録に失敗しました。';
        }

        /* DB切断 */
        $utilConnDB->disconnect($pdo);
        break;
}

/* 次に実行するモジュール（元の商品ページのレビュー欄へ戻る） */
$redirectOption = $option_id > 0 ? $option_id : 1;
header('Location: product-detail.php?option_id=' . $redirectOption . '#reviews-section');
exit;
?>