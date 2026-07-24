<?php
/* 07/16 */

class FavoriteSQL{

    /* 商品の一覧表示 */
    public function selectprodut($pdo){
        require_once('Beans.php');       
        $favoriteList = array();
        /* SQL文生成 */
        /* お気に入りID　商品名　商品イメージ図　価格　お気に入り追加日時 

        選択肢ID
        バリエーションID
        商品ID
        
        */
        $sql = 'SELECT product_favorites_id ,product_name ,image_url ,price ,add_at
                FROM product_favorites ,product_attributes_options ,product_attributes ,product ,product_images 
                WHERE product_favorites.option_id = product_attributes_options.option_id 
                AND product_attributes_options.option_id = product_images.option_id 
                AND product_attributes_options.variation_id = product_attributes.variation_id 
                AND product_attributes.product_id = product.product_id
                ORDER BY product_favorites.add_at DESC ;';
        $stmt = $pdo->prepare($sql);
       
        /* SQL文実行 */
        $ret = $stmt->execute();
       
        /* 検索結果をArrayListに登録 */
        foreach ($stmt as $row) {
                $Beans = new Beans();
            
                $Beans->setproduct_favorites_id($row['product_favorites_id']);
                $Beans->setproduct_name ($row['product_name']);
                $Beans->setimage_url ($row['image_url']);
                $Beans->setprice ($row['price']);
                $Beans->setadd_at ($row['add_at']);

                $favoriteList[] = $Beans; // $favoriteListに登録する
            }
       
            return $favoriteList;
        } 

    /* 削除するメソッド */
    public function itemdelete($pdo,$Beans)
    {
        try {
            $sql = "DELETE FROM product_favorites
                    WHERE product_favorites_id = ?";
    
            $stmt = $pdo->prepare($sql);
    
            $stmt->bindValue(
                1,
                $Beans->getproduct_favorites_id(),
                PDO::PARAM_INT
            );
    
            $stmt->execute();
    
            return $stmt->rowCount();
    
        } catch (PDOException $e) {
            echo $e->getMessage();
            return 0;
        } }
}
?>