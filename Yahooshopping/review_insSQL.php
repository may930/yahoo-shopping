<?php
/*
review_insSQL.php(レビュー登録 SQLクラス)
@author 自分の名前
@version 3.0
@date 作成日
*/
class review_insSQL
{
    /**
     * レビューを1件登録する
     * @param PDO $pdo
     * @param Beans $inBeans
     * @return int 登録件数（成功:1 / 失敗:0）
     */
    public function insert($pdo, $inBeans)
    {
        $recCount = 0;

        /* SQL文生成 */
        // review_id(自動採番), order_detail_id, option_id, user_id, rating, title, comment,
        // image_url, created_at(自動), views_count(0), like_count(0)
        $sql = 'INSERT INTO product_reviews
                    (order_detail_id, option_id, user_id, rating, title, comment, image_url, created_at, views_count, like_count)
                VALUES
                    (:order_detail_id, :option_id, :user_id, :rating, :title, :comment, :image_url, CURRENT_TIMESTAMP, 0, 0)';
        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':order_detail_id', $inBeans->getorder_detail_id(), PDO::PARAM_INT);
        $stmt->bindValue(':option_id',       $inBeans->getoption_id(),       PDO::PARAM_INT);
        $stmt->bindValue(':user_id',         $inBeans->getuser_id(),         PDO::PARAM_INT);
        $stmt->bindValue(':rating',          $inBeans->getrating(),          PDO::PARAM_INT);
        $stmt->bindValue(':title',           $inBeans->gettitle());
        $stmt->bindValue(':comment',         $inBeans->getcontents());
        $stmt->bindValue(':image_url',       $inBeans->getimage_url());

        try
        {
            /* SQL文実行 */
            $stmt->execute();
            /* 件数を取得 */
            $recCount = $stmt->rowCount();
        }
        catch (PDOException $e)
        {
            // 登録失敗（外部キー不一致などもここに来る）
            $recCount = 0;
        }
        return $recCount;
    }

    /**
     * ログインユーザーが指定のオプション商品を購入済みか確認し、
     * レビューに紐づける order_detail_id を1件取得する（未購入なら null）
     */
    public function findOrderDetailId($pdo, $userId, $optionId)
    {
        $sql = 'SELECT od.order_detail_id
                FROM order_details od
                INNER JOIN order_history oh ON od.order_id = oh.order_id
                WHERE oh.user_id = :user_id AND od.option_id = :option_id
                ORDER BY od.order_detail_id DESC
                LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':option_id', $optionId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? (int)$row['order_detail_id'] : null;
    }

    /**
     * レビューに添える画像として、商品オプションの代表画像を1枚取得する
     * （見つからなければ空文字）
     */
    public function findImageUrl($pdo, $optionId)
    {
        $sql = 'SELECT image_url
                FROM product_images
                WHERE option_id = :option_id
                ORDER BY display_order ASC
                LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':option_id', $optionId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? $row['image_url'] : '';
    }
}
?>