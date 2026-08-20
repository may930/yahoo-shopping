<?php
class AdminOrdersSQL 
{
    // 1. ログイン中の出品者の商品が含まれる注文と詳細を取得
    public function selectOrdersByProducer($pdo, $producer_id)
    {
        $sql = 'SELECT h.*, d.product_name, d.option_name, d.quantity, d.price 
                FROM order_history h
                JOIN order_details d ON h.order_id = d.order_id
                JOIN product p ON d.product_name = p.product_name
                WHERE p.producer_id = ?
                ORDER BY h.order_date DESC;';
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $producer_id);
        $stmt->execute();
        
        $List = array();
        foreach ($stmt as $row) {
            $List[] = $row;
        }
        return $List;
    }

    // 2. 注文の発送ステータスを「shipped」に更新
    public function updateShippingStatus($pdo, $order_id)
    {
        $sql = 'UPDATE order_history SET shipping_status = "shipped" WHERE order_id = ?;';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $order_id);
        return $stmt->execute();
    }
}
?>