<?php
/* 07/16 */

class TopPageSQL{

    /* トップページに表示する商品のジャンルによって変更する（夏におすすめの商品　食品　人気ランキングなど） */

    /* 人気商品の一覧表示 */
    public function select_popular_produt($pdo){
        require_once('Beans.php');       
        $toppageList = array();
        /* SQL文生成 */
        /* 商品ID、商品名、商品画像、値段、売れた商品の数量(同じデータの合計) */
        $sql = 'SELECT product.product_id ,product.product_name ,product_images.image_url ,product_attributes_options.price ,SUM(order_details.quantity) AS total_sold
                FROM product_attributes_options ,product_attributes ,product ,product_images ,order_details
                WHERE product.product_id = product_attributes.product_id
                AND product_attributes.variation_id = product_attributes_options.variation_id
                AND product_attributes_options.option_id = product_images.option_id
                AND product_attributes_options.option_id = order_details.option_id
                GROUP BY product_attributes_options.option_id
                ORDER BY total_sold DESC, product.product_id ASC
                LIMIT 3;';
        $stmt = $pdo->prepare($sql);
       
        /* SQL文実行 */
        $ret = $stmt->execute();
       
        /* 検索結果をtoppageListに登録 */
        foreach ($stmt as $row) {
                $Beans = new Beans();
            
                $Beans->setproduct_id($row['product_id']);
                $Beans->setproduct_name ($row['product_name']);
                $Beans->setimage_url ($row['image_url']);
                $Beans->setprice ($row['price']);
                $Beans->setquantity ($row['total_sold']);

                $toppageList[] = $Beans;
            }
       
        return $toppageList;
    } 


    private $summer_products_id = [22, 23, 24, 25, 26]; // 冷えピタ、汗拭きシートなど、実際のoption_id/product_idを列挙

    //夏におすすめの商品
    function select_summer_products($pdo){
        require_once('Beans.php');       
        $toppage_summer_List = array();

        if (empty($this->summer_products_id)) {
            return $toppage_summer_List; // 配列が空なら何もせず空配列を返す
        }

        //[]の数だけ増やす
        $placeholders = implode(',', array_fill(0, count($this->summer_products_id), '?'));

        /* SQL文生成 */
        /* 商品ID、商品名、商品画像、値段、*/
        $sql = "SELECT product.product_id ,product.product_name ,product_images.image_url ,product_attributes_options.price
                FROM product_attributes_options ,product_attributes ,product ,product_images
                WHERE product.product_id = product_attributes.product_id 
                AND product.product_id IN ($placeholders)
                AND product_attributes.variation_id = product_attributes_options.variation_id
                AND product_attributes_options.option_id = product_images.option_id
                GROUP BY product.product_id, product.product_name
                LIMIT 3;";
        $stmt = $pdo->prepare($sql);
       
        /* SQL文実行 */
        $ret = $stmt->execute($this->summer_products_id);       
        /* 検索結果をtoppageListに登録 */
        foreach ($stmt as $row) {
                $Beans = new Beans();
            
                $Beans->setproduct_id($row['product_id']);
                $Beans->setproduct_name ($row['product_name']);
                $Beans->setimage_url ($row['image_url']);
                $Beans->setprice ($row['price']);

                $toppage_summer_List[] = $Beans;
            }
       
        return $toppage_summer_List;
    }

    private $food_products_id = [9, 10, 11, 12]; // 冷えピタ、汗拭きシートなど、実際のoption_id/product_idを列挙
    //食べ物でおすすめの商品
    function select_food_products($pdo){
        require_once('Beans.php');       
        $toppage_food_List = array();

        if (empty($this->food_products_id)) {
            return $toppage_food_List; // 配列が空なら何もせず空配列を返す
        }

        //[]の数だけ増やす
        $placeholders = implode(',', array_fill(0, count($this->food_products_id), '?'));

        /* SQL文生成 */
        /* 商品ID、商品名、商品画像、値段、*/
        $sql = "SELECT product.product_id ,product.product_name ,product_images.image_url ,product_attributes_options.price
                FROM product_attributes_options ,product_attributes ,product ,product_images
                WHERE product.product_id = product_attributes.product_id 
                AND product.product_id IN ($placeholders)
                AND product_attributes.variation_id = product_attributes_options.variation_id
                AND product_attributes_options.option_id = product_images.option_id
                GROUP BY product.product_id, product.product_name
                LIMIT 3;";
        $stmt = $pdo->prepare($sql);
       
        /* SQL文実行 */
        $ret = $stmt->execute($this->food_products_id);       
        /* 検索結果をtoppageListに登録 */
        foreach ($stmt as $row) {
                $Beans = new Beans();
            
                $Beans->setproduct_id($row['product_id']);
                $Beans->setproduct_name ($row['product_name']);
                $Beans->setimage_url ($row['image_url']);
                $Beans->setprice ($row['price']);

                $toppage_food_List[] = $Beans;
            }
       
        return $toppage_food_List;
    }

}
?>