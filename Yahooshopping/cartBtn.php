<?php
/* インポート */
require_once('cartSQL.php');
require_once('utilCommDB.php');
require_once('Beans.php');

/* インスタンス生成 */
$cartSQL    = new CartSQL();
$utilConnDB = new UtilCommDB();
$Beans      = new Beans();

/* DB接続 */
$pdo = $utilConnDB->connect();

/* ボタン判定と数量の加減算処理*/
if (isset($_POST['cart_id'])) 
{
    $cart_id     = (int)$_POST['cart_id'];
    $current_qty = (int)$_POST['current_quantity'];

    if (isset($_POST['btn-plus'])) // 【プラスボタン】
    {
        $new_qty = $current_qty + 1;
        if ($new_qty <= 99) // 上限99個
        { 
            $cartSQL->updateQuantity($pdo, $cart_id, $new_qty);
        }
    } 
    elseif (isset($_POST['btn-minus'])) // 【マイナスボタン】
    {
        $new_qty = $current_qty - 1;
        if ($new_qty >= 1) // 下限1個
        { 
            $cartSQL->updateQuantity($pdo, $cart_id, $new_qty);
        }
    }
}

/* 最新のカート情報を取得して再計算*/
$List = $cartSQL->select($pdo, $Beans);

$total_price = 0;
foreach ($List as $item) 
{
    $subtotal = $item->getprice() * $item->getquantity();
    $total_price += $subtotal;
}

/* DB切断 */
$utilConnDB->disconnect($pdo);
?>