<?php
session_start();

/* インポート */
require_once('favorite_SQL.php');
require_once('Beans.php');
require_once('utilConnDB.php');

/* インスタンス生成 */
$favorite_SQL = new FavoriteSQL();
$beans = new Beans();
$utilConnDB = new UtilConnDB();

/* HTMLから削除対象のIDを受け取る（数値としてキャスト） */
$favorite_deleteitem = isset($_POST['delete_item']) ? (int)$_POST['delete_item'] : 0;
if ($favorite_deleteitem > 0) {
    /* 入力データをBeansにセット */
    $beans->setproduct_favorites_id($favorite_deleteitem);

    /* DB接続 */
    $pdo = $utilConnDB->connect();

    try {
        // トランザクション開始
        $pdo->beginTransaction(); 

        /* SQL実行 */
        $recCount = $favorite_SQL->itemdelete($pdo, $beans);

        if ($recCount > 0) {
            // 成功時コミット
            $pdo->commit();
        } else {
            // 失敗時ロールバック
            //$pdo->rollBack();
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            //$pdo->rollBack();
        }
    } finally {
        /* DB切断 */
        $utilConnDB->disconnect($pdo);
    }
}

// 削除完了後、一覧再取得モジュールへリダイレクト
header('Location: favorites_Lstmain.php');
exit;
?>