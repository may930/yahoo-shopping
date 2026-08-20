<?php
// クラス名をファイル名や呼び出し元に合わせて cartSQL に（※もし修正が必要なら）
class CartSQL
{
    // セッション等に入っている option_id の配列（または個別）から商品情報を取得する
    public function selectCartItems($pdo, $option_ids)
    {
        if (empty($option_ids)) {
            return [];
        }

        // プレースホルダーを動的に生成（IN句用）
        $placeholders = implode(',', array_fill(0, count($option_ids), '?'));

        // 商品名、オプション名、価格、画像、在庫、オプションIDを取得するSQL
        $sql = "SELECT 
                    o.option_id,
                    o.option_name,
                    o.price,
                    o.stock,
                    p.product_id,
                    p.product_name,
                    i.image_url
                FROM product_attributes_options AS o
                JOIN product_attributes AS pa ON o.variation_id = pa.variation_id
                JOIN product AS p ON pa.product_id = p.product_id
                LEFT JOIN product_images AS i ON o.option_id = i.option_id
                WHERE o.option_id IN ($placeholders)
                GROUP BY o.option_id";

        $stmt = $pdo->prepare($sql);
$stmt->execute($option_ids);

return $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>