<?php
session_start();

if (!isset($_SESSION['user']) || !$_SESSION['user']['is_producer']) {
    header('Location: login_view.php');
    exit();
}

require_once('adminOrdersSQL.php');
require_once('utilConnDB.php');

$producer_id = $_SESSION['user']['producer_id'];

$utilConnDB = new UtilConnDB();
$pdo = $utilConnDB->connect();
$ordersSQL = new AdminOrdersSQL();

// 「発送する」ボタンが押されたときの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['shipping_order_id'])) {
    $target_order_id = $_POST['shipping_order_id'];
    
    // ステータス更新を実行
    $ordersSQL->updateShippingStatus($pdo, $target_order_id);
    
    // データベースの変更を確定（コミット）する
    $utilConnDB->commit($pdo);
    
    // 画面をリロードして反映
    header('Location: admin_orders.php');
    exit();
}

// 注文と詳細の一覧を取得
$orderList = $ordersSQL->selectOrdersByProducer($pdo, $producer_id);

$utilConnDB->disconnect($pdo);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>注文管理 - <?php echo htmlspecialchars($_SESSION['user']['store_name'], ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        body { font-family: sans-serif; margin: 20px; background-color: #f9f9f9; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; font-size: 14px; }
        th { background-color: #f2f2f2; }
        .btn-ship { background-color: #28a745; color: white; border: none; padding: 6px 12px; cursor: pointer; border-radius: 4px; }
        .btn-ship:hover { background-color: #218838; }
        .status-shipped { color: #007bff; font-weight: bold; }
        .back-link { display: inline-block; margin-bottom: 15px; text-decoration: none; color: #0066cc; }
        .product-info { text-align: left; }
    </style>
</head>
<body>

    <a href="admin_top.php" class="back-link">← 管理画面トップへ戻る</a>
    <h1>📦 注文管理画面</h1>
    <p>あなたの店舗の商品が含まれる注文一覧です。「発送する」ボタンを押すと、ステータスが「発送済み」に更新されます。</p>

    <?php if (empty($orderList)): ?>
        <p>現在、注文はありません。</p>
    <?php else: ?>
        <table>
            <tr>
                <th>注文ID</th>
                <th>注文日時</th>
                <th>購入者名</th>
                <th>購入商品 / オプション</th>
                <th>個数</th>
                <th>金額</th>
                <th>決済方法</th>
                <th>発送ステータス</th>
                <th>操作</th>
            </tr>
            <?php foreach ($orderList as $order): ?>
            <tr>
                <td><?php echo htmlspecialchars($order['order_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($order['order_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($order['shipping_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="product-info">
                    <strong><?php echo htmlspecialchars($order['product_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    <?php if (!empty($order['option_name'])): ?>
                        <br><small style="color: #666;">(<?php echo htmlspecialchars($order['option_name'], ENT_QUOTES, 'UTF-8'); ?>)</small>
                    <?php endif; ?>
                </td>
                <td><strong><?php echo htmlspecialchars($order['quantity'], ENT_QUOTES, 'UTF-8'); ?>個</strong></td>
                <td><?php echo number_format($order['price']); ?>円</td>
                <td><?php echo htmlspecialchars($order['pay'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <?php if ($order['shipping_status'] === 'shipped' || $order['shipping_status'] === 'delicvered'): ?>
                        <span class="status-shipped">発送済み (<?php echo htmlspecialchars($order['shipping_status'], ENT_QUOTES, 'UTF-8'); ?>)</span>
                    <?php else: ?>
                        <span style="color: #dc3545; font-weight: bold;">未発送 (<?php echo htmlspecialchars($order['shipping_status'], ENT_QUOTES, 'UTF-8'); ?>)</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($order['shipping_status'] !== 'shipped' && $order['shipping_status'] !== 'delicvered'): ?>
                        <form method="POST" style="margin: 0;">
                            <input type="hidden" name="shipping_order_id" value="<?php echo $order['order_id']; ?>">
                            <button type="submit" class="btn-ship">発送する</button>
                        </form>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

</body>
</html>
