<?php
header('Content-Type:text/plain; charset=utf-8');

class cartAQL
{
    public function select($pdo,$inBeans)
    {
        require_once('Beans.php');
    
        $List = array();
    
        /* SQL文生成 */
        $sql = 'SELECT * FROM user_account WHERE phone_number = ? or mail_address = ?;';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $inBeans->getphone_number());
        $stmt->bindValue(2, $inBeans->getphone_number());
    
        /* SQL文実行 */
        $ret = $stmt->execute();
    
        /* 検索結果をArrayListに登録 */
        foreach ($stmt as $row) 
        {
            $Beans = new Beans();
        
            $Beans->setphone_number($row['phone_number']);
        
            $List[] = $Beans; // $Listに登録する
        }
    
        return $List;
    } 
}
?>