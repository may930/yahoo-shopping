<?php
header('Content-Type:text/html; charset=utf-8');
require_once('Beans.php');

class MypageSQL 
{
    public function selectById($pdo, $userId)
    {
        if (empty($userId) || !$pdo) 
        {
            return null;
        }

        /*まず一般ユーザー（user_account）を検索 */
        $sql = 'SELECT * FROM user_account WHERE user_id = ?;';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_STR);
        $stmt->execute();
    
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
        {
            $Beans = new Beans();
            if (method_exists($Beans, 'setuser_id'))      $Beans->setuser_id($row['user_id'] ?? '');
            if (method_exists($Beans, 'setname'))         $Beans->setname($row['name'] ?? '');
            if (method_exists($Beans, 'setname_first'))   $Beans->setname_first($row['name'] ?? ''); // 画面表示用
            if (method_exists($Beans, 'setmail_address')) $Beans->setmail_address($row['mail_address'] ?? '');
            if (method_exists($Beans, 'setzipcode'))      $Beans->setzipcode($row['zipcode'] ?? '');   // 郵便番号
            if (method_exists($Beans, 'setaddress'))      $Beans->setaddress($row['address'] ?? '');   // 住所
            if (method_exists($Beans, 'setphone_number')) $Beans->setphone_number($row['phone_number'] ?? '');
            
            // 一般ユーザーフラグ
            if (method_exists($Beans, 'setis_producer'))  $Beans->setis_producer(false);
        
            return $Beans; // 一般ユーザーが見つかったら返却
        }

        /*一般ユーザーになければ出品者（producer）を検索 */
        $sqlProducer = 'SELECT * FROM producer WHERE producer_id = ?;';
        $stmtP = $pdo->prepare($sqlProducer);
        $stmtP->bindValue(1, $userId, PDO::PARAM_STR);
        $stmtP->execute();

        if ($row = $stmtP->fetch(PDO::FETCH_ASSOC)) 
        {
            $Beans = new Beans();
            if (method_exists($Beans, 'setuser_id'))      $Beans->setuser_id($row['producer_id'] ?? '');
            if (method_exists($Beans, 'setproducer_id'))  $Beans->setproducer_id($row['producer_id'] ?? '');
            if (method_exists($Beans, 'setstore_name'))   $Beans->setstore_name($row['store_name'] ?? '');
            if (method_exists($Beans, 'setname'))         $Beans->setname($row['name'] ?? $row['store_name'] ?? '');
            if (method_exists($Beans, 'setname_first'))   $Beans->setname_first($row['name'] ?? $row['store_name'] ?? '');
            if (method_exists($Beans, 'setmail_address')) $Beans->setmail_address($row['mail_address'] ?? '');
            if (method_exists($Beans, 'setzipcode'))      $Beans->setzipcode($row['zipcode'] ?? '');   // 郵便番号
            if (method_exists($Beans, 'setaddress'))      $Beans->setaddress($row['address'] ?? '');   // 住所
            if (method_exists($Beans, 'setphone_number')) $Beans->setphone_number($row['phone_number'] ?? '');
            
            // 出品者フラグを true に
            if (method_exists($Beans, 'setis_producer'))  $Beans->setis_producer(true);

            return $Beans; // 出品者が見つかったら返却
        }
    
        return null; // どちらにもいなければ null
    } 
}
?>