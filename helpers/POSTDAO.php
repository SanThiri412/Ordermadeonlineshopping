<?php
require_once __DIR__ . '/DAO.php';

class PostDAO {
    public function findByGoodsCode(string $goodsCode) {
        $dbh = DAO::get_db_connect();
        
        // mail.php で表示に必要なカラム（goodsText, image等）を追加
        $sql = "SELECT 
                    goodsCode, 
                    member_id, 
                    goodsName, 
                    goodsText, 
                    image 
                FROM post 
                WHERE goodsCode = :goodsCode";
                
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':goodsCode', $goodsCode, PDO::PARAM_STR);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // 見つかった場合はオブジェクトにキャストして返す
        return $row ? (object)$row : false;
    }
}