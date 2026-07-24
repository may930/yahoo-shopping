<?php
header('Content-Type:text/plain; charset=utf-8');
class account_registrationSQL 
{
    public function insert($pdo,$inBeans) 
    {
        $recCount = 0;
        /* SQL文生成 */
        $sql = 'INSERT INTO user_account (user_id, name, name_kana, phone_number, mail_address, password, user_name, sex, birthday, zipcode, address, created_at, deleted_at) 
                    VALUES (null, ??, ??, ?, ?, ? ,?, ?, ?, ?, ?, current_timestamp, null);';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $inBeans->getname_first());
        $stmt->bindValue(2, $inBeans->getname_second());
        $stmt->bindValue(3, $inBeans->getname_kana_first());
        $stmt->bindValue(4, $inBeans->getname_kana_second());
        $stmt->bindValue(5, $inBeans->getphone_number());
        $stmt->bindValue(6, $inBeans->getmail_address());
        $stmt->bindValue(7, $inBeans->getpassword());
        $stmt->bindValue(8, $inBeans->getsex());
        $stmt->bindValue(9, $inBeans->getbirthday());
        $stmt->bindValue(10, $inBeans->getzipcode());
        $stmt->bindValue(11, $inBeans->getaddress());

        try 
        {
            /* SQL文実行 */
            $ret = $stmt->execute();
            /* 件数を取得 */
            $recCount = $stmt->rowCount();
        } 
        catch (PDOException $e) {}
        return $recCount;
    }
}
   ?> 