<?php
require_once __DIR__ . '/DAO.php';

class SearchDAO
{
	// GoodsDAOから移植: 検索条件のWHERE句を作成
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

	// GoodsDAOから移植: 検索件数カウント
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

	// GoodsDAOから移植: アーティスト名で検索
	public function searchByArtists(array $artistNames, int $limit = 8, int $offset = 0): array
	{
		if (empty($artistNames)) return [];

		$dbh = DAO::get_db_connect();
		// getOrderByExprはGoodsDAOからコピペするか、共通化してください
		$orderBy = "goodsName";

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

	// GoodsDAOから移植: アーティスト名で検索した件数
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

	// GoodsDAOから移植: 検索（カテゴリ・ジャンル・色・キーワード・並び順）
	public function search(array $filters = []): array
	{
		$dbh = DAO::get_db_connect();
		$params = [];
		$where = " WHERE sellFlag = 1 AND stock >= 0 ";

		// 1. 分類 (文字列キーから数値IDへの変換)
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
			$where .= " AND (goodsName LIKE :kw_name OR goodsText LIKE :kw_text)";
			$params[':kw_name'] = '%' . $filters['keyword'] . '%';
			$params[':kw_text'] = '%' . $filters['keyword'] . '%';
		}

		// 並び順
		$order = "ORDER BY recommend DESC, goodsCode DESC";
		if (!empty($filters['sort'])) {
			if ($filters['sort'] === 'price_asc') {
				$order = "ORDER BY price ASC";
			} elseif ($filters['sort'] === 'price_desc') {
				$order = "ORDER BY price DESC";
			}
		}

		$sql = "SELECT goodsCode, goodsName, price, goods_image, recommend FROM Goods $where $order";
		$stmt = $dbh->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
