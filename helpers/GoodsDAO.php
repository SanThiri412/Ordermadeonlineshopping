<?php
require_once __DIR__ . '/DAO.php';

/**
 * 商品エンティティ
 */
class Goods
{
    public ?string $goodsCode = null;
    public string $goodsName;
    public ?int $price = null;
    public ?string $goods_image = null;
    public ?string $genre = null;
    public ?string $color = null;
    public ?string $goodsText = null;
    public ?int $bunrui_id1 = null;
    public ?int $bunrui_id2 = null;
    public ?int $stock = null;
    public ?int $member_id = null;
    public ?int $recommend = null;
    public ?string $size = null;
    public ?int $sellFlag = null;
}

class GoodsDAO
{
    /**
     * 商品画像ファイル名リストを返す（DB値を直接返す）
     */
    public function getImages($goodsCode): array
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT goods_image FROM Goods WHERE goodsCode = :code";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':code', $goodsCode, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row || empty($row['goods_image'])) return [];
        
        $raw = trim($row['goods_image']);
        if ($raw === '') return [];
        
        if (strpos($raw, ',') !== false) {
            $images = array_map('trim', explode(',', $raw));
        } else {
            $images = [$raw];
        }
        
        return array_map(function($img) { return trim($img, '/\\'); }, $images);
    }
    private function getOrderByExpr(): string
    {
        return "
            CASE genre
              WHEN 'シルバー' THEN 1
              WHEN 'ゴールド' THEN 2
              WHEN '宝石' THEN 3
              WHEN '月' THEN 4
              WHEN '金属' THEN 5
              WHEN '粘土' THEN 6
              WHEN '天然石' THEN 7
              WHEN 'レジン' THEN 8
              WHEN '木製' THEN 9
              WHEN 'ターコイズ' THEN 10
              WHEN 'ウッド/レジン' THEN 11
              WHEN 'プラスチック' THEN 12
              WHEN 'カボション' THEN 13
              WHEN 'マーブル' THEN 14
              WHEN 'レジンアート' THEN 15
              WHEN 'べっ甲' THEN 16
              WHEN '銀製' THEN 17
              ELSE 99
            END,
            goodsName
        ";
    }
    public function findByCode($goodsCode) {
        $dbh = DAO::get_db_connect();
        $sql = "
            SELECT g.*, m.name AS artist_name, m.nickName AS artist_nickname
            FROM Goods g
            LEFT JOIN Member m ON g.member_id = m.member_id
            WHERE g.goodsCode = :goodsCode
        ";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':goodsCode', $goodsCode, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getStock($goodsCode): int {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT stock FROM Goods WHERE goodsCode = :goodsCode";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':goodsCode', $goodsCode, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return isset($row['stock']) ? (int)$row['stock'] : 0;
    }
    private function buildSearchWhere(array $filters, array &$params): string
    {
        $whereClauses = ["sellFlag = 1", "stock >= 0"];

        if (isset($filters['bunrui_id']) && is_int($filters['bunrui_id'])) {
            $whereClauses[] = "bunrui_id1 = :bunrui_id";
            $params[':bunrui_id'] = $filters['bunrui_id'];
        }

        if (isset($filters['keyword']) && is_string($filters['keyword']) && $filters['keyword'] !== '') {
            $whereClauses[] = "goodsName LIKE :keyword";
            $params[':keyword'] = '%' . $filters['keyword'] . '%';
        }

        if (empty($whereClauses)) {
            return '';
        }

        return 'WHERE ' . implode(' AND ', $whereClauses);
    }

    public function countSearch(array $filters = []): int
    {
        $dbh = DAO::get_db_connect();
        $params = [];
        $where = $this->buildSearchWhere($filters, $params);

        $sql = "SELECT COUNT(*) AS cnt FROM Goods {$where}";
        $stmt = $dbh->prepare($sql);
        foreach ($params as $key => $value) {
            if ($key === ':bunrui_id') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['cnt'] ?? 0);
    }

    public function searchByArtists(array $artistNames, int $limit = 8, int $offset = 0): array
    {
        if (empty($artistNames)) return [];

        $dbh = DAO::get_db_connect();
        $orderBy = $this->getOrderByExpr();

        $params = [];
        $uniq = array_values(array_unique($artistNames));

        $namePlaceholders = [];
        $nickPlaceholders = [];
        foreach ($uniq as $i => $name) {
            $phName = ":name{$i}";
            $phNick = ":nick{$i}";
            $namePlaceholders[] = $phName;
            $nickPlaceholders[] = $phNick;
            $params[$phName] = $name;
            $params[$phNick] = $name;
        }

        $from = $offset + 1;
        $to = $offset + $limit;

        $sql = "
            WITH GoodsCTE AS (
                SELECT
                    g.goodsCode,
                    g.goodsName,
                    g.price,
                    g.goods_image,
                    g.genre,
                    g.color,
                    g.goodsText,
                    ROW_NUMBER() OVER (ORDER BY {$orderBy}) AS rn
                FROM Goods g
                INNER JOIN member m ON g.member_id = m.member_id
                WHERE (m.name IN (" . implode(', ', $namePlaceholders) . ")
                       OR m.nickName IN (" . implode(', ', $nickPlaceholders) . "))
                       AND g.stock >= 0
                       AND g.sellFlag = 1
            )
            SELECT goodsCode, goodsName, price, goods_image, genre, color, goodsText
            FROM GoodsCTE
            WHERE rn BETWEEN :from AND :to
            ORDER BY rn
        ";

        $stmt = $dbh->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':from', $from, PDO::PARAM_INT);
        $stmt->bindValue(':to', $to, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countByArtists(array $artistNames): int
    {
        if (empty($artistNames)) return 0;

        $dbh = DAO::get_db_connect();

        $params = [];
        $uniq = array_values(array_unique($artistNames));

        $namePlaceholders = [];
        $nickPlaceholders = [];
        foreach ($uniq as $i => $name) {
            $phName = ":name{$i}";
            $phNick = ":nick{$i}";
            $namePlaceholders[] = $phName;
            $nickPlaceholders[] = $phNick;
            $params[$phName] = $name;
            $params[$phNick] = $name;
        }

        $sql = "
            SELECT COUNT(*) AS cnt
            FROM Goods g
            INNER JOIN member m ON g.member_id = m.member_id
            WHERE (m.name IN (" . implode(', ', $namePlaceholders) . ")
                   OR m.nickName IN (" . implode(', ', $nickPlaceholders) . "))
                   AND g.sellFlag = 1
        ";

        $stmt = $dbh->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)($row['cnt'] ?? 0);
    }

    public function get_recommend_goods_by_member_id(int $member_id)
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM Goods WHERE member_id = :member_id AND recommend = 1 ORDER BY goodsCode DESC";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        $stmt->execute();

        $data = [];
        while ($row = $stmt->fetchObject('Goods')) {
            $data[] = $row;
        }
        return $data;
    }

    public function get_goods_by_groupcode(int $groupcode)
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM Goods WHERE groupcode = :groupcode ORDER BY recommend DESC";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':groupcode', $groupcode, PDO::PARAM_INT);
        $stmt->execute();
        $data = [];
        while ($row = $stmt->fetchObject('Goods')) {
            $data[] = $row;
        }
        return $data;
    }

    public function get_goods_by_goodscode(string $goodscode)
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM Goods WHERE goodsCode = :goodscode";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':goodscode', $goodscode, PDO::PARAM_STR);
        $stmt->execute();
        $goods = $stmt->fetchObject('Goods');
        return $goods;
    }

    public function get_goods_by_keyword(string $keyword)
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM Goods WHERE goodsName LIKE :keyword1 OR goodsText LIKE :keyword2 ORDER BY recommend DESC";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':keyword1', '%' . $keyword . '%', PDO::PARAM_STR);
        $stmt->bindValue(':keyword2', '%' . $keyword . '%', PDO::PARAM_STR);
        $stmt->execute();

        $data = [];
        while ($row = $stmt->fetchObject('Goods')) {
            $data[] = $row;
        }
        return $data;
    }

    public function get_goods_by_member_id(int $member_id, bool $includeDeleted = false): array {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT goodsCode, goodsName, price, goods_image, stock, sellFlag, 
                       goodsText, size, color, genre, recommend, bunrui_id1, bunrui_id2
                FROM Goods
                WHERE member_id = :member_id";
        if (!$includeDeleted) {
            $sql .= " AND sellFlag = 1";
        }
        $sql .= " ORDER BY goodsCode DESC";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':member_id', $member_id, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $list = [];
        foreach ($rows as $r) {
            $g = new Goods();
            $g->goodsCode = (string)$r['goodsCode'];
            $g->goodsName = $r['goodsName'];
            $g->price = (int)($r['price'] ?? 0);
            $g->goods_image = $r['goods_image'] ?? null;
            $g->stock = (int)($r['stock'] ?? 0);
            $g->sellFlag = (int)($r['sellFlag'] ?? 0);
            $g->goodsText = $r['goodsText'] ?? null;
            $g->size = $r['size'] ?? null;
            $g->color = $r['color'] ?? null;
            $g->genre = $r['genre'] ?? null;
            $g->recommend = (int)($r['recommend'] ?? 0);
            $g->bunrui_id1 = isset($r['bunrui_id1']) ? (int)$r['bunrui_id1'] : null;
            $g->bunrui_id2 = isset($r['bunrui_id2']) ? (int)$r['bunrui_id2'] : null;
            $list[] = $g;
        }
        return $list;
    }

    public function get_all_goods(bool $includeDeleted = true): array
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM Goods";
        if (!$includeDeleted) {
            $sql .= " WHERE sellFlag = 1";
        }
        $sql .= " ORDER BY recommend DESC, goodsCode DESC";

        $stmt = $dbh->prepare($sql);
        $stmt->execute();

        $data = [];
        while ($row = $stmt->fetchObject('Goods')) {
            $data[] = $row;
        }
        return $data;
    }
   public function search(array $filters = []): array
{
    $dbh = DAO::get_db_connect();
    $params = [];
    $where = " WHERE sellFlag = 1 AND stock >= 0 ";

    // 1. 分類 (文字列キーから数値IDへの変換) - ここを画面の定義と完全に一致させます
    $bunrui_map = [
        'necklace'   => 1,
        'ring'       => 2,
        'pierce'     => 3,
        'earring'    => 4,
        'armlet'     => 5,
        'bangle'     => 6,
        'bracelet'   => 7,
        'amulet'     => 8,
        'kanzashi'   => 9,
        'barrette'   => 10,
        'brooch'     => 11,
        'necktiepin' => 12,
        'others'     => 13
    ];

    if (!empty($filters['category']) && isset($bunrui_map[$filters['category']])) {
        $where .= " AND bunrui_id1 = :category_id";
        $params[':category_id'] = $bunrui_map[$filters['category']];
    }

    // 2. ジャンル (素材) 
    if (!empty($filters['genre']) && is_array($filters['genre'])) {
        $genreList = array_filter($filters['genre'], function($g) {
            return $g !== '' && $g !== 'すべてを選択' && $g !== 'all';
        });
        if (!empty($genreList)) {
            $genreWheres = [];
            foreach ($genreList as $i => $g) {
                $ph = ":genre{$i}";
                // カンマ区切りや完全一致も考慮してLIKE検索
                $genreWheres[] = "genre LIKE $ph";
                $params[$ph] = "%$g%";
            }
            $where .= " AND (" . implode(' OR ', $genreWheres) . ")";
        }
    }

    // 3. 色
    if (!empty($filters['color_exact'])) {
        $where .= " AND color = '1'"; 
    } elseif (!empty($filters['color'])) {
        $where .= " AND color = :color";
        $params[':color'] = $filters['color'];
    }

    // 4. キーワード検索
    if (!empty($filters['keyword'])) {
        $where .= " AND (goodsName LIKE :kw_name OR goodsText LIKE :kw_text OR color LIKE :kw_color)";
        $params[':kw_name'] = '%' . $filters['keyword'] . '%';
        $params[':kw_text'] = '%' . $filters['keyword'] . '%';
        $params[':kw_color'] = '%' . $filters['keyword'] . '%';
    }

    // 並び順
    $order = "ORDER BY recommend DESC, goodsCode DESC"; // おすすめ順
    if (!empty($filters['sort'])) {
        if ($filters['sort'] === 'price_asc') {
            $order = "ORDER BY price ASC";
        } elseif ($filters['sort'] === 'price_desc') {
            $order = "ORDER BY price DESC";
        }
    }

    // 必ずrecommendをSELECT
    $sql = "SELECT goodsCode, goodsName, price, goods_image, recommend FROM Goods $where $order";
    $stmt = $dbh->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function get_goods_by_member_id_and_sellFlag($member_id, $sellFlag) {
    $dbh = DAO::get_db_connect();
    $sql = "SELECT * FROM Goods WHERE member_id = :member_id AND sellFlag = :sellFlag ORDER BY goodsCode DESC";
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
    $stmt->bindValue(':sellFlag', $sellFlag, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}
}
