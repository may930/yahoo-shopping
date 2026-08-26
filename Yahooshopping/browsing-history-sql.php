<?php 
    class Browsing_history_SQL{
        public function select_history_product($pdo,$user_id){
            require_once('Beans.php');       
            $history_List = array();
            $sql = "SELECT product_attributes_options.option_id, product.product_name, product_images.image_url, product_attributes_options.price, views_history.view_time
                    FROM views_history
                    JOIN product_attributes_options ON views_history.option_id = product_attributes_options.option_id
                    JOIN product_attributes ON product_attributes_options.variation_id = product_attributes.variation_id
                    JOIN product ON product_attributes.product_id = product.product_id
                    JOIN product_images ON product_attributes_options.option_id = product_images.option_id
                    WHERE views_history.user_id = ?
                    GROUP BY product_attributes_options.option_id, product.product_name, views_history.view_time
                    ORDER BY views_history.view_time DESC
                    LIMIT 20";
            $stmt = $pdo->prepare($sql);
           
            /* SQL文実行 */
            $ret = $stmt->execute([$user_id]);
           
            /* 検索結果をArrayListに登録 */
            foreach ($stmt as $row) {
                    $Beans = new Beans();
                
                    //$Beans->setproduct_id($row['product_id']);
                    $Beans->setoption_id($row['option_id']);
                    $Beans->setproduct_name ($row['product_name']);
                    $Beans->setimage_url ($row['image_url']);
                    $Beans->setprice ($row['price']);
                    $Beans->setview_time ($row['view_time']);
    
                    $history_List[] = $Beans; // $history_Listに登録する
                }
           
                return $history_List;
        } 
    }
?>