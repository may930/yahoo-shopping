<?php
    class CategorySQL{

        //食べ物
        private $parent_food_id = [3,4,6,7,8,9,10,11,12]; ///product_idを列挙
        public function parent_food_puroduct($pdo){
            require_once('Beans.php');       
            $category_food_List = array();
    
            if (empty($this->parent_food_id)) {
                return $category_food_List; // 配列が空なら何もせず空配列を返す
            }
    
            //[]の数だけ増やす
            $placeholders = implode(',', array_fill(0, count($this->parent_food_id), '?'));
    
            /* SQL文生成 */
            /* 商品ID、商品名、商品画像、値段、*/
            $sql = "SELECT product.product_id ,product.product_name ,product_images.image_url ,product_attributes_options.price
                    FROM product_attributes_options ,product_attributes ,product ,product_images
                    WHERE product.product_id = product_attributes.product_id 
                    AND product.product_id IN ($placeholders)
                    AND product_attributes.variation_id = product_attributes_options.variation_id
                    AND product_attributes_options.option_id = product_images.option_id
                    GROUP BY product.product_id, product.product_name;";
            $stmt = $pdo->prepare($sql);
           
            /* SQL文実行 */
            $ret = $stmt->execute($this->parent_food_id);       
            foreach ($stmt as $row) {
                    $Beans = new Beans();
                
                    $Beans->setproduct_id($row['product_id']);
                    $Beans->setproduct_name ($row['product_name']);
                    $Beans->setimage_url ($row['image_url']);
                    $Beans->setprice ($row['price']);
    
                    $category_food_List[] = $Beans;
                }
           
            return $category_food_List;    
        }
        //食べ物(惣菜)
        private $child_cook_id = [3]; ///product_idを列挙
        public function child_cook_puroduct($pdo){
            require_once('Beans.php');       
            $category_cooking_List = array();
    
            if (empty($this->child_cook_id)) {
                return $category_cooking_List; // 配列が空なら何もせず空配列を返す
            }
    
            //[]の数だけ増やす
            $placeholders = implode(',', array_fill(0, count($this->child_cook_id), '?'));
    
            /* SQL文生成 */
            /* 商品ID、商品名、商品画像、値段、*/
            $sql = "SELECT product.product_id ,product.product_name ,product_images.image_url ,product_attributes_options.price
                    FROM product_attributes_options ,product_attributes ,product ,product_images
                    WHERE product.product_id = product_attributes.product_id 
                    AND product.product_id IN ($placeholders)
                    AND product_attributes.variation_id = product_attributes_options.variation_id
                    AND product_attributes_options.option_id = product_images.option_id
                    GROUP BY product.product_id, product.product_name;";
            $stmt = $pdo->prepare($sql);
            
            /* SQL文実行 */
            $ret = $stmt->execute($this->child_cook_id);       
            foreach ($stmt as $row) {
                    $Beans = new Beans();
                
                    $Beans->setproduct_id($row['product_id']);
                    $Beans->setproduct_name ($row['product_name']);
                    $Beans->setimage_url ($row['image_url']);
                    $Beans->setprice ($row['price']);
    
                    $category_cooking_List[] = $Beans;
                }
            
            return $category_cooking_List;    
        }
        //食べ物(米、雑穀、粉類)
        private $child_grain_id = [6]; ///product_idを列挙
        public function child_grain_puroduct($pdo){
            require_once('Beans.php');       
            $category_grain_List = array();
    
            if (empty($this->child_grain_id)) {
                return $category_grain_List; // 配列が空なら何もせず空配列を返す
            }
    
            //[]の数だけ増やす
            $placeholders = implode(',', array_fill(0, count($this->child_grain_id), '?'));
    
            /* SQL文生成 */
            /* 商品ID、商品名、商品画像、値段、*/
            $sql = "SELECT product.product_id ,product.product_name ,product_images.image_url ,product_attributes_options.price
                    FROM product_attributes_options ,product_attributes ,product ,product_images
                    WHERE product.product_id = product_attributes.product_id 
                    AND product.product_id IN ($placeholders)
                    AND product_attributes.variation_id = product_attributes_options.variation_id
                    AND product_attributes_options.option_id = product_images.option_id
                    GROUP BY product.product_id, product.product_name;";
            $stmt = $pdo->prepare($sql);
            
            /* SQL文実行 */
            $ret = $stmt->execute($this->child_grain_id);       
            foreach ($stmt as $row) {
                    $Beans = new Beans();
                
                    $Beans->setproduct_id($row['product_id']);
                    $Beans->setproduct_name ($row['product_name']);
                    $Beans->setimage_url ($row['image_url']);
                    $Beans->setprice ($row['price']);
    
                    $category_grain_List[] = $Beans;
                }
            
            return $category_grain_List;    
        }
        //食べ物(スイーツ、洋菓子)
        private $child_sweets_id = [7,8,9]; ///product_idを列挙
        public function child_sweets_puroduct($pdo){
            require_once('Beans.php');       
            $category_sweets_List = array();
    
            if (empty($this->child_sweets_id)) {
                return $category_sweets_List; // 配列が空なら何もせず空配列を返す
            }
    
            //[]の数だけ増やす
            $placeholders = implode(',', array_fill(0, count($this->child_sweets_id), '?'));
    
            /* SQL文生成 */
            /* 商品ID、商品名、商品画像、値段、*/
            $sql = "SELECT product.product_id ,product.product_name ,product_images.image_url ,product_attributes_options.price
                    FROM product_attributes_options ,product_attributes ,product ,product_images
                    WHERE product.product_id = product_attributes.product_id 
                    AND product.product_id IN ($placeholders)
                    AND product_attributes.variation_id = product_attributes_options.variation_id
                    AND product_attributes_options.option_id = product_images.option_id
                    GROUP BY product.product_id, product.product_name;";
            $stmt = $pdo->prepare($sql);
            
            /* SQL文実行 */
            $ret = $stmt->execute($this->child_sweets_id);       
            foreach ($stmt as $row) {
                    $Beans = new Beans();
                
                    $Beans->setproduct_id($row['product_id']);
                    $Beans->setproduct_name ($row['product_name']);
                    $Beans->setimage_url ($row['image_url']);
                    $Beans->setprice ($row['price']);
    
                    $category_sweets_List[] = $Beans;
                }
            
            return $category_sweets_List;    
        }        
        //食べ物(魚介類、海産物)
        private $child_seafood_id = [10,11]; ///product_idを列挙
        public function child_seafood_puroduct($pdo){
            require_once('Beans.php');       
            $category_seafood_List = array();
    
            if (empty($this->child_seafood_id)) {
                return $category_seafood_List; // 配列が空なら何もせず空配列を返す
            }
    
            //[]の数だけ増やす
            $placeholders = implode(',', array_fill(0, count($this->child_seafood_id), '?'));
    
            /* SQL文生成 */
            /* 商品ID、商品名、商品画像、値段、*/
            $sql = "SELECT product.product_id ,product.product_name ,product_images.image_url ,product_attributes_options.price
                    FROM product_attributes_options ,product_attributes ,product ,product_images
                    WHERE product.product_id = product_attributes.product_id 
                    AND product.product_id IN ($placeholders)
                    AND product_attributes.variation_id = product_attributes_options.variation_id
                    AND product_attributes_options.option_id = product_images.option_id
                    GROUP BY product.product_id, product.product_name;";
            $stmt = $pdo->prepare($sql);
            
            /* SQL文実行 */
            $ret = $stmt->execute($this->child_seafood_id);       
            foreach ($stmt as $row) {
                    $Beans = new Beans();
                
                    $Beans->setproduct_id($row['product_id']);
                    $Beans->setproduct_name ($row['product_name']);
                    $Beans->setimage_url ($row['image_url']);
                    $Beans->setprice ($row['price']);
    
                    $category_seafood_List[] = $Beans;
                }
            
            return $category_seafood_List;    
        }
        //食べ物(肉、ハム、ソーセージ)
        private $child_meat_id = [12]; ///product_idを列挙
        public function child_meat_puroduct($pdo){
            require_once('Beans.php');       
            $category_meat_List = array();
    
            if (empty($this->child_meat_id)) {
                return $category_meat_List; // 配列が空なら何もせず空配列を返す
            }
    
            //[]の数だけ増やす
            $placeholders = implode(',', array_fill(0, count($this->child_meat_id), '?'));
    
            /* SQL文生成 */
            /* 商品ID、商品名、商品画像、値段、*/
            $sql = "SELECT product.product_id ,product.product_name ,product_images.image_url ,product_attributes_options.price
                    FROM product_attributes_options ,product_attributes ,product ,product_images
                    WHERE product.product_id = product_attributes.product_id 
                    AND product.product_id IN ($placeholders)
                    AND product_attributes.variation_id = product_attributes_options.variation_id
                    AND product_attributes_options.option_id = product_images.option_id
                    GROUP BY product.product_id, product.product_name;";
            $stmt = $pdo->prepare($sql);
            
            /* SQL文実行 */
            $ret = $stmt->execute($this->child_meat_id);       
            foreach ($stmt as $row) {
                    $Beans = new Beans();
                
                    $Beans->setproduct_id($row['product_id']);
                    $Beans->setproduct_name ($row['product_name']);
                    $Beans->setimage_url ($row['image_url']);
                    $Beans->setprice ($row['price']);
    
                    $category_meat_List[] = $Beans;
                }
            
            return $category_meat_List;    
        }
        

        

        //飲み物
        private $parent_drink_id = [1,2,5]; ///product_idを列挙
        public function parent_drink_puroduct($pdo){
            require_once('Beans.php');       
            $category_drink_List = array();
    
            if (empty($this->parent_drink_id)) {
                return $category_drink_List; // 配列が空なら何もせず空配列を返す
            }
    
            //[]の数だけ増やす
            $placeholders = implode(',', array_fill(0, count($this->parent_drink_id), '?'));
    
            /* SQL文生成 */
            /* 商品ID、商品名、商品画像、値段、*/
            $sql = "SELECT product.product_id ,product.product_name ,product_images.image_url ,product_attributes_options.price
                    FROM product_attributes_options ,product_attributes ,product ,product_images
                    WHERE product.product_id = product_attributes.product_id 
                    AND product.product_id IN ($placeholders)
                    AND product_attributes.variation_id = product_attributes_options.variation_id
                    AND product_attributes_options.option_id = product_images.option_id
                    GROUP BY product.product_id, product.product_name;";
            $stmt = $pdo->prepare($sql);
           
            /* SQL文実行 */
            $ret = $stmt->execute($this->parent_drink_id);       
            foreach ($stmt as $row) {
                    $Beans = new Beans();
                
                    $Beans->setproduct_id($row['product_id']);
                    $Beans->setproduct_name ($row['product_name']);
                    $Beans->setimage_url ($row['image_url']);
                    $Beans->setprice ($row['price']);
    
                    $category_drink_List[] = $Beans;
                }
           
            return $category_drink_List;    
        }
        //飲み物(水、お茶、清涼飲料水)
        private $child_softdrink_id = [2,5]; ///product_idを列挙
        public function child_softdrinks_puroduct($pdo){
            require_once('Beans.php');       
            $category_softdrink_List = array();
    
            if (empty($this->child_softdrink_id)) {
                return $category_softdrink_List; // 配列が空なら何もせず空配列を返す
            }
    
            //[]の数だけ増やす
            $placeholders = implode(',', array_fill(0, count($this->child_softdrink_id), '?'));
    
            /* SQL文生成 */
            /* 商品ID、商品名、商品画像、値段、*/
            $sql = "SELECT product.product_id ,product.product_name ,product_images.image_url ,product_attributes_options.price
                    FROM product_attributes_options ,product_attributes ,product ,product_images
                    WHERE product.product_id = product_attributes.product_id 
                    AND product.product_id IN ($placeholders)
                    AND product_attributes.variation_id = product_attributes_options.variation_id
                    AND product_attributes_options.option_id = product_images.option_id
                    GROUP BY product.product_id, product.product_name;";
            $stmt = $pdo->prepare($sql);
            
            /* SQL文実行 */
            $ret = $stmt->execute($this->child_softdrink_id);       
            foreach ($stmt as $row) {
                    $Beans = new Beans();
                
                    $Beans->setproduct_id($row['product_id']);
                    $Beans->setproduct_name ($row['product_name']);
                    $Beans->setimage_url ($row['image_url']);
                    $Beans->setprice ($row['price']);
    
                    $category_softdrink_List[] = $Beans;
                }
            
            return $category_softdrink_List;    
        }
        //飲み物(お酒)
        private $child_alcohol_id = [1]; ///product_idを列挙
        public function child_alcohol_puroduct($pdo){
            require_once('Beans.php');       
            $category_alcohol_List = array();
    
            if (empty($this->child_alcohol_id)) {
                return $category_alcohol_List; // 配列が空なら何もせず空配列を返す
            }
    
            //[]の数だけ増やす
            $placeholders = implode(',', array_fill(0, count($this->child_alcohol_id), '?'));
    
            /* SQL文生成 */
            /* 商品ID、商品名、商品画像、値段、*/
            $sql = "SELECT product.product_id ,product.product_name ,product_images.image_url ,product_attributes_options.price
                    FROM product_attributes_options ,product_attributes ,product ,product_images
                    WHERE product.product_id = product_attributes.product_id 
                    AND product.product_id IN ($placeholders)
                    AND product_attributes.variation_id = product_attributes_options.variation_id
                    AND product_attributes_options.option_id = product_images.option_id
                    GROUP BY product.product_id, product.product_name;";
            $stmt = $pdo->prepare($sql);
            
            /* SQL文実行 */
            $ret = $stmt->execute($this->child_alcohol_id);       
            foreach ($stmt as $row) {
                    $Beans = new Beans();
                
                    $Beans->setproduct_id($row['product_id']);
                    $Beans->setproduct_name ($row['product_name']);
                    $Beans->setimage_url ($row['image_url']);
                    $Beans->setprice ($row['price']);
    
                    $category_alcohol_List[] = $Beans;
                }
            
            return $category_alcohol_List;    
        }
        
        //レディース
        private $parent_ladies_id = [17,18,19,20,21]; ///product_idを列挙
        public function parent_ladies_puroduct($pdo){
            require_once('Beans.php');       
            $category_ladies_List = array();
    
            if (empty($this->parent_ladies_id)) {
                return $category_ladies_List; // 配列が空なら何もせず空配列を返す
            }
    
            //[]の数だけ増やす
            $placeholders = implode(',', array_fill(0, count($this->parent_ladies_id), '?'));
    
            /* SQL文生成 */
            /* 商品ID、商品名、商品画像、値段、*/
            $sql = "SELECT product.product_id ,product.product_name ,product_images.image_url ,product_attributes_options.price
                    FROM product_attributes_options ,product_attributes ,product ,product_images
                    WHERE product.product_id = product_attributes.product_id 
                    AND product.product_id IN ($placeholders)
                    AND product_attributes.variation_id = product_attributes_options.variation_id
                    AND product_attributes_options.option_id = product_images.option_id
                    GROUP BY product.product_id, product.product_name;";
            $stmt = $pdo->prepare($sql);
           
            /* SQL文実行 */
            $ret = $stmt->execute($this->parent_ladies_id);       
            foreach ($stmt as $row) {
                    $Beans = new Beans();
                
                    $Beans->setproduct_id($row['product_id']);
                    $Beans->setproduct_name ($row['product_name']);
                    $Beans->setimage_url ($row['image_url']);
                    $Beans->setprice ($row['price']);
    
                    $category_ladies_List[] = $Beans;
                }
           
            return $category_ladies_List;    
        }

        //メンズ
        private $parent_mens_id = [13,14,15,16]; ///product_idを列挙
        public function parent_mens_puroduct($pdo){
            require_once('Beans.php');       
            $category_mens_List = array();
    
            if (empty($this->parent_mens_id)) {
                return $category_mens_List; // 配列が空なら何もせず空配列を返す
            }
    
            //[]の数だけ増やす
            $placeholders = implode(',', array_fill(0, count($this->parent_mens_id), '?'));
    
            /* SQL文生成 */
            /* 商品ID、商品名、商品画像、値段、*/
            $sql = "SELECT product.product_id ,product.product_name ,product_images.image_url ,product_attributes_options.price
                    FROM product_attributes_options ,product_attributes ,product ,product_images
                    WHERE product.product_id = product_attributes.product_id 
                    AND product.product_id IN ($placeholders)
                    AND product_attributes.variation_id = product_attributes_options.variation_id
                    AND product_attributes_options.option_id = product_images.option_id
                    GROUP BY product.product_id, product.product_name;";
            $stmt = $pdo->prepare($sql);
           
            /* SQL文実行 */
            $ret = $stmt->execute($this->parent_mens_id);       
            foreach ($stmt as $row) {
                    $Beans = new Beans();
                
                    $Beans->setproduct_id($row['product_id']);
                    $Beans->setproduct_name ($row['product_name']);
                    $Beans->setimage_url ($row['image_url']);
                    $Beans->setprice ($row['price']);
    
                    $category_mens_List[] = $Beans;
                }
           
            return $category_mens_List;    
        }

        //アクセサリー
        private $parent_accessories_id = [4]; ///product_idを列挙
        public function parent_accessories_puroduct($pdo){
            require_once('Beans.php');       
            $category_accessories_List = array();
    
            if (empty($this->parent_accessories_id)) {
                return $category_accessories_List; // 配列が空なら何もせず空配列を返す
            }
    
            //[]の数だけ増やす
            $placeholders = implode(',', array_fill(0, count($this->parent_accessories_id), '?'));
    
            /* SQL文生成 */
            /* 商品ID、商品名、商品画像、値段、*/
            $sql = "SELECT product.product_id ,product.product_name ,product_images.image_url ,product_attributes_options.price
                    FROM product_attributes_options ,product_attributes ,product ,product_images
                    WHERE product.product_id = product_attributes.product_id 
                    AND product.product_id IN ($placeholders)
                    AND product_attributes.variation_id = product_attributes_options.variation_id
                    AND product_attributes_options.option_id = product_images.option_id
                    GROUP BY product.product_id, product.product_name;";
            $stmt = $pdo->prepare($sql);
           
            /* SQL文実行 */
            $ret = $stmt->execute($this->parent_accessories_id);       
            foreach ($stmt as $row) {
                    $Beans = new Beans();
                
                    $Beans->setproduct_id($row['product_id']);
                    $Beans->setproduct_name ($row['product_name']);
                    $Beans->setimage_url ($row['image_url']);
                    $Beans->setprice ($row['price']);
    
                    $category_accessories_List[] = $Beans;
                }
           
            return $category_accessories_List;    
        }
        //アクセサリー（ネックレス）
        private $child_accessories_necklace_id = [9]; ///product_idを列挙
        public function child_accessories_necklace_puroduct($pdo){
            require_once('Beans.php');       
            $category_accessories_necklace_List = array();
    
            if (empty($this->child_accessories_necklace_id)) {
                return $category_accessories_necklace_List; // 配列が空なら何もせず空配列を返す
            }
    
            //[]の数だけ増やす
            $placeholders = implode(',', array_fill(0, count($this->child_accessories_necklace_id), '?'));
    
            /* SQL文生成 */
            /* 商品ID、商品名、商品画像、値段、*/
            $sql = "SELECT product.product_id ,product.product_name ,product_images.image_url ,product_attributes_options.price
                    FROM product_attributes_options ,product_attributes ,product ,product_images
                    WHERE product.product_id = product_attributes.product_id 
                    AND product.product_id IN ($placeholders)
                    AND product_attributes.variation_id = product_attributes_options.variation_id
                    AND product_attributes_options.option_id = product_images.option_id
                    GROUP BY product.product_id, product.product_name;";
            $stmt = $pdo->prepare($sql);
            
            /* SQL文実行 */
            $ret = $stmt->execute($this->child_accessories_necklace_id);       
            foreach ($stmt as $row) {
                    $Beans = new Beans();
                
                    $Beans->setproduct_id($row['product_id']);
                    $Beans->setproduct_name ($row['product_name']);
                    $Beans->setimage_url ($row['image_url']);
                    $Beans->setprice ($row['price']);
    
                    $category_accessories_necklace_List[] = $Beans;
                }
            
            return $category_accessories_necklace_List;    
        }        
        //アクセサリー（指輪）
        private $child_accessories_ring_id = [5]; ///product_idを列挙
        public function child_accessories_ring_puroduct($pdo){
            require_once('Beans.php');       
            $category_accessories_ring_List = array();
    
            if (empty($this->child_accessories_ring_id)) {
                return $category_accessories_ring_List; // 配列が空なら何もせず空配列を返す
            }
    
            //[]の数だけ増やす
            $placeholders = implode(',', array_fill(0, count($this->child_accessories_ring_id), '?'));
    
            /* SQL文生成 */
            /* 商品ID、商品名、商品画像、値段、*/
            $sql = "SELECT product.product_id ,product.product_name ,product_images.image_url ,product_attributes_options.price
                    FROM product_attributes_options ,product_attributes ,product ,product_images
                    WHERE product.product_id = product_attributes.product_id 
                    AND product.product_id IN ($placeholders)
                    AND product_attributes.variation_id = product_attributes_options.variation_id
                    AND product_attributes_options.option_id = product_images.option_id
                    GROUP BY product.product_id, product.product_name;";
            $stmt = $pdo->prepare($sql);
           
            /* SQL文実行 */
            $ret = $stmt->execute($this->child_accessories_ring_id);       
            foreach ($stmt as $row) {
                    $Beans = new Beans();
                
                    $Beans->setproduct_id($row['product_id']);
                    $Beans->setproduct_name ($row['product_name']);
                    $Beans->setimage_url ($row['image_url']);
                    $Beans->setprice ($row['price']);
    
                    $category_accessories_ring_List[] = $Beans;
                }
           
            return $category_accessories_ring_List;    
        }        
        //アクセサリー（ピアス）
        private $child_accessories_earrings_id = [6]; ///product_idを列挙
        public function child_accessories_earrings_puroduct($pdo){
            require_once('Beans.php');       
            $category_accessories_earrings_List = array();
    
            if (empty($this->child_accessories_earrings_id)) {
                return $category_accessories_earrings_List; // 配列が空なら何もせず空配列を返す
            }
    
            //[]の数だけ増やす
            $placeholders = implode(',', array_fill(0, count($this->child_accessories_earrings_id), '?'));
    
            /* SQL文生成 */
            /* 商品ID、商品名、商品画像、値段、*/
            $sql = "SELECT product.product_id ,product.product_name ,product_images.image_url ,product_attributes_options.price
                    FROM product_attributes_options ,product_attributes ,product ,product_images
                    WHERE product.product_id = product_attributes.product_id 
                    AND product.product_id IN ($placeholders)
                    AND product_attributes.variation_id = product_attributes_options.variation_id
                    AND product_attributes_options.option_id = product_images.option_id
                    GROUP BY product.product_id, product.product_name;";
            $stmt = $pdo->prepare($sql);
           
            /* SQL文実行 */
            $ret = $stmt->execute($this->child_accessories_earrings_id);       
            foreach ($stmt as $row) {
                    $Beans = new Beans();
                
                    $Beans->setproduct_id($row['product_id']);
                    $Beans->setproduct_name ($row['product_name']);
                    $Beans->setimage_url ($row['image_url']);
                    $Beans->setprice ($row['price']);
    
                    $category_accessories_earrings_List[] = $Beans;
                }
           
            return $category_accessories_earrings_List;    
        }


        //子供用
        private $parent_kids_id = [4]; ///product_idを列挙
        public function parent_kids_puroduct($pdo){
            require_once('Beans.php');       
            $category_kids_List = array();
    
            if (empty($this->parent_kids_id)) {
                return $category_kids_List; // 配列が空なら何もせず空配列を返す
            }
    
            //[]の数だけ増やす
            $placeholders = implode(',', array_fill(0, count($this->parent_kids_id), '?'));
    
            /* SQL文生成 */
            /* 商品ID、商品名、商品画像、値段、*/
            $sql = "SELECT product.product_id ,product.product_name ,product_images.image_url ,product_attributes_options.price
                    FROM product_attributes_options ,product_attributes ,product ,product_images
                    WHERE product.product_id = product_attributes.product_id 
                    AND product.product_id IN ($placeholders)
                    AND product_attributes.variation_id = product_attributes_options.variation_id
                    AND product_attributes_options.option_id = product_images.option_id
                    GROUP BY product.product_id, product.product_name;";
            $stmt = $pdo->prepare($sql);
           
            /* SQL文実行 */
            $ret = $stmt->execute($this->parent_kids_id);       
            foreach ($stmt as $row) {
                    $Beans = new Beans();
                
                    $Beans->setproduct_id($row['product_id']);
                    $Beans->setproduct_name ($row['product_name']);
                    $Beans->setimage_url ($row['image_url']);
                    $Beans->setprice ($row['price']);
    
                    $category_kids_List[] = $Beans;
                }
           
            return $category_kids_List;    
        }


    }
?>
