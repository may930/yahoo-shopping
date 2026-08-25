<?php
header('Content-Type:text/plain; charset=utf-8');
class review_insSQL 
{
    public function insert($pdo,$inBeans) 
    {
        $recCount = 0;
        /* SQL文生成 */
        // レビューID、注文詳細ID、選択肢ID、ユーザID、評価、タイトル、コメント、画像URL、投稿日時、閲覧数、いいね数
        // reviwe_id,order_detail_id,option_id,user_id,rating,title,comment,image_url,created_at,views_count,like_count
        $sql = 'INSERT INTO reviews (0,) 
                    VALUES (0, 0, 0, 0, ?, ? ,?, 0, 0, 0, 0);';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(5, $inBeans->getrating().$inBeans->getratinf());
        $stmt->bindValue(6, $inBeans->gettitle().$inBeans->gettitle());
        $stmt->bindValue(7, $inBeans->getcontents().$inBeans->getcontents());

        try 
        {
            /* SQL文実行 */
            $ret = $stmt->execute();
            /* 件数を取得 */
            $recCount = $stmt->rowCount();
        } 
        catch (PDOException $e) {}
        return $recCount;
    }
}
   ?> 
