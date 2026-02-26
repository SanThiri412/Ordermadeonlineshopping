<?php
require_once __DIR__ . '/DAO.php';

class CartDAO {

    private function getPdo()
    {
        return DAO::get_db_connect();
    }

    public function addToCart($member_id, $goodsCode, $cartCount, $request = null)
    {
        $pdo = $this->getPdo();
        $sql = "SELECT cartCount FROM Cart WHERE member_id = ? AND goodsCode = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$member_id, $goodsCode]);
        $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingItem) {
            $newQuantity = (int)$existingItem['cartCount'] + (int)$cartCount;
            if ($request !== null && $request !== '') {
                $updateSql = "UPDATE Cart SET cartCount = ?, request = ? WHERE member_id = ? AND goodsCode = ?";
                $updateStmt = $pdo->prepare($updateSql);
                return $updateStmt->execute([$newQuantity, $request, $member_id, $goodsCode]);
            } else {
                $updateSql = "UPDATE Cart SET cartCount = ? WHERE member_id = ? AND goodsCode = ?";
                $updateStmt = $pdo->prepare($updateSql);
                return $updateStmt->execute([$newQuantity, $member_id, $goodsCode]);
            }
        } else {
            if ($request !== null && $request !== '') {
                $insertSql = "INSERT INTO Cart (member_id, goodsCode, cartCount, request) VALUES (?, ?, ?, ?)";
                $insertStmt = $pdo->prepare($insertSql);
                return $insertStmt->execute([$member_id, $goodsCode, $cartCount, $request]);
            } else {
                $insertSql = "INSERT INTO Cart (member_id, goodsCode, cartCount) VALUES (?, ?, ?)";
                $insertStmt = $pdo->prepare($insertSql);
                return $insertStmt->execute([$member_id, $goodsCode, $cartCount]);
            }
        }
    }

    public function getCartItems($member_id)
    {
        $pdo = $this->getPdo();

     $sql = "
         SELECT c.goodsCode            AS goodsCode,
             g.goodsName            AS goodsName,
             g.price                AS price,
             g.goods_image          AS image,
             g.stock                AS stock,
             c.cartCount            AS quantity,
             c.cartCount            AS cartCount,
             (g.price * c.cartCount) AS subtotal
         FROM Cart c
         JOIN Goods g ON c.goodsCode = g.goodsCode
         WHERE c.member_id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$member_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 画像パスの正規化（バックスラッシュ→スラッシュ、/htdocs/切り出し、images/補完、URLエンコード）
        foreach ($items as &$item) {
            $raw = isset($item['image']) ? trim((string)$item['image']) : '';

            if ($raw === '') {
                $item['image'] = '/images/no_image.png';
                $item['id'] = $item['goodsCode'];
                continue;
            }

            // バックスラッシュをスラッシュに変換（Windowsパス対策）
            $p = str_replace(['\\\\', '\\'], '/', $raw);

            // 完全URL(http/https)はそのまま利用
            if (preg_match('#^https?://#i', $p)) {
                $item['image'] = $p;
                $item['id'] = $item['goodsCode'];
                continue;
            }

            // /htdocs/ より前を切り落として Web ルートからの相対に揃える
            $pos = stripos($p, '/htdocs/');
            if ($pos !== false) {
                $p = substr($p, $pos + strlen('/htdocs/')); // 例: images/...
            }

            // 先頭の ./ や / を取り除く（判定を簡単にするため）
            $p = ltrim($p, "./");

            // 既に images/ または uploads/ で始まらなければ images/ を補完
            if (!preg_match('#^(images|uploads)/#i', $p)) {
                $p = 'images/' . $p;
            }

            // 重複 images/ を 1 個に正規化
            $p = preg_replace('#^(images/)+#i', 'images/', $p);

            // URL用に各セグメントをエンコード（日本語・スペース・記号対策）
            $segments = explode('/', $p);
            $segments = array_map('rawurlencode', $segments);
            $url = '/' . implode('/', $segments);

            $item['image'] = $url;
            $item['goodsimage'] = $raw; // 元の値も保持
            $item['id'] = $item['goodsCode'];
        }

        return $items;
    }

    public function updateQuantity($member_id, $goodsCode, $cartCount)
    {
        $pdo = $this->getPdo();
        $sql = "UPDATE Cart SET cartCount = ? WHERE member_id = ? AND goodsCode = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$cartCount, $member_id, $goodsCode]);
    }

    public function removeFromCart($member_id, $goodsCode)
    {
        $pdo = $this->getPdo();
        $sql = "DELETE FROM Cart WHERE member_id = ? AND goodsCode = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$member_id, $goodsCode]);
    }

    public function clearCart($member_id)
    {
        $pdo = $this->getPdo();
        $sql = "DELETE FROM Cart WHERE member_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$member_id]);
    }

}
