<?php
require_once __DIR__ . '/DAO.php';

class ShuppinDAO
{
    // 出品一覧取得
    public function get_by_member_id($member_id) {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM Goods WHERE member_id = :member_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ); // ←ここ
    }

    //出品登録
     public function insert(array $data): int {
        $dbh = DAO::get_db_connect();
        
        // IDENTITY値を現在の最大値に合わせる
        $sql_check = "SELECT ISNULL(MAX(goodsCode), 0) AS max_code FROM Goods";
        $stmt_check = $dbh->query($sql_check);
        $max_code = (int)$stmt_check->fetchColumn();
        $stmt_check->closeCursor();
        
        if ($max_code > 0) {
            $sql_reseed = "DBCC CHECKIDENT ('Goods', RESEED, $max_code)";
            $dbh->exec($sql_reseed);
        }
        
        $sql = "INSERT INTO Goods
                (goodsName, price, goods_image, genre, color, goodsText, bunrui_id1, bunrui_id2, stock, member_id, recommend, size, sellFlag)
                VALUES
                (:goodsName, :price, :goods_image, :genre, :color, :goodsText, :bunrui_id1, :bunrui_id2, :stock, :member_id, :recommend, :size, 1)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':goodsName', (string)$data['goodsName'], \PDO::PARAM_STR);
        $stmt->bindValue(':price', (int)$data['price'], \PDO::PARAM_INT);
        $stmt->bindValue(':goods_image', (string)($data['goods_image'] ?? ''), \PDO::PARAM_STR);
        $stmt->bindValue(':genre', (string)($data['genre'] ?? ''), \PDO::PARAM_STR);
        $stmt->bindValue(':color', (string)($data['color'] ?? ''), \PDO::PARAM_STR);
        $stmt->bindValue(':goodsText', (string)($data['goodsText'] ?? ''), \PDO::PARAM_STR);
        $stmt->bindValue(':bunrui_id1', (int)($data['bunrui_id1'] ?? 0), \PDO::PARAM_INT);
        
        if (isset($data['bunrui_id2']) && $data['bunrui_id2'] !== null) {
            $stmt->bindValue(':bunrui_id2', (int)$data['bunrui_id2'], \PDO::PARAM_INT);
        } else {
            $stmt->bindValue(':bunrui_id2', null, \PDO::PARAM_NULL);
        }
        
        $stmt->bindValue(':stock', (int)$data['stock'], \PDO::PARAM_INT);
        $stmt->bindValue(':member_id', (int)$data['member_id'], \PDO::PARAM_INT);
        $stmt->bindValue(':recommend', (int)($data['recommend'] ?? 0), \PDO::PARAM_INT);
        
        if (isset($data['size']) && $data['size'] !== null && $data['size'] !== '') {
            $stmt->bindValue(':size', (string)$data['size'], \PDO::PARAM_STR);
        } else {
            $stmt->bindValue(':size', null, \PDO::PARAM_NULL);
        }
        
        if (!$stmt->execute()) {
            throw new \Exception('データベースエラー: ' . implode(' | ', $stmt->errorInfo()));
        }
        
        // 挿入されたIDを取得
        $sql_id = "SELECT IDENT_CURRENT('Goods') AS newId";
        $stmt_id = $dbh->query($sql_id);
        $newId = (int)$stmt_id->fetchColumn();
        
        if ($newId <= 0) {
            throw new \Exception('採番IDの取得に失敗しました');
        }
        
        return $newId;
    }


    // 出品削除
    public function safeDeleteByGoodsCode($goodsCode, $memberId) {
        $dbh = DAO::get_db_connect();
        $sql_check = "SELECT member_id FROM Goods WHERE goodsCode = :code";
        $stmt_check = $dbh->prepare($sql_check);
        $stmt_check->bindValue(':code', $goodsCode, PDO::PARAM_STR);
        $stmt_check->execute();
        $goods = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if (!$goods || $goods['member_id'] != $memberId) {
            return false;
        }

        $sql_delete = "UPDATE Goods SET sellFlag = 0, updated_at = GETDATE() WHERE goodsCode = :code";
        $stmt_delete = $dbh->prepare($sql_delete);
        $stmt_delete->bindValue(':code', $goodsCode, PDO::PARAM_STR);

        return $stmt_delete->execute();
    }

    // 出品情報更新
    public function updateForOwner(int $goodsCode, int $member_id, array $data): bool {
        $dbh = DAO::get_db_connect();
        $fields = [];
        $params = [':code' => $goodsCode, ':member_id' => $member_id];
        if (isset($data['price'])) {
            $fields[] = "price = :price";
            $params[':price'] = (int)$data['price'];
        }
        if (isset($data['goodsText'])) {
            $fields[] = "goodsText = :goodsText";
            $params[':goodsText'] = (string)$data['goodsText'];
        }
        if (isset($data['stock'])) {
            $fields[] = "stock = :stock";
            $params[':stock'] = (int)$data['stock'];
        }
        if (isset($data['recommend'])) {
            $fields[] = "recommend = :recommend";
            $params[':recommend'] = (int)$data['recommend'];
        }
        if (array_key_exists('size', $data)) {
            $fields[] = "size = :size";
            $params[':size'] = ($data['size'] !== null && $data['size'] !== '') ? (string)$data['size'] : null;
        }
        if (isset($data['goodsName'])) {
            $fields[] = "goodsName = :goodsName";
            $params[':goodsName'] = (string)$data['goodsName'];
        }
        if (isset($data['goods_image'])) {
            $fields[] = "goods_image = :goods_image";
            $params[':goods_image'] = (string)$data['goods_image'];
        }
        if (isset($data['sellFlag'])) {
            $fields[] = "sellFlag = :sellFlag";
            $params[':sellFlag'] = (int)$data['sellFlag'];
        }
        if (isset($data['color'])) {
            $fields[] = "color = :color";
            $params[':color'] = (string)$data['color'];
        }
        if (empty($fields)) {
            return true;
        }
        $sql = "UPDATE Goods SET " . implode(', ', $fields) . " WHERE goodsCode = :code AND member_id = :member_id";
        $stmt = $dbh->prepare($sql);
        foreach ($params as $key => $value) {
            if ($value === null) {
                $stmt->bindValue($key, null, \PDO::PARAM_NULL);
            } elseif (is_int($value)) {
                $stmt->bindValue($key, $value, \PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value, \PDO::PARAM_STR);
            }
        }
        return $stmt->execute();
    }

    public function delete(int $goodsCode): bool {
        $dbh = DAO::get_db_connect();
        $sql = "DELETE FROM Goods WHERE goodsCode = :goodsCode";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':goodsCode', $goodsCode, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function findOneForOwner($goodsCode, $member_id) {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM Goods WHERE goodsCode = :goodsCode AND member_id = :member_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':goodsCode', $goodsCode, PDO::PARAM_INT);
        $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}