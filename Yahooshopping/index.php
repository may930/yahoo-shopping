<?php
/*
  index.php - ロジック・データ取得担当
*/

// 1. セッション開始（ログイン状態の判別用）
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. データベース接続ファイルを読み込む
require_once 'db.php';

try {
    /* 
       売れ筋人気ランキング用データ取得
    */
    $sql = "SELECT 
                p.product_id,
                p.product_name,
                p.information,
                p.category_id,
                IFNULL(MIN(o.option_id), 1) AS option_id,
                IFNULL(MIN(o.price), 0) AS price,
                img.image_url
            FROM product AS p
            LEFT JOIN product_attributes_options AS o ON p.product_id = o.variation_id
            LEFT JOIN product_images AS img ON o.option_id = img.option_id AND img.display_order = 1
            GROUP BY p.product_id
            LIMIT 3";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $ranking_products = $stmt->fetchAll();

    /*
       ☀️ 夏対策特集（category_id = 69）から3つ取得
    */
    $sql_summer = "SELECT 
                        p.product_id, 
                        p.product_name, 
                        p.information,
                        o.option_id,
                        o.price,
                        i.image_url
                   FROM product AS p
                   INNER JOIN product_attributes_options AS o ON p.product_id = o.variation_id
                   LEFT JOIN product_images AS i ON o.option_id = i.option_id
                   WHERE p.category_id = 69
                   GROUP BY p.product_id
                   ORDER BY RAND()
                   LIMIT 3";
                   
    $stmt_summer = $pdo->query($sql_summer);
    $summer_products = $stmt_summer->fetchAll();

} catch (PDOException $e) {
    exit('データ取得に失敗しました: ' . $e->getMessage());
}

// 3. 表示用のビューファイルを呼び出す
include 'index_view.php';