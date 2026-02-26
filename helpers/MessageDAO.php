<?php
require_once 'DAO.php';

class Message {
    public int $message_id;
    public int $member_id;      // 送信者ID
    public int $to_member_id;   // 受信者ID
    public int $goodscode;      // 商品コード (GoodsテーブルのgoodsCode)
    public string $content;     // メッセージ内容
    public string $created_at;  // 送信日時
}

class MessageDAO {
    /**
     * チャット履歴の取得
     */
    public function getMessages(int $goodscode, int $my_id, int $partner_id) {
        $dbh = DAO::get_db_connect();
        
        // テーブル名を [Message] と角括弧で囲むことで、予約語エラーを回避します
        $sql = "SELECT * FROM [Message] 
                WHERE goodsCode = :goodsCode 
                AND (
                    (member_id = :my_id1 AND to_member_id = :partner_id1) 
                    OR 
                    (member_id = :partner_id2 AND to_member_id = :my_id2)
                ) 
                ORDER BY created_at ASC";
        
        $stmt = $dbh->prepare($sql);
        
        // SQL Server(ODBC)の特性に合わせ、全てのプレースホルダを個別にバインドします
        $stmt->bindValue(':goodsCode', $goodscode, PDO::PARAM_INT);
        $stmt->bindValue(':my_id1', $my_id, PDO::PARAM_INT);
        $stmt->bindValue(':partner_id1', $partner_id, PDO::PARAM_INT);
        $stmt->bindValue(':partner_id2', $partner_id, PDO::PARAM_INT);
        $stmt->bindValue(':my_id2', $my_id, PDO::PARAM_INT);
        
        $stmt->execute();
        
        $data = [];
        while ($row = $stmt->fetchObject("Message")) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * メッセージの新規登録
     */
    public function insert(int $member_id, int $to_member_id, int $goodscode, string $content) {
        $dbh = DAO::get_db_connect();
        
        // created_at は SQL Server の GETDATE() 関数を使用
        $sql = "INSERT INTO [Message] (member_id, to_member_id, goodsCode, content, created_at) 
                VALUES (:member_id, :to_member_id, :goodsCode, :content, GETDATE())";
        
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        $stmt->bindValue(':to_member_id', $to_member_id, PDO::PARAM_INT);
        $stmt->bindValue(':goodsCode', $goodscode, PDO::PARAM_INT);
        $stmt->bindValue(':content', $content, PDO::PARAM_STR);
        
        return $stmt->execute();
    }
}