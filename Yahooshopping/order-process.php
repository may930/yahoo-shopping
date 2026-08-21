<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login_view.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit();
}

$shipping_name = trim($_POST['shipping_name'] ?? '');
$zipcode       = trim($_POST['zipcode'] ?? '');
$address       = trim($_POST['address'] ?? '');
$paymentMethod = $_POST['paymentMethod'] ?? '';

if (empty($shipping_name) || empty($zipcode) || empty($address) || empty($paymentMethod)) {
    header('Location: checkout.php');
    exit();
}

$payment_labels = [
    'credit' => 'クレジットカード',
    'paypay' => 'PayPay',
    'cod'    => '代金引換',
];
$payment_label = $payment_labels[$paymentMethod] ?? $paymentMethod;

$user_id = $_SESSION['user']['id'] ?? null;

require_once('cartSQL.php');
require_once('utilConnDB.php');

$cartSQL    = new CartSQL();
$utilConnDB = new UtilConnDB();
$pdo        = $utilConnDB->connect();

// カートの中身をDBから再取得（ここでも改めて価格を検証）
$cart_session = $_SESSION['cart'] ?? [];
$option_ids   = array_keys($cart_session);

$cart_items = [];
if (!empty($option_ids)) {
    $cart_items = $cartSQL->selectCartItems($pdo, $option_ids);
}

if (empty($cart_items) || empty($user_id)) {
    $utilConnDB->disconnect($pdo);
    header('Location: cart.php');
    exit();
}

$total_price = 0;
foreach ($cart_items as $item) {
    $quantity = $cart_session[$item['option_id']] ?? 1;
    $total_price += $item['price'] * $quantity;
}

try {
    // 1. order_history に登録
    $sql = "INSERT INTO order_history 
                (user_id, shipping_name, zipcode, address, order_date, coupon_id, coupon_discount_amount, total_price, pay, shipping_status)
            VALUES 
                (:user_id, :shipping_name, :zipcode, :address, NOW(), NULL, 0, :total_price, :pay, 'ordered')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'user_id'       => $user_id,
        'shipping_name' => $shipping_name,
        'zipcode'       => $zipcode,
        'address'       => $address,
        'total_price'   => $total_price,
        'pay'           => $payment_label,
    ]);

    $order_id = $pdo->lastInsertId();

    // 2. order_details に商品明細を登録
    $sql_detail = "INSERT INTO order_details 
                        (order_id, option_id, product_name, option_name, quantity, price)
                   VALUES 
                        (:order_id, :option_id, :product_name, :option_name, :quantity, :price)";
    $stmt_detail = $pdo->prepare($sql_detail);

    foreach ($cart_items as $item) {
        $quantity = $cart_session[$item['option_id']] ?? 1;
        $stmt_detail->execute([
            'order_id'     => $order_id,
            'option_id'    => $item['option_id'],
            'product_name' => $item['product_name'],
            'option_name'  => $item['option_name'],
            'quantity'     => $quantity,
            'price'        => $item['price'],
        ]);
    }

    $pdo->commit();

    // 3. カートを空にする
    $_SESSION['cart'] = [];

    $utilConnDB->disconnect($pdo);

    header('Location: order-complete.php?order_id=' . $order_id);
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    $utilConnDB->disconnect($pdo);
    exit('注文処理中にエラーが発生しました。時間を置いて再度お試しください。');
}