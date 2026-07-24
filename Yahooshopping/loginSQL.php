<?php
header('Content-Type:text/html; charset=utf-8');

class loginSQL 
{
    public function select($pdo, $inBeans)
    {
        require_once('Beans.php');
    
        $List = array();
    
        /* 1. まず一般ユーザー（user_account）を検索 */
        $sql = 'SELECT * FROM user_account WHERE (phone_number = ? OR mail_address = ?) AND password = ?;';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $inBeans->getphone_number());
        $stmt->bindValue(2, $inBeans->getphone_number());
        $stmt->bindValue(3, $inBeans->getpassword());
        $stmt->execute();
    
        foreach ($stmt as $row) {
            $Beans = new Beans();
            if (method_exists($Beans, 'setuser_id'))      $Beans->setuser_id($row['user_id']);
            if (method_exists($Beans, 'setname'))         $Beans->setname($row['name']);
            if (method_exists($Beans, 'setphone_number')) $Beans->setphone_number($row['phone_number']);
            if (method_exists($Beans, 'setmail_address')) $Beans->setmail_address($row['mail_address']);
            if (method_exists($Beans, 'setpassword'))     $Beans->setpassword($row['password']);
            if (method_exists($Beans, 'setuser_name'))    $Beans->setuser_name($row['user_name']);
            
            // 一般ユーザーフラグ
            if (method_exists($Beans, 'setis_producer'))  $Beans->setis_producer(false);
        
            $List[] = $Beans;
            return $List; // 見つかったらここで終了
        }

        /* 2. 一般ユーザーになければ出品者（producer）を検索 */
        $sqlProducer = 'SELECT * FROM producer WHERE (phone_number = ? OR mail_address = ?) AND password = ?;';
        $stmtP = $pdo->prepare($sqlProducer);
        $stmtP->bindValue(1, $inBeans->getphone_number());
        $stmtP->bindValue(2, $inBeans->getphone_number());
        $stmtP->bindValue(3, $inBeans->getpassword());
        $stmtP->execute();

        foreach ($stmtP as $row) {
            $Beans = new Beans();
            // producerテーブルのカラム（producer_id, store_name）をBeansにセット
            if (method_exists($Beans, 'setuser_id'))      $Beans->setuser_id($row['producer_id']);
            if (method_exists($Beans, 'setname'))         $Beans->setname($row['store_name']); // 店舗名を名前として扱う
            if (method_exists($Beans, 'setphone_number')) $Beans->setphone_number($row['phone_number']);
            if (method_exists($Beans, 'setmail_address')) $Beans->setmail_address($row['mail_address']);
            if (method_exists($Beans, 'setpassword'))     $Beans->setpassword($row['password']);
            
            // 出品者フラグを true に！
            if (method_exists($Beans, 'setis_producer'))  $Beans->setis_producer(true);

            $List[] = $Beans;
            return $List;
        }
    
        return $List; // どちらにもいなければ空配列
    } 
}
?>